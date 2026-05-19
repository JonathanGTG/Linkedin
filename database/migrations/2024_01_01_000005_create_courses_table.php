<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('category_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('instructor_name')->nullable();
            $table->enum('level', ['Beginner', 'Intermediate', 'Advanced'])->default('Beginner');
            $table->string('scraped_level')->nullable();
            $table->string('durasi')->nullable();
            $table->integer('durasi_detik')->default(0);
            $table->integer('jumlah_learner')->default(0);
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('rating_count')->nullable();
            $table->date('release_date')->nullable();
            $table->string('source_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
            $table->index(['category_id', 'is_published']);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
