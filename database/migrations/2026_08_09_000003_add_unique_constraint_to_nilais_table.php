<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->unique(
                ['siswa_id', 'mapel_id', 'tahun_ajaran_id'],
                'nilais_siswa_mapel_tahun_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->dropUnique('nilais_siswa_mapel_tahun_unique');
        });
    }
};
