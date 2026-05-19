<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructors', function (Blueprint $table) {
            $table->id('instructor_id');
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('nama', 200);
            $table->string('info', 300)->nullable();            // headline / jabatan
            $table->text('bio')->nullable();                    // deskripsi lengkap
            $table->string('foto', 300)->nullable();
            $table->string('link', 300)->nullable();            // URL profil LinkedIn
            $table->string('keahlian', 300)->nullable();        // "Python, Data Science, ML"
            $table->unsignedInteger('total_siswa')->default(0);
            $table->unsignedSmallInteger('total_kursus')->default(0);
            $table->enum('status', ['pending', 'aktif', 'nonaktif'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructors');
    }
};
