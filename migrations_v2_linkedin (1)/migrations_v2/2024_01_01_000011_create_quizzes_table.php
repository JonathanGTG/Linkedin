<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel kuis (1 kuis per chapter)
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id('quiz_id');
            $table->foreignId('chapter_id')
                  ->constrained('chapters', 'chapter_id')
                  ->cascadeOnDelete();
            $table->foreignId('course_id')
                  ->constrained('courses', 'course_id')
                  ->cascadeOnDelete();
            $table->string('judul', 200);
            $table->unsignedTinyInteger('passing_score')->default(70); // nilai minimum lulus (%)
            $table->timestamps();
        });

        // Pertanyaan dalam kuis
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id('question_id');
            $table->foreignId('quiz_id')
                  ->constrained('quizzes', 'quiz_id')
                  ->cascadeOnDelete();
            $table->unsignedTinyInteger('urutan');
            $table->text('pertanyaan');
            $table->string('pilihan_a', 300);
            $table->string('pilihan_b', 300);
            $table->string('pilihan_c', 300)->nullable();
            $table->string('pilihan_d', 300)->nullable();
            $table->enum('jawaban_benar', ['a', 'b', 'c', 'd']);
            $table->text('penjelasan')->nullable();              // penjelasan jawaban benar
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
