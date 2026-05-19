<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id('course_id');
            $table->foreignId('topic_id')
                  ->nullable()
                  ->constrained('topics', 'topic_id')
                  ->nullOnDelete();

            $table->string('slug', 200)->unique();
            $table->string('judul', 300);
            $table->string('thumbnail', 300)->nullable();
            $table->enum('level', ['Beginner', 'Intermediate', 'Advanced', 'General'])->default('General');
            $table->string('durasi', 50)->nullable();           // "2h 15m" (tampilan)
            $table->unsignedInteger('durasi_detik')->nullable();
            $table->date('tanggal_rilis')->nullable();
            $table->decimal('rating', 3, 1)->default(0);        // dihitung otomatis dari reviews
            $table->unsignedInteger('jumlah_rating')->default(0);
            $table->unsignedInteger('jumlah_learner')->default(0); // dihitung dari enrollments
            $table->text('deskripsi')->nullable();
            $table->string('url', 300)->nullable();
            $table->enum('status', ['draft', 'review', 'published', 'archived'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
