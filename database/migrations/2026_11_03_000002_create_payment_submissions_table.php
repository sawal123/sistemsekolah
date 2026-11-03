<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihans')->restrictOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->restrictOnDelete();
            $table->foreignId('submitted_by')->constrained('users')->restrictOnDelete();
            $table->decimal('nominal', 12, 2);
            $table->date('tanggal_transfer');
            $table->string('metode', 40);
            $table->string('bukti_path');
            $table->string('status', 32)->default('Menunggu Verifikasi');
            $table->text('catatan_siswa')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('pembayaran_id')->nullable()->unique()->constrained('pembayarans')->nullOnDelete();
            $table->timestamps();

            $table->index(['siswa_id', 'status', 'created_at']);
            $table->index(['tagihan_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_submissions');
    }
};
