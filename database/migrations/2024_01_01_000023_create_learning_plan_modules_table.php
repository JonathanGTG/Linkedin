<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_plan_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_goal_id')->constrained('career_goals')->onDelete('cascade');
            $table->string('judul_modul');
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
            $table->index(['career_goal_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_plan_modules');
    }
};
