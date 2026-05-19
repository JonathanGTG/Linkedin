<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Poin "What you'll learn" dalam learning path
        Schema::create('path_what_learn', function (Blueprint $table) {
            $table->id();
            $table->foreignId('path_id')
                  ->constrained('learning_paths', 'path_id')
                  ->cascadeOnDelete();
            $table->unsignedTinyInteger('urutan');
            $table->text('poin');
            $table->timestamps();

            $table->unique(['path_id', 'urutan']);
        });

        // Course yang ada di dalam learning path
        Schema::create('path_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('path_id')
                  ->constrained('learning_paths', 'path_id')
                  ->cascadeOnDelete();
            $table->foreignId('course_id')
                  ->nullable()
                  ->constrained('courses', 'course_id')
                  ->nullOnDelete();
            $table->unsignedTinyInteger('urutan');
            $table->string('tipe', 50)->default('Course');
            $table->timestamps();

            $table->unique(['path_id', 'urutan']);
        });

        // Instruktur learning path
        Schema::create('path_instructor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('path_id')
                  ->constrained('learning_paths', 'path_id')
                  ->cascadeOnDelete();
            $table->foreignId('instructor_id')
                  ->constrained('instructors', 'instructor_id')
                  ->cascadeOnDelete();
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->timestamps();

            $table->unique(['path_id', 'instructor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('path_instructor');
        Schema::dropIfExists('path_content');
        Schema::dropIfExists('path_what_learn');
    }
};
