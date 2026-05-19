<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_instructor', function (Blueprint $table) {
            $table->id();
            $table->string('course_id');
            $table->string('instructor_id');
            $table->string('nama')->nullable();
            $table->timestamps();
            $table->unique(['course_id', 'instructor_id']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_instructor');
    }
};
