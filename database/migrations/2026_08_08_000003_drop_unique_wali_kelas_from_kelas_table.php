<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // FK InnoDB butuh index — sediakan index biasa DULU sebelum unique di-drop.
            // Tanpa ini MySQL menolak: "needed in a foreign key constraint".
            $table->index('wali_kelas_id');
            $table->dropUnique(['wali_kelas_id']);
        });
    }

    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // Kembalikan unique (dipakai FK), baru buang index biasa.
            $table->unique('wali_kelas_id');
            $table->dropIndex(['wali_kelas_id']);
        });
    }
};
