<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('video_id')
                  ->constrained('videos', 'video_id')
                  ->cascadeOnDelete();
            $table->foreignId('course_id')
                  ->constrained('courses', 'course_id')
                  ->cascadeOnDelete();

            $table->unsignedSmallInteger('detik_terakhir')->default(0); // posisi terakhir ditonton
            $table->decimal('persen_ditonton', 5, 2)->default(0);       // 0.00 - 100.00
            $table->boolean('is_selesai')->default(false);              // true jika sudah 80%+
            $table->timestamp('last_watched_at')->nullable();

            $table->unique(['user_id', 'video_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_progress');
    }
};
