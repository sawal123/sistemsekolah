<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rombels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wali_kelas_id')->nullable()->constrained('gurus')->nullOnDelete();
            $table->unsignedSmallInteger('kapasitas')->nullable();
            $table->string('status', 30)->default('Aktif');
            $table->timestamps();

            $table->unique(['kelas_id', 'tahun_ajaran_id']);
            $table->unique(['tahun_ajaran_id', 'wali_kelas_id']);
        });

        Schema::create('anggota_rombels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rombel_id')->constrained('rombels')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            $table->string('status', 30)->default('Aktif');
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->timestamps();

            $table->unique(['rombel_id', 'siswa_id']);
            $table->index('siswa_id');
        });

        $now = now();
        $rombelId = function ($kelasId, $tahunAjaranId) use ($now) {
            DB::table('rombels')->insertOrIgnore([
                'kelas_id' => $kelasId,
                'tahun_ajaran_id' => $tahunAjaranId,
                'wali_kelas_id' => DB::table('kelas')->where('id', $kelasId)->value('wali_kelas_id'),
                'status' => 'Aktif',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return DB::table('rombels')
                ->where('kelas_id', $kelasId)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->value('id');
        };

        DB::table('rapors')
            ->select('siswa_id', 'kelas_id', 'tahun_ajaran_id')
            ->orderBy('id')
            ->get()
            ->unique(fn ($rapor) => $rapor->siswa_id.'-'.$rapor->tahun_ajaran_id)
            ->each(function ($rapor) use ($rombelId, $now) {
                $id = $rombelId($rapor->kelas_id, $rapor->tahun_ajaran_id);

                DB::table('anggota_rombels')->insertOrIgnore([
                    'rombel_id' => $id,
                    'siswa_id' => $rapor->siswa_id,
                    'status' => 'Aktif',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });

        $activeTahunAjaranId = DB::table('tahun_ajarans')->where('is_active', true)->value('id');

        if ($activeTahunAjaranId) {
            DB::table('kelas')
                ->select('id')
                ->orderBy('id')
                ->get()
                ->each(fn ($kelas) => $rombelId($kelas->id, $activeTahunAjaranId));

            DB::table('siswas')
                ->whereNotNull('kelas_id')
                ->select('id', 'kelas_id')
                ->orderBy('id')
                ->get()
                ->each(function ($siswa) use ($activeTahunAjaranId, $rombelId, $now) {
                    $id = $rombelId($siswa->kelas_id, $activeTahunAjaranId);

                    DB::table('anggota_rombels')->insertOrIgnore([
                        'rombel_id' => $id,
                        'siswa_id' => $siswa->id,
                        'status' => 'Aktif',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_rombels');
        Schema::dropIfExists('rombels');
    }
};
