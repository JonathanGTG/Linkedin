<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id('video_id');
            $table->foreignId('chapter_id')
                  ->constrained('chapters', 'chapter_id')
                  ->cascadeOnDelete();
            $table->foreignId('course_id')
                  ->constrained('courses', 'course_id')
                  ->cascadeOnDelete();
            $table->unsignedTinyInteger('urutan');
            $table->string('judul', 300);
            $table->string('durasi', 20)->nullable();             // "4m 32s"
            $table->unsignedSmallInteger('durasi_detik')->nullable();
            $table->string('video_url', 300)->nullable();         // link file video
            $table->enum('tipe', ['video', 'exercise', 'quiz'])->default('video');
            $table->boolean('is_preview')->default(false);        // bisa ditonton tanpa enroll
            $table->timestamps();

            $table->unique(['chapter_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
