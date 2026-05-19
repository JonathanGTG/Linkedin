<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Instructor;
use App\Models\Skill;
use App\Models\Video;
use App\Support\XlsxWorkbook;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportLinkedinScrape extends Command
{
    protected $signature = 'linkedin:import-scrape
        {file : Path file .xlsx hasil scraping}
        {--dry-run : Hanya baca dan tampilkan ringkasan tanpa menyimpan}
        {--fresh-catalog : Kosongkan katalog course lama sebelum import}
        {--limit= : Batasi jumlah course untuk uji coba}';

    protected $description = 'Import data katalog learning dari workbook scraping ke database aplikasi';

    private XlsxWorkbook $workbook;

    public function handle(): int
    {
        $file = (string) $this->argument('file');
        $limit = $this->option('limit') !== null ? max(1, (int) $this->option('limit')) : null;
        $dryRun = (bool) $this->option('dry-run');

        if (! is_file($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        $this->workbook = new XlsxWorkbook($file);
        $summary = $this->inspectWorkbook($limit);

        $this->table(['Sheet', 'Rows'], collect($summary['counts'])->map(fn ($count, $sheet) => [$sheet, $count])->all());
        $this->line('Level ditemukan: '.collect($summary['levels'])->map(fn ($count, $level) => "{$level}: {$count}")->implode(', '));
        $this->line("Course slug duplikat: {$summary['duplicate_course_slugs']}");
        $this->line("Course dengan topic hilang: {$summary['missing_course_topics']}");
        $this->line("Relasi skill hilang: {$summary['missing_skill_relations']}");
        $this->line("Video dengan chapter hilang: {$summary['missing_video_chapters']}");

        if ($dryRun) {
            $this->info('Dry-run selesai. Tidak ada data yang disimpan.');
            return self::SUCCESS;
        }

        if ($this->option('fresh-catalog')) {
            $this->clearCatalog();
        }

        $topicMap = $this->importTopics();
        $courseMap = $this->importCourses($topicMap, $limit);
        $skillMap = $this->importSkills();
        $instructorMap = $this->importInstructors();
        $chapterMap = $this->importChapters($courseMap);

        $this->importCourseSkills($courseMap, $skillMap);
        $this->importCourseIncludes($courseMap);
        $this->importCourseInstructors($courseMap, $instructorMap);
        $this->importVideos($courseMap, $chapterMap);

        $this->info('Import selesai.');
        return self::SUCCESS;
    }

    private function inspectWorkbook(?int $limit): array
    {
        $counts = [];
        foreach ([
            '01_topics',
            '02_courses',
            '03_skills',
            '04_course_skill',
            '05_includes',
            '06_instructors',
            '07_course_instructor',
            '08_chapters',
            '09_videos',
        ] as $sheet) {
            $counts[$sheet] = $this->workbook->countRows($sheet);
        }

        $topicIds = [];
        foreach ($this->workbook->rows('01_topics') as $row) {
            $topicIds[(string) $row['topic_id']] = true;
        }

        $skillIds = [];
        $skillNames = [];
        foreach ($this->workbook->rows('03_skills') as $row) {
            $skillIds[(string) $row['skill_id']] = true;
            $skillNames[$this->rowString($row, 'nama', 1)] = true;
        }

        $chapterIds = [];
        foreach ($this->workbook->rows('08_chapters') as $row) {
            $chapterIds[(string) $row['chapter_id']] = true;
        }

        $courseIds = [];
        $courseSlugs = [];
        $levels = [];
        $missingCourseTopics = 0;
        $courseCount = 0;
        foreach ($this->workbook->rows('02_courses') as $row) {
            if ($limit !== null && $courseCount >= $limit) {
                break;
            }

            $courseCount++;
            $courseIds[(string) $row['course_id']] = true;
            $courseSlugs[(string) $row['slug']] = ($courseSlugs[(string) $row['slug']] ?? 0) + 1;
            $levels[(string) $row['level']] = ($levels[(string) $row['level']] ?? 0) + 1;

            if (! isset($topicIds[(string) $row['topic_id']])) {
                $missingCourseTopics++;
            }
        }

        $missingSkillRelations = 0;
        foreach ($this->workbook->rows('04_course_skill') as $row) {
            if (! isset($courseIds[(string) $row['course_id']])) {
                continue;
            }
            if (! isset($skillIds[(string) $row['skill_id']]) && ! isset($skillNames[trim((string) $row['skill_nama'])])) {
                $missingSkillRelations++;
            }
        }

        $missingVideoChapters = 0;
        foreach ($this->workbook->rows('09_videos') as $row) {
            if (! isset($courseIds[(string) $row['course_id']])) {
                continue;
            }
            if (! isset($chapterIds[(string) $row['chapter_id']])) {
                $missingVideoChapters++;
            }
        }

        return [
            'counts' => $counts,
            'levels' => $levels,
            'duplicate_course_slugs' => collect($courseSlugs)->filter(fn ($count) => $count > 1)->count(),
            'missing_course_topics' => $missingCourseTopics,
            'missing_skill_relations' => $missingSkillRelations,
            'missing_video_chapters' => $missingVideoChapters,
        ];
    }

    private function clearCatalog(): void
    {
        DB::table('course_includes')->delete();
        DB::table('course_instructor')->delete();
        DB::table('course_skill')->delete();
        Video::query()->delete();
        Chapter::query()->delete();
        Course::query()->delete();
        Skill::query()->delete();
        Instructor::query()->delete();
        Category::query()->delete();
    }

    private function importTopics(): array
    {
        $map = [];
        foreach ($this->workbook->rows('01_topics') as $row) {
            $id = trim((string) $row['topic_id']);
            $name = trim((string) $row['nama']);
            $category = Category::find($id);
            $slugCandidate = $this->nullableString($row['slug']) ?? Str::slug($name);
            $slug = $this->uniqueSlug($slugCandidate, Category::class, 'slug', $id);

            $category = $this->saveModel($category ?? new Category(), [
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
            ]);

            $map[$id] = $category->id;
        }

        return $map;
    }

    private function importCourses(array &$topicMap, ?int $limit): array
    {
        $map = [];
        $bar = $this->output->createProgressBar($limit ?? $this->workbook->countRows('02_courses'));
        $bar->start();

        $count = 0;
        foreach ($this->workbook->rows('02_courses') as $row) {
            if ($limit !== null && $count >= $limit) {
                break;
            }
            $count++;

            $id = trim((string) $row['course_id']);
            $topicId = trim((string) $row['topic_id']);

            if (! isset($topicMap[$topicId])) {
                $topicMap[$topicId] = Category::updateOrCreate(
                    ['id' => $topicId],
                    [
                        'name' => "Unknown Topic {$topicId}",
                        'slug' => $this->uniqueSlug("unknown-topic-{$topicId}", Category::class),
                    ]
                )->id;
            }

            $slugCandidate = $this->nullableString($row['slug']) ?? Str::slug((string) $row['judul']);
            $slug = $this->uniqueSlug($slugCandidate, Course::class, 'slug', $id);

            $course = Course::updateOrCreate(
                ['id' => $id],
                [
                    'category_id' => $topicMap[$topicId],
                    'title' => trim((string) $row['judul']),
                    'slug' => $slug,
                    'description' => $this->nullableString($row['deskripsi']),
                    'instructor_name' => 'LinkedIn Learning',
                    'level' => $this->normalizeLevel($row['level']),
                    'scraped_level' => $this->nullableString($row['level']),
                    'durasi' => $this->nullableString($row['durasi']),
                    'release_date' => $this->normalizeDate($row['tanggal_rilis']),
                    'durasi_detik' => (int) ($row['durasi_detik'] ?: 0),
                    'jumlah_learner' => (int) ($row['jumlah_learner'] ?: 0),
                    'rating' => (float) ($row['rating'] ?: 0),
                    'rating_count' => $row['jumlah_rating'] !== null && $row['jumlah_rating'] !== '' ? (int) $row['jumlah_rating'] : null,
                    'source_url' => $this->nullableString($row['url']),
                    'is_published' => true,
                ]
            );

            $map[$id] = $course->id;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $map;
    }

    private function importSkills(): array
    {
        $map = [];
        foreach ($this->workbook->rows('03_skills') as $row) {
            $name = $this->rowString($row, 'nama', 1);
            if ($name === '') {
                continue;
            }

            $rawId = trim((string) $row['skill_id']);
            $id = $this->normalizeNamedId($rawId, 'SKL', $name);
            $existing = Skill::find($id);
            $slugCandidate = Str::slug($name);
            $slug = $this->uniqueSlug($slugCandidate, Skill::class, 'slug', $id);

            $skill = $this->saveModel($existing ?? new Skill(), [
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
            ]);

            $map[$rawId] ??= $skill->id;
            $map[$this->relationKey($rawId, $name)] = $skill->id;
        }

        return $map;
    }

    private function rowValue(array $row, string $key, ?int $fallbackIndex = null): mixed
    {
        if (array_key_exists($key, $row)) {
            return $row[$key];
        }

        if ($fallbackIndex === null) {
            return null;
        }

        $values = array_values($row);
        return $values[$fallbackIndex] ?? null;
    }

    private function rowString(array $row, string $key, ?int $fallbackIndex = null): string
    {
        return trim((string) $this->rowValue($row, $key, $fallbackIndex));
    }

    private function importInstructors(): array
    {
        $map = [];
        foreach ($this->workbook->rows('06_instructors') as $row) {
            $name = trim((string) $row['nama']);
            if ($name === '') {
                continue;
            }

            $rawId = trim((string) $row['instructor_id']);
            $id = $this->normalizeNamedId($rawId, 'INS', $name);
            $existing = Instructor::find($id);
            $slug = Str::slug($name);

            $instructor = $this->saveModel($existing ?? new Instructor(), [
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'info' => $this->nullableString($row['info']),
                'bio' => $this->nullableString($row['bio']),
                'link' => $this->nullableString($row['link']),
            ]);

            $map[$rawId] ??= $instructor->id;
            $map[$this->relationKey($rawId, $name)] = $instructor->id;
        }

        return $map;
    }

    private function importChapters(array $courseMap): array
    {
        $map = [];
        foreach ($this->workbook->rows('08_chapters') as $row) {
            $courseId = $courseMap[(string) $row['course_id']] ?? null;
            if (! $courseId) {
                continue;
            }

            $rawChapterId = trim((string) $row['chapter_id']);
            $id = $this->normalizeScopedId($rawChapterId, 'CPT', (string) $row['course_id']);
            $chapter = Chapter::updateOrCreate(
                ['id' => $id],
                [
                    'course_id' => $courseId,
                    'judul' => trim((string) $row['judul']),
                    'urutan' => (int) ($row['urutan'] ?: 0),
                    'jumlah_video' => (int) ($row['jumlah_video'] ?: 0),
                ]
            );

            $map[$this->relationKey($row['course_id'], $rawChapterId)] = $chapter->id;
        }

        return $map;
    }

    private function importCourseSkills(array $courseMap, array $skillMap): void
    {
        $rows = [];
        $skillNameMap = Skill::query()
            ->orderBy('id')
            ->get(['id', 'name'])
            ->groupBy('name')
            ->map(fn ($items) => $items->pluck('id')->all())
            ->all();

        foreach ($this->workbook->rows('04_course_skill') as $row) {
            $courseId = $courseMap[(string) $row['course_id']] ?? null;
            $skillName = trim((string) $row['skill_nama']);
            $rawSkillId = trim((string) $row['skill_id']);
            $skillId = $skillMap[$this->relationKey($rawSkillId, $skillName)]
                ?? $this->firstAvailableSkillId($skillNameMap[$skillName] ?? [], $courseId, $rows);

            if (! $courseId || ! $skillId) {
                continue;
            }

            $rows["{$courseId}:{$skillId}"] = [
                'course_id' => $courseId,
                'skill_id' => $skillId,
                'skill_nama' => $this->nullableString($skillName),
            ];
        }

        foreach (array_chunk(array_values($rows), 1000) as $chunk) {
            DB::table('course_skill')->upsert($chunk, ['course_id', 'skill_id'], ['skill_nama']);
        }
    }

    private function firstAvailableSkillId(array $skillIds, ?string $courseId, array $pendingRows): ?string
    {
        if ($skillIds === []) {
            return null;
        }

        foreach ($skillIds as $skillId) {
            if ($courseId && ! isset($pendingRows["{$courseId}:{$skillId}"])) {
                return $skillId;
            }
        }

        return $skillIds[0];
    }

    private function importCourseIncludes(array $courseMap): void
    {
        $rows = [];
        $now = now();
        foreach ($this->workbook->rows('05_includes') as $row) {
            $courseId = $courseMap[(string) $row['course_id']] ?? null;
            $item = trim((string) $row['item']);
            if (! $courseId || $item === '') {
                continue;
            }

            $rows["{$courseId}:{$item}"] = [
                'course_id' => $courseId,
                'item' => $item,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk(array_values($rows), 1000) as $chunk) {
            DB::table('course_includes')->upsert($chunk, ['course_id', 'item'], ['updated_at']);
        }
    }

    private function importCourseInstructors(array $courseMap, array $instructorMap): void
    {
        $rows = [];
        $firstInstructorByCourse = [];
        foreach ($this->workbook->rows('07_course_instructor') as $row) {
            $courseId = $courseMap[(string) $row['course_id']] ?? null;
            $rawInstructorId = trim((string) $row['instructor_id']);
            $name = trim((string) $row['nama']);
            $instructorId = $instructorMap[$this->relationKey($rawInstructorId, $name)]
                ?? $instructorMap[$rawInstructorId]
                ?? null;
            if (! $courseId || ! $instructorId) {
                continue;
            }

            $rows["{$courseId}:{$instructorId}"] = [
                'course_id' => $courseId,
                'instructor_id' => $instructorId,
                'nama' => $name,
            ];
            $firstInstructorByCourse[$courseId] ??= $name;
        }

        foreach (array_chunk(array_values($rows), 1000) as $chunk) {
            DB::table('course_instructor')->upsert($chunk, ['course_id', 'instructor_id'], ['nama']);
        }

        foreach ($firstInstructorByCourse as $courseId => $name) {
            Course::whereKey($courseId)->update(['instructor_name' => $name ?: 'LinkedIn Learning']);
        }
    }

    private function importVideos(array $courseMap, array $chapterMap): void
    {
        $bar = $this->output->createProgressBar($this->workbook->countRows('09_videos'));
        $bar->start();
        $now = now();
        $rows = [];

        foreach ($this->workbook->rows('09_videos') as $row) {
            $courseId = $courseMap[(string) $row['course_id']] ?? null;
            if (! $courseId) {
                $bar->advance();
                continue;
            }

            $rawVideoId = trim((string) $row['video_id']);
            $rawChapterId = trim((string) $row['chapter_id']);
            $id = $this->normalizeScopedId($rawVideoId, 'MP4', (string) $row['course_id'], $rawChapterId);
            $title = trim((string) $row['judul']);

            $rows[] = [
                'id' => $id,
                'course_id' => $courseId,
                'chapter_id' => $chapterMap[$this->relationKey($row['course_id'], $rawChapterId)] ?? null,
                'title' => $title,
                'slug' => Str::slug($title).'-'.Str::lower($id),
                'video_url' => 'videos/'.Str::lower($id).'.mp4',
                'durasi' => $this->nullableString($row['durasi']),
                'durasi_detik' => (int) ($row['durasi_detik'] ?: 0),
                'urutan' => (int) ($row['urutan'] ?: 0),
                'is_preview' => ((int) ($row['urutan'] ?: 0)) === 1,
                'tipe' => $this->nullableString($row['tipe']),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($rows) >= 1000) {
                $this->upsertVideos($rows);
                $rows = [];
            }

            $bar->advance();
        }

        if ($rows !== []) {
            $this->upsertVideos($rows);
        }

        $bar->finish();
        $this->newLine();
    }

    private function upsertVideos(array $rows): void
    {
        DB::table('videos')->upsert(
            $rows,
            ['id'],
            [
                'course_id',
                'chapter_id',
                'title',
                'slug',
                'video_url',
                'durasi',
                'durasi_detik',
                'urutan',
                'is_preview',
                'tipe',
                'updated_at',
            ]
        );
    }

    private function normalizeLevel(mixed $level): string
    {
        return match (trim((string) $level)) {
            'Intermediate', 'Beginner + Intermediate' => 'Intermediate',
            'Advanced' => 'Advanced',
            default => 'Beginner',
        };
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return gmdate('Y-m-d', ((int) $value - 25569) * 86400);
        }

        return Str::of((string) $value)->before(' ')->toString();
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);
        return $value === '' ? null : $value;
    }

    private function uniqueSlug(string $value, string $modelClass, string $column = 'slug', string|int|null $ignoreId = null): string
    {
        $base = Str::slug($value);
        $base = $base !== '' ? $base : Str::random(8);
        $slug = $base;
        $counter = 2;

        while ($modelClass::query()
            ->where($column, $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function relationKey(mixed ...$parts): string
    {
        return collect($parts)
            ->map(fn ($part) => trim((string) $part))
            ->implode('|');
    }

    private function normalizeNamedId(string $rawId, string $prefix, string $name): string
    {
        $rawId = trim($rawId);
        if (Str::startsWith($rawId, "{$prefix}-")) {
            return $rawId;
        }

        return "{$prefix}-{$rawId}-".substr(md5($name), 0, 8);
    }

    private function normalizeScopedId(string $rawId, string $prefix, string ...$scope): string
    {
        $rawId = trim($rawId);
        if (Str::startsWith($rawId, "{$prefix}-")) {
            return $rawId;
        }

        $scopePart = collect($scope)
            ->map(fn ($part) => preg_replace('/[^A-Za-z0-9]+/', '-', trim($part)))
            ->filter()
            ->implode('-');

        return "{$prefix}-{$scopePart}-{$rawId}";
    }

    private function saveModel($model, array $attributes)
    {
        $model->fill($attributes);
        $model->save();

        return $model;
    }
}
