<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 200);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->text('bio')->nullable();
            $table->string('foto', 300)->nullable();
            $table->string('headline', 300)->nullable();        // "Software Engineer at Google"
            $table->enum('role', ['learner', 'instructor', 'admin'])->default('learner');
            $table->boolean('is_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
