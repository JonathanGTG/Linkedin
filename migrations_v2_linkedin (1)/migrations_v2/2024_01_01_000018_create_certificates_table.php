<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id('certificate_id');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('course_id')
                  ->constrained('courses', 'course_id')
                  ->cascadeOnDelete();
            $table->foreignId('enrollment_id')
                  ->constrained('enrollments', 'enrollment_id')
                  ->cascadeOnDelete();

            $table->string('nomor_sertifikat', 100)->unique();  // kode unik sertifikat
            $table->timestamp('diterbitkan_at')->useCurrent();
            $table->string('file_url', 300)->nullable();         // link download PDF sertifikat
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
