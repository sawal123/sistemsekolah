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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('nisn')->unique();
            $table->string('nis')->unique();
            $table->enum('jenjang', ['SMP', 'SMA']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'])->nullable();
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('no_telp_ortu')->nullable();
            $table->enum('status', ['Aktif', 'Lulus', 'Pindah', 'Dikeluarkan'])->default('Aktif');
            $table->string('tahun_lulus')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
