<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('semester');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
        });

        // ── Dedup sebelum UNIQUE — pertahankan record terbaru ──
        $duplicates = DB::table('tahun_ajarans')
            ->select('tahun', 'semester', DB::raw('MAX(id) as keep_id'))
            ->groupBy('tahun', 'semester')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $dup) {
            DB::table('tahun_ajarans')
                ->where('tahun', $dup->tahun)
                ->where('semester', $dup->semester)
                ->where('id', '!=', $dup->keep_id)
                ->delete();
        }

        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->unique(['tahun', 'semester'], 'tahun_ajarans_tahun_semester_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tahun_ajarans', function (Blueprint $table) {
            $table->dropUnique('tahun_ajarans_tahun_semester_unique');
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
