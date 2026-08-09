<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill data dari pembayaran_spps (lama) ke tagihans + pembayarans (baru)
        // Hanya untuk data yang belum ada di tabel baru

        $oldPayments = DB::table('pembayaran_spps')
            ->where('status', 'Lunas')
            ->orderBy('id')
            ->get();

        if ($oldPayments->isEmpty()) {
            return;
        }

        $sppCache = DB::table('spps')->pluck('kategori', 'id');

        foreach ($oldPayments as $old) {
            // 1. Cari atau buat Tagihan
            $tagihanId = DB::table('tagihans')
                ->where('siswa_id', $old->siswa_id)
                ->where('spp_id', $old->spp_id)
                ->where('tahun', $old->tahun)
                ->when($old->bulan, fn($q) => $q->where('bulan', $old->bulan))
                ->value('id');

            if (! $tagihanId) {
                $sppNominal = DB::table('spps')->where('id', $old->spp_id)->value('nominal') ?? $old->jumlah_bayar;

                $tagihanId = DB::table('tagihans')->insertGetId([
                    'siswa_id' => $old->siswa_id,
                    'spp_id' => $old->spp_id,
                    'jenis_biaya' => $sppCache[$old->spp_id] ?? 'SPP Bulanan',
                    'tahun' => $old->tahun,
                    'bulan' => $old->bulan,
                    'nominal' => $sppNominal,
                    'jatuh_tempo' => null,
                    'status' => 'Lunas', // Karena sudah dibayar di sistem lama
                    'keterangan' => 'Migrasi dari sistem lama',
                    'created_at' => $old->created_at ?? now(),
                    'updated_at' => $old->updated_at ?? now(),
                ]);
            } else {
                // Update status tagihan jadi Lunas
                DB::table('tagihans')->where('id', $tagihanId)->update(['status' => 'Lunas']);
            }

            // 2. Buat Pembayaran (hindari duplikat)
            $alreadyExists = DB::table('pembayarans')
                ->where('tagihan_id', $tagihanId)
                ->where('tanggal_bayar', $old->tanggal_bayar)
                ->where('nominal', $old->jumlah_bayar - max(0, $old->potongan))
                ->exists();

            if (! $alreadyExists) {
                DB::table('pembayarans')->insert([
                    'tagihan_id' => $tagihanId,
                    'tanggal_bayar' => $old->tanggal_bayar,
                    'nominal' => $old->jumlah_bayar - max(0, $old->potongan),
                    'metode' => 'Tunai',
                    'petugas_id' => $old->user_id,
                    'keterangan' => $old->keterangan ?? 'Migrasi dari sistem lama',
                    'created_at' => $old->created_at ?? now(),
                    'updated_at' => $old->updated_at ?? now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Hapus data hasil migrasi (tidak drop tabel)
        DB::table('pembayarans')->where('keterangan', 'Migrasi dari sistem lama')->delete();
        DB::table('tagihans')->where('keterangan', 'Migrasi dari sistem lama')->delete();
    }
};
