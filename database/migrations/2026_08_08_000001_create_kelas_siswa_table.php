<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('Aktif');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_ajaran_id']);
            $table->index(['kelas_id', 'tahun_ajaran_id']);
        });

        $now = now();

        DB::table('rapors')
            ->select('siswa_id', 'kelas_id', 'tahun_ajaran_id')
            ->orderBy('id')
            ->get()
            ->unique(fn ($rapor) => $rapor->siswa_id.'-'.$rapor->tahun_ajaran_id)
            ->each(function ($rapor) use ($now) {
                DB::table('kelas_siswa')->insertOrIgnore([
                    'siswa_id' => $rapor->siswa_id,
                    'kelas_id' => $rapor->kelas_id,
                    'tahun_ajaran_id' => $rapor->tahun_ajaran_id,
                    'status' => 'Aktif',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });

        $activeTahunAjaranId = DB::table('tahun_ajarans')->where('is_active', true)->value('id');

        if ($activeTahunAjaranId) {
            DB::table('siswas')
                ->whereNotNull('kelas_id')
                ->select('id', 'kelas_id')
                ->orderBy('id')
                ->get()
                ->each(function ($siswa) use ($activeTahunAjaranId, $now) {
                    DB::table('kelas_siswa')->insertOrIgnore([
                        'siswa_id' => $siswa->id,
                        'kelas_id' => $siswa->kelas_id,
                        'tahun_ajaran_id' => $activeTahunAjaranId,
                        'status' => 'Aktif',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_siswa');
    }
};
