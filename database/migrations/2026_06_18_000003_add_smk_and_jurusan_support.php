<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurusans', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->string('jenjang', 10)->change();
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->string('jenjang', 10)->change();
        });

        Schema::table('spps', function (Blueprint $table) {
            $table->string('jenjang', 10)->default('Semua')->change();
        });

        Schema::table('mapels', function (Blueprint $table) {
            $table->string('jenjang', 10)->default('Umum')->change();
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->foreignId('jurusan_id')->nullable()->after('jenjang')->constrained()->nullOnDelete();
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->foreignId('jurusan_id')->nullable()->after('jenjang')->constrained()->nullOnDelete();
        });

        Schema::table('spps', function (Blueprint $table) {
            $table->foreignId('jurusan_id')->nullable()->after('jenjang')->constrained()->nullOnDelete();
        });

        Schema::table('mapels', function (Blueprint $table) {
            $table->foreignId('jurusan_id')->nullable()->after('jenjang')->constrained()->nullOnDelete();
        });

        Schema::table('pendaftaran_murid_barus', function (Blueprint $table) {
            $table->enum('jenjang_pilihan', ['SMP', 'SMA', 'SMK'])->nullable()->after('sekolah_pilihan_1');
            $table->foreignId('jurusan_id')->nullable()->after('jenjang_pilihan')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_murid_barus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('jurusan_id');
            $table->dropColumn('jenjang_pilihan');
        });

        Schema::table('mapels', fn (Blueprint $table) => $table->dropConstrainedForeignId('jurusan_id'));
        Schema::table('spps', fn (Blueprint $table) => $table->dropConstrainedForeignId('jurusan_id'));
        Schema::table('siswas', fn (Blueprint $table) => $table->dropConstrainedForeignId('jurusan_id'));
        Schema::table('kelas', fn (Blueprint $table) => $table->dropConstrainedForeignId('jurusan_id'));

        Schema::table('kelas', function (Blueprint $table) {
            $table->enum('jenjang', ['SMP', 'SMA'])->change();
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->enum('jenjang', ['SMP', 'SMA'])->change();
        });

        Schema::table('spps', function (Blueprint $table) {
            $table->enum('jenjang', ['SMP', 'SMA', 'Semua'])->default('Semua')->change();
        });

        Schema::table('mapels', function (Blueprint $table) {
            $table->enum('jenjang', ['SMP', 'SMA', 'Umum'])->default('Umum')->change();
        });

        Schema::dropIfExists('jurusans');
    }
};
