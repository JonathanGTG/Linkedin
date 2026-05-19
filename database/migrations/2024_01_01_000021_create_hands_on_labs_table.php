<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hands_on_labs', function (Blueprint $table) {
            $table->id();
            $table->string('course_id')->nullable();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type')->index();
            $table->string('level')->nullable();
            $table->unsignedInteger('durasi_menit')->default(0);
            $table->string('teknologi')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['is_published']);
            $table->foreign('course_id')->references('id')->on('courses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hands_on_labs');
    }
};
