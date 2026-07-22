<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb_gelombangs', function (Blueprint $table) {
            $table->id();
            $table->year('tahun_pendaftaran');
            $table->string('nama_gelombang');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['Draft', 'Dibuka', 'Ditutup'])->default('Draft');
            $table->text('deskripsi')->nullable();
            $table->timestamp('dibuka_pada')->nullable();
            $table->timestamp('ditutup_pada')->nullable();
            $table->timestamps();

            $table->unique(['tahun_pendaftaran', 'nama_gelombang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb_gelombangs');
    }
};
