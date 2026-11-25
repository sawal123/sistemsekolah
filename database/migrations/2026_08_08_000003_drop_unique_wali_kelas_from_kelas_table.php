<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah index biasa terlebih dahulu agar MySQL InnoDB memiliki backing index untuk FK sebelum dropUnique
        Schema::table('kelas', function (Blueprint $table) {
            $table->index('wali_kelas_id', 'kelas_wali_kelas_id_index');
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropUnique(['wali_kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->unique('wali_kelas_id');
        });

        Schema::table('kelas', function (Blueprint $table) {
            $table->dropIndex('kelas_wali_kelas_id_index');
        });
    }
};
