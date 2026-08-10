<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom status ke tahun_ajarans
        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->string('status', 20)->default('Draft')->after('is_active');
        });

        // Backfill status berdasarkan is_active
        DB::table('tahun_ajarans')->where('is_active', true)->update(['status' => 'Aktif']);
        DB::table('tahun_ajarans')->where('is_active', false)->update(['status' => 'Ditutup']);

        // 2. Ubah FK nilais: cascadeOnDelete → restrictOnDelete
        Schema::table('nilais', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->restrictOnDelete();
        });

        // 3. Ubah FK rapors: cascadeOnDelete → restrictOnDelete
        Schema::table('rapors', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->restrictOnDelete();
        });

        // 4. Ubah FK spps: cascadeOnDelete → restrictOnDelete
        Schema::table('spps', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->restrictOnDelete();
        });

        // 5. Ubah FK rombels: cascadeOnDelete → restrictOnDelete
        Schema::table('rombels', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        // Kembalikan FK ke cascadeOnDelete
        Schema::table('rombels', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->cascadeOnDelete();
        });

        Schema::table('spps', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->cascadeOnDelete();
        });

        Schema::table('rapors', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->cascadeOnDelete();
        });

        Schema::table('nilais', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->foreign('tahun_ajaran_id')->references('id')->on('tahun_ajarans')->cascadeOnDelete();
        });

        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
