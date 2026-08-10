<?php

namespace Tests\Feature;

use App\Models\AnggotaRombel;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RombelHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_roster_uses_rombel_for_selected_academic_year(): void
    {
        $tahunLama = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => false,
        ]);
        $tahunBaru = TahunAjaran::create([
            'tahun' => '2027/2028',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $kelasTujuh = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $kelasDelapan = Kelas::create(['nama_kelas' => 'VIII A', 'jenjang' => 'SMP']);
        $rombelTujuh = Rombel::create([
            'kelas_id' => $kelasTujuh->id,
            'tahun_ajaran_id' => $tahunLama->id,
            'status' => 'Aktif',
        ]);
        $rombelDelapan = Rombel::create([
            'kelas_id' => $kelasDelapan->id,
            'tahun_ajaran_id' => $tahunBaru->id,
            'status' => 'Aktif',
        ]);
        $user = User::create([
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => 'password',
        ]);
        $siswa = Siswa::create([
            'user_id' => $user->id,
            'kelas_id' => $kelasDelapan->id,
            'nisn' => '0012345678',
            'nis' => 'S001',
            'jenjang' => 'SMP',
            'status' => 'Aktif',
        ]);

        AnggotaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombelTujuh->id,
            'status' => 'Naik',
        ]);
        AnggotaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombelDelapan->id,
            'status' => 'Aktif',
        ]);

        $this->assertTrue(Siswa::inKelasPadaTahunAjaran($kelasTujuh->id, $tahunLama->id)->whereKey($siswa->id)->exists());
        $this->assertFalse(Siswa::inKelasPadaTahunAjaran($kelasTujuh->id, $tahunBaru->id)->whereKey($siswa->id)->exists());
        $this->assertSame($kelasTujuh->id, $siswa->kelasPadaTahunAjaran($tahunLama->id)->id);
        $this->assertSame($rombelDelapan->id, $siswa->rombelPadaTahunAjaran($tahunBaru->id)->id);
    }

    public function test_academic_year_can_be_inferred_from_attendance_date(): void
    {
        $ganjil = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $genap = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Genap',
            'is_active' => false,
        ]);

        $this->assertSame($ganjil->id, TahunAjaran::forDate('2026-08-15')->id);
        $this->assertSame($genap->id, TahunAjaran::forDate('2027-02-15')->id);
    }
}
