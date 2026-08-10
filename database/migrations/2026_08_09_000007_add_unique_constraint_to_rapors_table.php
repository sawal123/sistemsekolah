<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Dedup rapor: satu siswa hanya boleh satu rapor per tahun ajaran ──
        $duplicates = DB::table('rapors')
            ->select('siswa_id', 'tahun_ajaran_id', DB::raw('MAX(id) as keep_id'))
            ->groupBy('siswa_id', 'tahun_ajaran_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('rapors')
                ->where('siswa_id', $dup->siswa_id)
                ->where('tahun_ajaran_id', $dup->tahun_ajaran_id)
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

        Schema::table('rapors', function (Blueprint $table) {
            $table->unique(
                ['siswa_id', 'tahun_ajaran_id'],
                'rapors_siswa_tahun_ajaran_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('rapors', function (Blueprint $table) {
            $table->dropUnique('rapors_siswa_tahun_ajaran_unique');
        });
    }
};
