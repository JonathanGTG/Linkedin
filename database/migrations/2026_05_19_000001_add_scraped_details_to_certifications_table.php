<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->string('source_url')->nullable()->after('slug');
            $table->string('level')->nullable()->after('type');
            $table->string('durasi')->nullable()->after('level');
            $table->unsignedInteger('durasi_detik')->default(0)->after('durasi');
            $table->string('tanggal_rilis')->nullable()->after('durasi_detik');
            $table->decimal('rating', 3, 1)->nullable()->after('tanggal_rilis');
            $table->unsignedInteger('jumlah_rating')->nullable()->after('rating');
            $table->unsignedInteger('jumlah_learner')->nullable()->after('jumlah_rating');
            $table->text('skills')->nullable()->after('jumlah_learner');
            $table->text('konten_path')->nullable()->after('skills');
        });
    }

    public function down(): void
    {
        Schema::table('certifications', function (Blueprint $table) {
            $table->dropColumn([
                'source_url',
                'level',
                'durasi',
                'durasi_detik',
                'tanggal_rilis',
                'rating',
                'jumlah_rating',
                'jumlah_learner',
                'skills',
                'konten_path',
            ]);
        });
    }
};
