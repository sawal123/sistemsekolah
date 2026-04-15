<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayaran_spps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('spp_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Log petugas TU
            $table->smallInteger('tahun');                        // Tahun TAGIHAN (bukan tahun bayar)
            $table->tinyInteger('bulan')->nullable();              // 1-12 (null = tagihan sekali bayar)
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_bayar', 12, 2);
            $table->decimal('potongan', 12, 2)->default(0);       // Diskon / Beasiswa
            $table->enum('status', ['Lunas', 'Belum_Lunas', 'Menunggu_Konfirmasi'])->default('Lunas');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_spps');
    }
};
