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
        Schema::create('kegiatan_alumnis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->enum('jenis_kegiatan', ['Kuliah', 'Kerja', 'Wirausaha', 'Mencari_Kerja', 'Lainnya']);
            $table->string('nama_instansi')->nullable();
            $table->string('posisi_jurusan')->nullable();
            $table->string('tahun_mulai');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_alumnis');
    }
};
