<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->foreignId('tahun_ajaran_id')
                ->nullable()
                ->after('kelas_id')
                ->constrained()
                ->nullOnDelete();
            $table->index(['tahun_ajaran_id', 'kelas_id']);
            $table->index(['tahun_ajaran_id', 'guru_id']);
            $table->index(['tahun_ajaran_id', 'ruangan_id']);
        });

        $activeTahunAjaranId = DB::table('tahun_ajarans')->where('is_active', true)->value('id');

        if ($activeTahunAjaranId) {
            DB::table('jadwals')->whereNull('tahun_ajaran_id')->update([
                'tahun_ajaran_id' => $activeTahunAjaranId,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
            $table->dropIndex(['tahun_ajaran_id', 'kelas_id']);
            $table->dropIndex(['tahun_ajaran_id', 'guru_id']);
            $table->dropIndex(['tahun_ajaran_id', 'ruangan_id']);
            $table->dropColumn('tahun_ajaran_id');
        });
    }
};
