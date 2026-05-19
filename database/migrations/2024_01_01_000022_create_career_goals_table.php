<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role_saat_ini')->nullable();
            $table->string('goal_title');
            $table->timestamps();
            $table->unique(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_goals');
    }
};
