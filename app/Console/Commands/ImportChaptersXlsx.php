<?php

namespace App\Console\Commands;

use App\Models\Chapter;
use App\Models\Course;
use App\Support\XlsxWorkbook;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportChaptersXlsx extends Command
{
    protected $signature = 'linkedin:import-chapters
        {file : Path file .xlsx yang berisi data chapter}
        {--sheet=Sheet1 : Nama sheet untuk dibaca}
        {--replace : Sinkronkan data (hapus chapter DB yang tidak ada di file untuk course yang ada di file)}
        {--dry-run : Hanya baca dan tampilkan ringkasan tanpa menyimpan}';

    protected $description = 'Import/update tabel chapters dari workbook XLSX khusus chapter';

    public function handle(): int
    {
        $file = (string) $this->argument('file');
        $sheet = (string) $this->option('sheet');
        $replace = (bool) $this->option('replace');
        $dryRun = (bool) $this->option('dry-run');

        if (! is_file($file)) {
            $this->error("File tidak ditemukan: {$file}");
            return self::FAILURE;
        }

        $workbook = new XlsxWorkbook($file);
        $courseSet = Course::query()
            ->pluck('id')
            ->flip()
            ->all();

        $total = 0;
        $upserted = 0;
        $deleted = 0;
        $skippedMissingCourse = 0;
        $skippedEmpty = 0;
        $chapterIdsByCourse = [];

        $runner = function () use ($workbook, $sheet, $courseSet, &$total, &$upserted, &$deleted, &$skippedMissingCourse, &$skippedEmpty, &$chapterIdsByCourse, $dryRun, $replace): void {
            foreach ($workbook->rows($sheet) as $row) {
                $total++;

                $id = trim((string) ($row['chapter_id'] ?? ''));
                $courseId = trim((string) ($row['course_id'] ?? ''));
                if ($id === '' || $courseId === '') {
                    $skippedEmpty++;
                    continue;
                }

                if (! isset($courseSet[$courseId])) {
                    $skippedMissingCourse++;
                    continue;
                }

                $chapterIdsByCourse[$courseId][$id] = true;

                if (! $dryRun) {
                    Chapter::updateOrCreate(
                        ['id' => $id],
                        [
                            'course_id' => $courseId,
                            'judul' => trim((string) ($row['judul'] ?? '')),
                            'urutan' => (int) (($row['urutan'] ?? 0) ?: 0),
                            'jumlah_video' => (int) (($row['jumlah_video'] ?? 0) ?: 0),
                        ]
                    );
                }

                $upserted++;
            }

            if (! $replace) {
                return;
            }

            foreach ($chapterIdsByCourse as $courseId => $chapterIdSet) {
                $keepIds = array_keys($chapterIdSet);
                $existingIds = Chapter::query()
                    ->where('course_id', $courseId)
                    ->pluck('id')
                    ->all();
                $deleteIds = array_values(array_diff($existingIds, $keepIds));

                foreach (array_chunk($deleteIds, 500) as $chunk) {
                    if ($chunk === []) {
                        continue;
                    }

                    if (! $dryRun) {
                        $deleted += Chapter::query()
                            ->where('course_id', $courseId)
                            ->whereIn('id', $chunk)
                            ->delete();
                    } else {
                        $deleted += count($chunk);
                    }
                }
            }
        };

        if ($dryRun) {
            $runner();
        } else {
            DB::transaction($runner);
        }

        $this->info("Selesai membaca: {$total} baris");
        $this->line("Diinsert/diupdate: {$upserted}");
        $this->line("Dihapus (replace): {$deleted}");
        $this->line("Diskip (course_id tidak ada): {$skippedMissingCourse}");
        $this->line("Diskip (kolom wajib kosong): {$skippedEmpty}");

        if ($dryRun) {
            $this->info('Dry-run selesai. Tidak ada data yang disimpan.');
        }

        return self::SUCCESS;
    }
}
