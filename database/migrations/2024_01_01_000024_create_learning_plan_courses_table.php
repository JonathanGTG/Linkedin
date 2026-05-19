<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_plan_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('learning_plan_modules')->onDelete('cascade');
            $table->string('course_id');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
            $table->unique(['module_id', 'course_id']);
            $table->index(['module_id', 'urutan']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_plan_courses');
    }
};
