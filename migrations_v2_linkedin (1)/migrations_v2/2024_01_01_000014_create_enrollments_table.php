<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id('enrollment_id');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('course_id')
                  ->constrained('courses', 'course_id')
                  ->cascadeOnDelete();

            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();       // null = belum selesai
            $table->decimal('progress_persen', 5, 2)->default(0); // 0.00 - 100.00
            $table->enum('status', ['aktif', 'selesai', 'dropout'])->default('aktif');

            $table->unique(['user_id', 'course_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
