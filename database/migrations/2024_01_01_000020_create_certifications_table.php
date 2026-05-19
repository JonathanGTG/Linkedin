<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('provider')->nullable();
            $table->string('logo')->nullable();
            $table->string('type')->index();
            $table->unsignedInteger('jumlah_course')->default(0);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};
