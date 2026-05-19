<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id('attempt_id');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('quiz_id')
                  ->constrained('quizzes', 'quiz_id')
                  ->cascadeOnDelete();

            $table->unsignedTinyInteger('skor');                 // 0 - 100
            $table->boolean('is_lulus')->default(false);
            $table->unsignedTinyInteger('attempt_ke')->default(1); // percobaan ke-
            $table->timestamp('dikerjakan_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
