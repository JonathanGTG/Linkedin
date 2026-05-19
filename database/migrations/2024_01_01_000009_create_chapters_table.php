<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('course_id');
            $table->string('judul');
            $table->integer('urutan')->default(0);
            $table->unsignedInteger('jumlah_video')->default(0);
            $table->timestamps();

            $table->unique(['course_id', 'urutan']);
            $table->index(['course_id', 'urutan']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
