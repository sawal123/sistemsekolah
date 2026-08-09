<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('semester');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            $table->unique(['tahun', 'semester'], 'tahun_ajarans_tahun_semester_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->dropUnique('tahun_ajarans_tahun_semester_unique');
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
