<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('course_id');
            $table->string('chapter_id')->nullable();
            $table->string('title');
            $table->string('slug')->index();
            $table->string('video_url')->nullable();
            $table->string('durasi')->nullable();
            $table->integer('durasi_detik')->default(0);
            $table->integer('urutan')->default(0);
            $table->enum('tipe', ['video', 'exercise', 'quiz'])->default('video');
            $table->boolean('is_preview')->default(false);
            $table->timestamps();

            $table->index(['course_id', 'chapter_id', 'urutan']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('chapter_id')->references('id')->on('chapters')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
