<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_skill', function (Blueprint $table) {
            $table->foreignId('course_id')
                  ->constrained('courses', 'course_id')
                  ->cascadeOnDelete();
            $table->foreignId('skill_id')
                  ->constrained('skills', 'skill_id')
                  ->cascadeOnDelete();

            $table->primary(['course_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_skill');
    }
};
