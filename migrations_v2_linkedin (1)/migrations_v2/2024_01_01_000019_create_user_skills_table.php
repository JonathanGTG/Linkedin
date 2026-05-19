<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skill yang diperoleh user setelah menyelesaikan course
        Schema::create('user_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('skill_id')
                  ->constrained('skills', 'skill_id')
                  ->cascadeOnDelete();
            $table->foreignId('course_id')                       // dari course mana skill ini diperoleh
                  ->nullable()
                  ->constrained('courses', 'course_id')
                  ->nullOnDelete();
            $table->timestamp('diperoleh_at')->useCurrent();

            $table->unique(['user_id', 'skill_id', 'course_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_skills');
    }
};
