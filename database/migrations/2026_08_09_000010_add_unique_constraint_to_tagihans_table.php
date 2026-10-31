<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dedup tagihans sebelum menambahkan unique constraint (portable untuk MySQL & SQLite)
        $duplicates = DB::table('tagihans')
            ->select('siswa_id', 'spp_id', 'tahun', 'bulan', DB::raw('MIN(id) as keep_id'))
            ->groupBy('siswa_id', 'spp_id', 'tahun', 'bulan')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            $query = DB::table('tagihans')
                ->where('siswa_id', $dup->siswa_id)
                ->where('tahun', $dup->tahun)
                ->where('id', '!=', $dup->keep_id);

            if ($dup->spp_id === null) {
                $query->whereNull('spp_id');
            } else {
                $query->where('spp_id', $dup->spp_id);
            }

            if ($dup->bulan === null) {
                $query->whereNull('bulan');
            } else {
                $query->where('bulan', $dup->bulan);
            }

            $duplicateIds = $query->pluck('id');

            if ($duplicateIds->isNotEmpty()) {
                // Pindahkan pembayaran dari tagihan duplikat ke tagihan utama agar histori pembayaran tidak hilang
                DB::table('pembayarans')
                    ->whereIn('tagihan_id', $duplicateIds)
                    ->update(['tagihan_id' => $dup->keep_id]);

                DB::table('tagihans')
                    ->whereIn('id', $duplicateIds)
                    ->delete();
            }
        }

        Schema::table('tagihans', function (Blueprint $table) {
            $table->unique(
                ['siswa_id', 'spp_id', 'tahun', 'bulan'],
                'tagihans_siswa_spp_tahun_bulan_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropUnique('tagihans_siswa_spp_tahun_bulan_unique');
        });
    }
};

