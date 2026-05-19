<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_skill', function (Blueprint $table) {
            $table->id();
            $table->string('course_id');
            $table->string('skill_id');
            $table->string('skill_nama')->nullable();
            $table->timestamps();
            $table->unique(['course_id', 'skill_id']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_skill');
    }
};
