<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('units')) {
            Schema::create('units', function (Blueprint $table) {
                $table->id();
                $table->string('kode', 20)->unique();
                $table->string('nama');
                $table->string('npsn', 30)->nullable();
                $table->unsignedTinyInteger('tingkat_awal')->default(1);
                $table->unsignedTinyInteger('tingkat_akhir')->default(6);
                $table->boolean('punya_jurusan')->default(false);
                $table->string('kepala_sekolah')->nullable();
                $table->string('nip_kepala_sekolah', 50)->nullable();
                $table->text('alamat')->nullable();
                $table->string('telepon', 30)->nullable();
                $table->string('email')->nullable();
                $table->string('logo')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedSmallInteger('urutan')->default(0);
                $table->timestamps();
            });
        }

        $tablesWithUnit = [
            'users',
            'gurus',
            'kelas',
            'siswas',
            'mapels',
            'spps',
            'ruangans',
            'ppdb_gelombangs',
            'pendaftaran_murid_barus',
            'kalender_akademiks',
        ];

        foreach ($tablesWithUnit as $tableName) {
            if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'unit_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
                });
            }
        }

        // Ubah enum jenjang_pilihan menjadi string agar mendukung SD dan unit dinamis lainnya
        if (Schema::hasTable('pendaftaran_murid_barus') && Schema::hasColumn('pendaftaran_murid_barus', 'jenjang_pilihan')) {
            Schema::table('pendaftaran_murid_barus', function (Blueprint $table) {
                $table->string('jenjang_pilihan', 20)->nullable()->change();
            });
        }

        // Seed 4 unit default (idempoten)
        $now = now();
        $defaultUnits = [
            [
                'kode' => 'SD',
                'nama' => 'Sekolah Dasar (SD)',
                'tingkat_awal' => 1,
                'tingkat_akhir' => 6,
                'punya_jurusan' => false,
                'is_active' => true,
                'urutan' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'SMP',
                'nama' => 'Sekolah Menengah Pertama (SMP)',
                'tingkat_awal' => 7,
                'tingkat_akhir' => 9,
                'punya_jurusan' => false,
                'is_active' => true,
                'urutan' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'SMA',
                'nama' => 'Sekolah Menengah Atas (SMA)',
                'tingkat_awal' => 10,
                'tingkat_akhir' => 12,
                'punya_jurusan' => false,
                'is_active' => true,
                'urutan' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kode' => 'SMK',
                'nama' => 'Sekolah Menengah Kejuruan (SMK)',
                'tingkat_awal' => 10,
                'tingkat_akhir' => 12,
                'punya_jurusan' => true,
                'is_active' => true,
                'urutan' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($defaultUnits as $unit) {
            DB::table('units')->insertOrIgnore($unit);
        }

        $unitMap = DB::table('units')->pluck('id', 'kode')->toArray();

        // Backfill kelas, siswas, mapels, spps berdasarkan kolom jenjang
        foreach (['kelas', 'siswas', 'mapels', 'spps'] as $tbl) {
            if (! Schema::hasTable($tbl)) {
                continue;
            }
            foreach ($unitMap as $kode => $unitId) {
                DB::table($tbl)
                    ->whereNull('unit_id')
                    ->where('jenjang', $kode)
                    ->update(['unit_id' => $unitId]);
            }
        }

        // Backfill pendaftaran_murid_barus berdasarkan jenjang_pilihan
        if (Schema::hasTable('pendaftaran_murid_barus')) {
            foreach ($unitMap as $kode => $unitId) {
                DB::table('pendaftaran_murid_barus')
                    ->whereNull('unit_id')
                    ->where('jenjang_pilihan', $kode)
                    ->update(['unit_id' => $unitId]);
            }
        }

        // Backfill gurus berdasarkan kelas wali kelas -> jadwal -> default SMP/pertama
        if (Schema::hasTable('gurus')) {
            $gurus = DB::table('gurus')->whereNull('unit_id')->get(['id', 'user_id']);
            $fallbackUnitId = $unitMap['SMP'] ?? $unitMap['SMA'] ?? reset($unitMap) ?: null;

            foreach ($gurus as $guru) {
                $unitId = DB::table('kelas')
                    ->where('wali_kelas_id', $guru->id)
                    ->whereNotNull('unit_id')
                    ->value('unit_id');

                if (! $unitId && Schema::hasTable('jadwals')) {
                    $unitId = DB::table('jadwals')
                        ->join('kelas', 'jadwals.kelas_id', '=', 'kelas.id')
                        ->where('jadwals.guru_id', $guru->id)
                        ->whereNotNull('kelas.unit_id')
                        ->value('kelas.unit_id');
                }

                $finalUnitId = $unitId ?: $fallbackUnitId;
                if ($finalUnitId) {
                    DB::table('gurus')->where('id', $guru->id)->update(['unit_id' => $finalUnitId]);
                    if ($guru->user_id) {
                        DB::table('users')
                            ->where('id', $guru->user_id)
                            ->whereNull('unit_id')
                            ->update(['unit_id' => $finalUnitId]);
                    }
                }
            }
        }

        // Backfill users milik siswa
        if (Schema::hasTable('siswas')) {
            $siswas = DB::table('siswas')->whereNotNull('unit_id')->whereNotNull('user_id')->get(['user_id', 'unit_id']);
            foreach ($siswas as $siswa) {
                DB::table('users')
                    ->where('id', $siswa->user_id)
                    ->whereNull('unit_id')
                    ->update(['unit_id' => $siswa->unit_id]);
            }
        }
    }

    public function down(): void
    {
        $tablesWithUnit = [
            'kalender_akademiks',
            'pendaftaran_murid_barus',
            'ppdb_gelombangs',
            'ruangans',
            'spps',
            'mapels',
            'siswas',
            'kelas',
            'gurus',
            'users',
        ];

        foreach ($tablesWithUnit as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'unit_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('unit_id');
                });
            }
        }

        Schema::dropIfExists('units');
    }
};
