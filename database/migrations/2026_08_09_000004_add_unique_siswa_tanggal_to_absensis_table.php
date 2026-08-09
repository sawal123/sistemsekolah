<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus duplikat sebelum tambah unique constraint
        // Pertahankan record dengan id terbesar untuk setiap (siswa_id, tanggal)
        DB::statement('
            DELETE a1 FROM absensis a1
            INNER JOIN absensis a2
            WHERE a1.id < a2.id
              AND a1.siswa_id = a2.siswa_id
              AND a1.tanggal = a2.tanggal
        ');

        Schema::table('absensis', function (Blueprint $table) {
            $table->unique(['siswa_id', 'tanggal'], 'absensis_siswa_tanggal_unique');
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropUnique('absensis_siswa_tanggal_unique');
        });
    }
};
