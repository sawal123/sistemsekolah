<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Dedup sebelum UNIQUE — pertahankan record dengan ID terbesar ──
        $duplicates = DB::table('nilais')
            ->select('siswa_id', 'mapel_id', 'tahun_ajaran_id', DB::raw('MAX(id) as keep_id'))
            ->groupBy('siswa_id', 'mapel_id', 'tahun_ajaran_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('nilais')
                ->where('siswa_id', $dup->siswa_id)
                ->where('mapel_id', $dup->mapel_id)
                ->where('tahun_ajaran_id', $dup->tahun_ajaran_id)
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

        Schema::table('nilais', function (Blueprint $table) {
            $table->unique(
                ['siswa_id', 'mapel_id', 'tahun_ajaran_id'],
                'nilais_siswa_mapel_tahun_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->dropUnique('nilais_siswa_mapel_tahun_unique');
        });
    }
};
