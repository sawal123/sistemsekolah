<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Preflight: deteksi duplikat SEBELUM perubahan schema ──
        // Data akademik tidak boleh dihapus otomatis — admin harus perbaiki manual
        $duplicates = DB::table('tahun_ajarans')
            ->select('tahun', 'semester', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('tahun', 'semester')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isNotEmpty()) {
            $lines = $duplicates->map(fn ($d) => "  - {$d->tahun} {$d->semester}: {$d->jumlah} record")->join("\n");

            throw new \RuntimeException(
                "⚠️  Ditemukan duplikat periode akademik di database production:\n\n"
                    . "{$lines}\n\n"
                    . "Langkah perbaikan manual sebelum migration dijalankan ulang:\n"
                    . "1. Pilih satu record yang akan dipertahankan untuk setiap (tahun, semester).\n"
                    . "2. Pindahkan seluruh data (nilai, rapor, SPP, rombel, jadwal) dari record yang akan dihapus ke record yang dipertahankan.\n"
                    . "3. Hapus record duplikat: DELETE FROM tahun_ajarans WHERE id = <remove_id>;\n"
                    . "4. Jalankan ulang php artisan migrate.\n"
            );
        }

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
