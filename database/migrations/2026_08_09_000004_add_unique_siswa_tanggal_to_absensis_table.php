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
        // Gunakan query portable (kompatibel dengan MySQL & SQLite)
        $duplicates = DB::table('absensis')
            ->select('siswa_id', 'tanggal', DB::raw('MAX(id) as keep_id'))
            ->groupBy('siswa_id', 'tanggal')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('absensis')
                ->where('siswa_id', $dup->siswa_id)
                ->where('tanggal', $dup->tanggal)
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

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
