<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_murid_barus', function (Blueprint $table) {
            $table->id();

            $table->string('nama_lengkap');
            $table->string('nisn')->nullable()->unique();
            $table->string('nik')->nullable()->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan'])->nullable();
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'])->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();

            $table->text('alamat')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('dusun')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota_kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('no_kk')->nullable();
            $table->date('tanggal_terbit_kk')->nullable();
            $table->string('koordinat_rumah')->nullable();

            $table->string('sekolah_asal')->nullable();
            $table->string('npsn_sekolah_asal')->nullable();
            $table->year('tahun_lulus')->nullable();

            $table->json('nilai_rapor')->nullable();
            $table->string('nama_prestasi')->nullable();
            $table->string('tingkat_prestasi')->nullable();
            $table->year('tahun_prestasi')->nullable();
            $table->string('penyelenggara_prestasi')->nullable();

            $table->string('nama_ayah')->nullable();
            $table->string('nik_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('nik_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('nik_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('penghasilan_ortu')->nullable();
            $table->string('no_telp_ortu')->nullable();

            $table->string('sekolah_pilihan_1')->nullable();
            $table->string('jurusan_pilihan_1')->nullable();
            $table->string('sekolah_pilihan_2')->nullable();
            $table->string('jurusan_pilihan_2')->nullable();

            $table->enum('document_upload_mode', ['terpisah', 'gabungan'])->default('terpisah');
            $table->string('ijazah_skl')->nullable();
            $table->string('kartu_keluarga')->nullable();
            $table->string('akta_kelahiran')->nullable();
            $table->string('ktp_orang_tua')->nullable();
            $table->string('buku_rapor')->nullable();
            $table->string('pas_foto')->nullable();
            $table->string('dokumen_gabungan')->nullable();

            $table->enum('status', ['Baru', 'Diperiksa', 'Lengkap', 'Diterima', 'Ditolak'])->default('Baru');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_murid_barus');
    }
};
