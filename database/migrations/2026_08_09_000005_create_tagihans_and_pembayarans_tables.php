<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tabel Tagihan (Invoice per siswa) ─────────────────
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('spp_id')->nullable()->constrained('spps')->nullOnDelete();
            $table->string('jenis_biaya');                // SPP Bulanan, Uang Bangunan, dll.
            $table->smallInteger('tahun');                 // Tahun tagihan (contoh: 2026)
            $table->tinyInteger('bulan')->nullable();      // 1-12, null = tagihan non-bulanan
            $table->decimal('nominal', 12, 2);             // Total nominal tagihan
            $table->date('jatuh_tempo')->nullable();       // Tanggal jatuh tempo
            $table->enum('status', ['Belum Lunas', 'Lunas Sebagian', 'Lunas', 'Dibatalkan'])
                ->default('Belum Lunas');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['siswa_id', 'tahun']);
            $table->index(['status']);
            $table->index(['jatuh_tempo']);
        });

        // ── Tabel Pembayaran (Transaksi per bayar) ────────────
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihans')->cascadeOnDelete();
            $table->date('tanggal_bayar');
            $table->decimal('nominal', 12, 2);               // Jumlah yang dibayar kali ini
            $table->string('metode')->default('Tunai');      // Tunai, Transfer, dll.
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['tagihan_id']);
            $table->index(['tanggal_bayar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
        Schema::dropIfExists('tagihans');
    }
};
