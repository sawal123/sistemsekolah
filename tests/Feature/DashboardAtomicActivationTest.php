<?php

namespace Tests\Feature;

use App\Livewire\Admin\DataMaster\TahunAjaranIndex;
use App\Models\AnggotaRombel;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardAtomicActivationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (! Role::where('name', 'siswa')->exists()) {
            Role::create(['name' => 'siswa', 'guard_name' => 'web']);
        }
    }

    public function test_salin_rombel_activates_atomically(): void
    {
        $taGanjil = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $taGenap = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Genap', 'status' => 'Draft', 'is_active' => false]);

        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $rombelGanjil = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $taGanjil->id, 'status' => 'Aktif']);

        $user = User::create(['name' => 'Siswa', 'email' => 's@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'kelas_id' => $kelas->id, 'nisn' => '008', 'nis' => 'S008', 'jenjang' => 'SMP', 'status' => 'Aktif']);
        AnggotaRombel::create(['siswa_id' => $siswa->id, 'rombel_id' => $rombelGanjil->id, 'status' => 'Aktif']);

        $component = new TahunAjaranIndex();
        $component->salinTargetTahunAjaranId = $taGenap->id;
        $component->salinRombelDariSemesterSebelumnya();

        $this->assertTrue(TahunAjaran::find($taGenap->id)->is_active);
        $this->assertFalse(TahunAjaran::find($taGanjil->id)->is_active);
        $this->assertEquals('Ditutup', TahunAjaran::find($taGanjil->id)->status);

        $rombelGenap = Rombel::where('kelas_id', $kelas->id)->where('tahun_ajaran_id', $taGenap->id)->first();
        $this->assertNotNull($rombelGenap);
        $this->assertTrue(AnggotaRombel::where('rombel_id', $rombelGenap->id)->where('siswa_id', $siswa->id)->exists());
    }

    public function test_tolak_salin_rombel_does_not_activate_target(): void
    {
        $taGanjil = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $taGenap = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Genap', 'status' => 'Draft', 'is_active' => false]);

        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $taGanjil->id, 'status' => 'Aktif']);

        $component = new TahunAjaranIndex();
        $component->salinTargetTahunAjaranId = $taGenap->id;
        $component->showSalinRombelModal = true;
        $component->tolakSalinRombel();

        $this->assertFalse(TahunAjaran::find($taGenap->id)->is_active);
        $this->assertEquals('Draft', TahunAjaran::find($taGenap->id)->status);
        $this->assertTrue(TahunAjaran::find($taGanjil->id)->is_active);
    }
}
