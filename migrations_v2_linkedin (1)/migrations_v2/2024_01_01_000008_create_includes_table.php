<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('includes', function (Blueprint $table) {
            $table->id('include_id');
            $table->foreignId('course_id')
                  ->constrained('courses', 'course_id')
                  ->cascadeOnDelete();
            $table->string('item', 200);   // "Certificate of completion", "Exercise files"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('includes');
    }
};
