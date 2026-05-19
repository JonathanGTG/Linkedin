<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_paths', function (Blueprint $table) {
            $table->id('path_id');
            $table->foreignId('topic_id')
                  ->nullable()
                  ->constrained('topics', 'topic_id')
                  ->nullOnDelete();

            $table->string('slug', 200)->unique();
            $table->string('tipe', 50)->default('Learning Path');
            $table->string('judul', 300);
            $table->string('thumbnail', 300)->nullable();
            $table->string('durasi', 50)->nullable();
            $table->string('jumlah_items', 20)->nullable();
            $table->string('level', 100)->nullable();
            $table->string('tanggal', 50)->nullable();
            $table->unsignedInteger('jumlah_learner')->default(0);
            $table->text('deskripsi')->nullable();
            $table->string('url', 300)->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_paths');
    }
};
