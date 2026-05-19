<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Cek apakah video orphan & chapter punya pola urutan yang bisa di-match
// Misal: video.urutan bisa dikaitkan ke chapter berdasarkan range

echo "=== CHAPTER URUTAN RANGES (CRS-366C) ===\n";
$course = 'CRS-366C';
$chapters = DB::table('chapters')->where('course_id', $course)->orderBy('urutan')->get();
foreach ($chapters as $ch) {
    echo "  chapter={$ch->id} | judul={$ch->judul} | urutan={$ch->urutan} | jumlah_video={$ch->jumlah_video}\n";
}

$videos = DB::table('videos')->where('course_id', $course)->orderBy('urutan')->get(['id','chapter_id','urutan','title']);
echo "\nVideos (total " . count($videos) . "), first 5:\n";
foreach (array_slice($videos->toArray(), 0, 5) as $v) {
    echo "  id={$v->id} | ch=" . ($v->chapter_id ?? 'NULL') . " | urutan={$v->urutan} | title={$v->title}\n";
}

// Cek apakah kolom jumlah_video di chapters bisa dipakai sebagai panduan
echo "\n=== ANOTHER COURSE: CRS-590C ===\n";
$course2 = 'CRS-590C';
$ch2 = DB::table('chapters')->where('course_id', $course2)->orderBy('urutan')->get();
foreach ($ch2 as $ch) {
    echo "  chapter={$ch->id} | urutan={$ch->urutan} | jumlah_video={$ch->jumlah_video} | judul={$ch->judul}\n";
}
$vids2 = DB::table('videos')->where('course_id', $course2)->orderBy('urutan')->limit(5)->get();
echo "First 5 videos:\n";
foreach ($vids2 as $v) {
    echo "  id={$v->id} | ch=" . ($v->chapter_id ?? 'NULL') . " | urutan={$v->urutan} | title={$v->title}\n";
}

// Cek apakah chapter.jumlah_video accurate untuk courses yg punya chapter_id linkage
echo "\n=== COURSES WITH PROPERLY LINKED VIDEOS - SAMPLE ===\n";
$good = DB::table('chapters')
    ->join('videos','chapters.id','=','videos.chapter_id')
    ->select('chapters.course_id', 'chapters.id', 'chapters.jumlah_video', DB::raw('COUNT(videos.id) as actual_videos'))
    ->groupBy('chapters.course_id','chapters.id','chapters.jumlah_video')
    ->having(DB::raw('COUNT(videos.id)'), '>', 0)
    ->limit(5)
    ->get();
foreach ($good as $g) {
    echo "  course={$g->course_id} | chapter={$g->id} | jumlah_video(stored)={$g->jumlah_video} | actual={$g->actual_videos}\n";
}

// Berapa persen video orphan per suffix type
echo "\n=== ORPHAN VIDEO % BY COURSE ID SUFFIX ===\n";
$suffixGroups = [
    'B' => DB::table('videos')->where('course_id','like','%-B')->count(),
    'C' => DB::table('videos')->where('course_id','like','%-C')->count(),
    'T' => DB::table('videos')->where('course_id','like','%-T')->count(),
];
$orphanByGroup = [
    'B' => DB::table('videos')->where('course_id','like','%-B')->whereNull('chapter_id')->count(),
    'C' => DB::table('videos')->where('course_id','like','%-C')->whereNull('chapter_id')->count(),
    'T' => DB::table('videos')->where('course_id','like','%-T')->whereNull('chapter_id')->count(),
];
foreach ($suffixGroups as $suffix => $total) {
    $orphan = $orphanByGroup[$suffix];
    $pct = $total > 0 ? round($orphan/$total*100,1) : 0;
    echo "  Suffix -$suffix: total=$total | orphan=$orphan | $pct%\n";
}
