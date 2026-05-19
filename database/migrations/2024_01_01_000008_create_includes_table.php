<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_includes', function (Blueprint $table) {
            $table->id();
            $table->string('course_id');
            $table->string('item');
            $table->timestamps();
            $table->index(['course_id']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_includes');
    }
};
