<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\KalenderAkademik;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Post;
use App\Models\Rapor;
use App\Models\Rombel;
use App\Models\AnggotaRombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleGuruAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'guru']);
        Role::firstOrCreate(['name' => 'siswa']);
    }

    public function test_guru_dashboard_shows_guru_specific_metrics_and_not_admin_web_security()
    {
        $ta = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2025-07-14',
            'tanggal_selesai' => '2025-12-20',
            'status' => 'Aktif',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['name' => 'Guru Budi', 'is_active' => true]);
        $guruUser->assignRole('guru');
        $guru = Guru::create(['user_id' => $guruUser->id, 'nip' => '19800101', 'jenis_kelamin' => 'L']);

        $kelas = Kelas::create(['nama_kelas' => 'X-A', 'jenjang' => 'SMA']);
        $rombel = Rombel::create([
            'kelas_id' => $kelas->id,
            'tahun_ajaran_id' => $ta->id,
            'wali_kelas_id' => $guru->id,
        ]);

        $this->actingAs($guruUser);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Dashboard Guru');
        $response->assertSee('X-A');
        $response->assertDontSee('Grafik Visit Website');
        $response->assertDontSee('Detail Pengunjung Website');
    }

    public function test_guru_cannot_update_absensi_for_class_not_assigned_as_wali_kelas()
    {
        $ta = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2025-07-14',
            'tanggal_selesai' => '2025-12-20',
            'status' => 'Aktif',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['is_active' => true]);
        $guruUser->assignRole('guru');
        $guru = Guru::create(['user_id' => $guruUser->id, 'nip' => '19800101', 'jenis_kelamin' => 'L']);

        $kelasOther = Kelas::create(['nama_kelas' => 'X-B', 'jenjang' => 'SMA']);
        $rombelOther = Rombel::create([
            'kelas_id' => $kelasOther->id,
            'tahun_ajaran_id' => $ta->id,
            'wali_kelas_id' => null, // not this guru
        ]);

        $siswaUser = User::factory()->create(['is_active' => true]);
        $siswa = Siswa::create(['user_id' => $siswaUser->id, 'nis' => '101', 'status' => 'Aktif', 'kelas_id' => $kelasOther->id, 'jenjang' => 'SMA']);
        AnggotaRombel::create([
            'rombel_id' => $rombelOther->id,
            'siswa_id' => $siswa->id,
            'status' => 'Aktif',
            'tanggal_masuk' => '2025-07-14',
        ]);

        $this->actingAs($guruUser);

        Livewire::test(\App\Livewire\Admin\Kbm\RekapAbsensiIndex::class, [
            'filterKelas' => $kelasOther->id,
            'filterBulan' => '08',
            'filterTahun' => '2025',
        ])
            ->call('updateAbsensi', $siswa->id, '2025-08-01', 'hadir')
            ->assertDispatched('notify', fn ($event, $params) => $params['type'] === 'danger' && str_contains($params['message'], 'bukan wali kelas'));

        $this->assertDatabaseMissing('absensis', [
            'siswa_id' => $siswa->id,
            'tanggal' => '2025-08-01',
        ]);
    }

    public function test_guru_cannot_print_pdf_rapor_for_unassigned_student()
    {
        $ta = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2025-07-14',
            'tanggal_selesai' => '2025-12-20',
            'status' => 'Aktif',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['is_active' => true]);
        $guruUser->assignRole('guru');
        $guru = Guru::create(['user_id' => $guruUser->id, 'nip' => '19800101', 'jenis_kelamin' => 'L']);

        $kelas = Kelas::create(['nama_kelas' => 'X-B', 'jenjang' => 'SMA']);
        $rombel = Rombel::create([
            'kelas_id' => $kelas->id,
            'tahun_ajaran_id' => $ta->id,
            'wali_kelas_id' => null,
        ]);

        $siswaUser = User::factory()->create(['is_active' => true]);
        $siswa = Siswa::create(['user_id' => $siswaUser->id, 'nis' => '102', 'status' => 'Aktif', 'kelas_id' => $kelas->id, 'jenjang' => 'SMA']);
        AnggotaRombel::create([
            'rombel_id' => $rombel->id,
            'siswa_id' => $siswa->id,
            'status' => 'Aktif',
            'tanggal_masuk' => '2025-07-14',
        ]);

        $this->actingAs($guruUser);

        $response = $this->get(route('admin.kbm.e-rapor.cetak', ['siswa_id' => $siswa->id, 'ta' => $ta->id]));
        $response->assertStatus(403);
    }

    public function test_guru_cannot_save_score_for_unassigned_subject()
    {
        $ta = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2025-07-14',
            'tanggal_selesai' => '2025-12-20',
            'status' => 'Aktif',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['is_active' => true]);
        $guruUser->assignRole('guru');
        $guru = Guru::create(['user_id' => $guruUser->id, 'nip' => '19800101', 'jenis_kelamin' => 'L']);

        $kelas = Kelas::create(['nama_kelas' => 'X-A', 'jenjang' => 'SMA']);
        $mapel = Mapel::create(['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika', 'kkm' => 75]);

        $siswaUser = User::factory()->create(['is_active' => true]);
        $siswa = Siswa::create(['user_id' => $siswaUser->id, 'nis' => '103', 'status' => 'Aktif', 'kelas_id' => $kelas->id, 'jenjang' => 'SMA']);

        $this->actingAs($guruUser);

        // Guru does not have Jadwal for this kelas and mapel
        Livewire::test(\App\Livewire\Admin\Kbm\ManajemenNilaiIndex::class, [
            'filterKelas' => $kelas->id,
            'filterMapel' => $mapel->id,
            'filterTahunAjaran' => $ta->id,
        ])
            ->call('updatePts', $siswa->id, 85)
            ->assertDispatched('notify', fn ($event, $params) => $params['type'] === 'danger' && str_contains($params['message'], 'tidak memiliki jadwal'));

        $this->assertDatabaseMissing('nilais', [
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
        ]);
    }

    public function test_guru_cannot_create_schedule_in_jadwal_pelajaran()
    {
        $guruUser = User::factory()->create(['is_active' => true]);
        $guruUser->assignRole('guru');

        $this->actingAs($guruUser);

        Livewire::test(\App\Livewire\Admin\Kbm\JadwalPelajaranIndex::class)
            ->call('openModalForCreate')
            ->assertStatus(403);
    }

    public function test_guru_cannot_add_holiday_in_kalender_akademik()
    {
        $guruUser = User::factory()->create(['is_active' => true]);
        $guruUser->assignRole('guru');

        $this->actingAs($guruUser);

        Livewire::test(\App\Livewire\Admin\Akademik\KalenderAkademikIndex::class)
            ->set('tanggal', '2026-05-01')
            ->set('keterangan', 'Hari Buruh')
            ->set('jenis_libur', 'nasional')
            ->call('save')
            ->assertStatus(403);
    }
}

