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
        Schema::create('mapels', function (Blueprint $table) {
            $table->id();
            // Kode unik, misal: 'MTK-SMA-10' atau 'IPA-SMP-7'
            $table->string('kode_mapel')->unique();
            $table->string('nama_mapel');
            // Tipe/Kelompok: Wajib, Peminatan, atau Muatan Lokal
            $table->enum('kelompok', ['Nasional', 'Kewilayahan', 'Peminatan', 'Mulok'])->default('Nasional');
            // Karena aplikasi untuk SMP & SMA, kita harus tahu mapel ini milik jenjang mana
            $table->enum('jenjang', ['SMP', 'SMA', 'Umum'])->default('Umum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapels');
    }
};
