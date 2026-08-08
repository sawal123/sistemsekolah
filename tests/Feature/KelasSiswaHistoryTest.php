<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\KelasSiswa;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelasSiswaHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_roster_uses_class_membership_for_selected_academic_year(): void
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

        KelasSiswa::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelasTujuh->id,
            'tahun_ajaran_id' => $tahunLama->id,
            'status' => 'Naik',
        ]);
        KelasSiswa::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $kelasDelapan->id,
            'tahun_ajaran_id' => $tahunBaru->id,
            'status' => 'Aktif',
        ]);

        $this->assertTrue(Siswa::inKelasPadaTahunAjaran($kelasTujuh->id, $tahunLama->id)->whereKey($siswa->id)->exists());
        $this->assertFalse(Siswa::inKelasPadaTahunAjaran($kelasTujuh->id, $tahunBaru->id)->whereKey($siswa->id)->exists());
        $this->assertSame($kelasTujuh->id, $siswa->kelasPadaTahunAjaran($tahunLama->id)->id);
        $this->assertSame($kelasDelapan->id, $siswa->kelasPadaTahunAjaran($tahunBaru->id)->id);
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
