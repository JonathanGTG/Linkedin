<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// ── Load XLSX reader from audit script ──────────────────────────────────
require_once __DIR__.'/audit_dbml_xlsx.php';

$xlsxPath = __DIR__.'/linkedin_database_combined_full (1).xlsx';
$wb = new XlsxWorkbookLite($xlsxPath);

function readAllRows(XlsxWorkbookLite $wb, string $sheet): array
{
    $headers = $wb->headers($sheet);
    $rows = [];
    $isFirst = true;
    // Use reflection to access rawRows
    $ref = new ReflectionMethod($wb, 'rawRows');
    $ref->setAccessible(true);
    foreach ($ref->invoke($wb, $sheet) as $row) {
        if ($isFirst) { $isFirst = false; continue; } // skip header
        if ($row === []) continue;
        $assoc = [];
        foreach ($headers as $i => $h) {
            $assoc[(string)$h] = $row[$i] ?? null;
        }
        $rows[] = $assoc;
    }
    return $rows;
}

$now = now()->toDateTimeString();

// ── Disable FK checks for clean import ──────────────────────────────────
DB::statement('SET FOREIGN_KEY_CHECKS=0');

// ═══════════════════════════════════════════════════════════════════════
// 1. TOPICS → categories
// ═══════════════════════════════════════════════════════════════════════
echo "Importing 01_topics → categories...\n";
$rows = readAllRows($wb, '01_topics');
DB::table('categories')->truncate();
DB::statement('ALTER TABLE categories AUTO_INCREMENT = 1');
foreach ($rows as $r) {
    DB::table('categories')->insertOrIgnore([
        'id'   => $r['topic_id'],
        'name' => $r['nama'],
        'slug' => $r['slug'],
        'created_at' => $now, 'updated_at' => $now,
    ]);
}
echo "  ✓ ".count($rows)." topics imported\n";

// ═══════════════════════════════════════════════════════════════════════
// 2. INSTRUCTORS
// ═══════════════════════════════════════════════════════════════════════
echo "Importing 06_instructors → instructors...\n";
$rows = readAllRows($wb, '06_instructors');
DB::table('instructors')->truncate();
foreach ($rows as $r) {
    DB::table('instructors')->insertOrIgnore([
        'id'   => $r['instructor_id'],
        'name' => $r['nama'] ?? '',
        'slug' => Str::slug($r['nama'] ?? 'unknown').'-'.Str::random(4),
        'info' => $r['info'] ?? null,
        'bio'  => $r['bio'] ?? null,
        'link' => $r['link'] ?? null,
        'created_at' => $now, 'updated_at' => $now,
    ]);
}
echo "  ✓ ".count($rows)." instructors imported\n";

// ═══════════════════════════════════════════════════════════════════════
// 3. SKILLS
// ═══════════════════════════════════════════════════════════════════════
echo "Importing 03_skills → skills...\n";
$rows = readAllRows($wb, '03_skills');
DB::table('skills')->truncate();
DB::statement('ALTER TABLE skills AUTO_INCREMENT = 1');
foreach ($rows as $r) {
    DB::table('skills')->insertOrIgnore([
        'id'   => $r['skill_id'],
        'name' => $r['nama'] ?? '',
        'slug' => Str::slug($r['nama'] ?? 'unknown'),
        'created_at' => $now, 'updated_at' => $now,
    ]);
}
echo "  ✓ ".count($rows)." skills imported\n";

// ═══════════════════════════════════════════════════════════════════════
// 4. COURSES
// ═══════════════════════════════════════════════════════════════════════
echo "Importing 02_courses → courses...\n";
$rows = readAllRows($wb, '02_courses');
DB::table('courses')->truncate();
DB::statement('ALTER TABLE courses AUTO_INCREMENT = 1');
$count = 0;
foreach ($rows as $r) {
    $durasiDetik = is_numeric($r['durasi_detik'] ?? null) ? (int)$r['durasi_detik'] : 0;
    DB::table('courses')->insertOrIgnore([
        'id'              => $r['course_id'],
        'category_id'     => $r['topic_id'],
        'title'           => $r['judul'] ?? '',
        'slug'            => $r['slug'] ?? Str::slug($r['judul'] ?? 'course-'.$count),
        'description'     => $r['deskripsi'] ?? null,
        'instructor_name' => null,
        'level'           => $r['level'] ?? null,
        'scraped_level'   => $r['level'] ?? null,
        'durasi'          => $r['durasi'] ?? null,
        'durasi_detik'    => $durasiDetik,
        'release_date'    => $r['tanggal_rilis'] ?? null,
        'rating'          => is_numeric($r['rating'] ?? null) ? $r['rating'] : null,
        'rating_count'    => is_numeric($r['jumlah_rating'] ?? null) ? (int)$r['jumlah_rating'] : null,
        'jumlah_learner'  => is_numeric($r['jumlah_learner'] ?? null) ? (int)$r['jumlah_learner'] : 0,
        'source_url'      => $r['url'] ?? null,
        'is_published'    => true,
        'created_at'      => $now, 'updated_at' => $now,
    ]);
    $count++;
}
echo "  ✓ {$count} courses imported\n";

// ═══════════════════════════════════════════════════════════════════════
// 5. CHAPTERS
// ═══════════════════════════════════════════════════════════════════════
echo "Importing 08_chapters → chapters...\n";
$rows = readAllRows($wb, '08_chapters');
DB::table('chapters')->truncate();
DB::statement('ALTER TABLE chapters AUTO_INCREMENT = 1');
foreach ($rows as $r) {
    DB::table('chapters')->insertOrIgnore([
        'id'           => $r['chapter_id'],
        'course_id'    => $r['course_id'],
        'judul'        => $r['judul'] ?? '',
        'urutan'       => is_numeric($r['urutan'] ?? null) ? (int)$r['urutan'] : 0,
        'jumlah_video' => is_numeric($r['jumlah_video'] ?? null) ? (int)$r['jumlah_video'] : 0,
        'created_at'   => $now, 'updated_at' => $now,
    ]);
}
echo "  ✓ ".count($rows)." chapters imported\n";

// ═══════════════════════════════════════════════════════════════════════
// 6. VIDEOS
// ═══════════════════════════════════════════════════════════════════════
echo "Importing 09_videos → videos...\n";
$rows = readAllRows($wb, '09_videos');
DB::table('videos')->truncate();
DB::statement('ALTER TABLE videos AUTO_INCREMENT = 1');
foreach ($rows as $r) {
    $durasiDetik = is_numeric($r['durasi_detik'] ?? null) ? (int)$r['durasi_detik'] : 0;
    DB::table('videos')->insertOrIgnore([
        'id'           => $r['video_id'],
        'course_id'    => $r['course_id'],
        'chapter_id'   => $r['chapter_id'] ?? null,
        'title'        => $r['judul'] ?? '',
        'slug'         => Str::slug($r['judul'] ?? 'video').'-'.Str::random(4),
        'video_url'    => null,
        'durasi'       => $r['durasi'] ?? null,
        'durasi_detik' => $durasiDetik,
        'urutan'       => is_numeric($r['urutan'] ?? null) ? (int)$r['urutan'] : 0,
        'is_preview'   => false,
        'tipe'         => $r['tipe'] ?? 'video',
        'created_at'   => $now, 'updated_at' => $now,
    ]);
}
echo "  ✓ ".count($rows)." videos imported\n";

// ═══════════════════════════════════════════════════════════════════════
// 7. COURSE_SKILL (pivot)
// ═══════════════════════════════════════════════════════════════════════
echo "Importing 04_course_skill → course_skill...\n";
$rows = readAllRows($wb, '04_course_skill');
DB::table('course_skill')->truncate();
$inserted = 0;
foreach ($rows as $r) {
    try {
        DB::table('course_skill')->insert([
            'course_id'  => $r['course_id'],
            'skill_id'   => $r['skill_id'],
            'skill_nama' => $r['skill_nama'] ?? null,
        ]);
        $inserted++;
    } catch (\Exception $e) {
        // skip duplicates
    }
}
echo "  ✓ {$inserted} course_skill relations imported\n";

// ═══════════════════════════════════════════════════════════════════════
// 8. COURSE_INCLUDES
// ═══════════════════════════════════════════════════════════════════════
echo "Importing 05_includes → course_includes...\n";
$rows = readAllRows($wb, '05_includes');
DB::table('course_includes')->truncate();
$inserted = 0;
foreach ($rows as $r) {
    try {
        DB::table('course_includes')->insert([
            'course_id' => $r['course_id'],
            'item'      => $r['item'] ?? '',
            'created_at' => $now, 'updated_at' => $now,
        ]);
        $inserted++;
    } catch (\Exception $e) {
        // skip duplicates
    }
}
echo "  ✓ {$inserted} course_includes imported\n";

// ═══════════════════════════════════════════════════════════════════════
// 9. COURSE_INSTRUCTOR (pivot)
// ═══════════════════════════════════════════════════════════════════════
echo "Importing 07_course_instructor → course_instructor...\n";
$rows = readAllRows($wb, '07_course_instructor');
DB::table('course_instructor')->truncate();
$inserted = 0;
foreach ($rows as $r) {
    try {
        DB::table('course_instructor')->insert([
            'course_id'     => $r['course_id'],
            'instructor_id' => $r['instructor_id'],
            'nama'          => $r['nama'] ?? null,
        ]);
        $inserted++;
    } catch (\Exception $e) {
        // skip duplicates
    }
}
echo "  ✓ {$inserted} course_instructor relations imported\n";

// ── Re-enable FK checks ─────────────────────────────────────────────
DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "\n🎉 Import selesai! Semua data dari XLSX berhasil dimasukkan ke database.\n";
