<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id('chapter_id');
            $table->foreignId('course_id')
                  ->constrained('courses', 'course_id')
                  ->cascadeOnDelete();
            $table->unsignedTinyInteger('urutan');
            $table->string('judul', 300);
            $table->unsignedSmallInteger('jumlah_video')->default(0);
            $table->timestamps();

            $table->unique(['course_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
