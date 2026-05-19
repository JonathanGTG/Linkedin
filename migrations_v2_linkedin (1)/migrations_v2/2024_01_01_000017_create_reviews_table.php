<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('review_id');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('course_id')
                  ->constrained('courses', 'course_id')
                  ->cascadeOnDelete();

            $table->unsignedTinyInteger('rating');               // 1 - 5
            $table->text('ulasan')->nullable();
            $table->boolean('is_verified')->default(false);      // hanya bisa review jika sudah enroll
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);            // 1 user hanya bisa review 1x per course
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
