<?php

namespace Tests\Feature;

use App\Livewire\Admin\Civitas\DataGuruDetail;
use App\Livewire\Admin\Civitas\DataGuruIndex;
use App\Livewire\Admin\Civitas\DataPenggunaIndex;
use App\Livewire\Admin\Civitas\DataSiswaIndex;
use App\Models\AnggotaRombel;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CivitasAkademikTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'guru']);
        Role::firstOrCreate(['name' => 'siswa']);
        Role::firstOrCreate(['name' => 'alumni']);
        Role::firstOrCreate(['name' => 'operator']);
    }

    public function test_delete_siswa_updates_anggota_rombel_and_deactivates_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $ta = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
            'status' => 'Aktif',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'X-A',
            'jenjang' => 'SMA',
        ]);

        $rombel = Rombel::create([
            'kelas_id' => $kelas->id,
            'tahun_ajaran_id' => $ta->id,
            'status' => 'Aktif',
        ]);

        $siswaUser = User::factory()->create(['is_active' => true]);
        $siswaUser->assignRole('siswa');

        $siswa = Siswa::create([
            'user_id' => $siswaUser->id,
            'kelas_id' => $kelas->id,
            'nis' => '1001',
            'nisn' => '0010010011',
            'jenjang' => 'SMA',
            'status' => 'Aktif',
        ]);

        $anggota = AnggotaRombel::create([
            'rombel_id' => $rombel->id,
            'siswa_id' => $siswa->id,
            'status' => 'Aktif',
            'tanggal_masuk' => '2026-07-01',
            'tanggal_keluar' => null,
        ]);

        $this->actingAs($admin);

        Livewire::test(DataSiswaIndex::class)
            ->call('delete', $siswa->id)
            ->assertDispatched('notify');

        $this->assertEquals('Pindah', $siswa->fresh()->status);
        $this->assertFalse((bool) $siswaUser->fresh()->is_active);
        $this->assertEquals('Pindah', $anggota->fresh()->status);
        $this->assertNotNull($anggota->fresh()->tanggal_keluar);
    }

    public function test_reset_password_siswa(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $siswaUser = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $siswa = Siswa::create([
            'user_id' => $siswaUser->id,
            'nis' => '1002',
            'nisn' => '0010020022',
            'jenjang' => 'SMA',
            'tanggal_lahir' => '2008-04-20',
            'status' => 'Aktif',
        ]);

        $this->actingAs($admin);

        Livewire::test(DataSiswaIndex::class)
            ->call('resetPassword', $siswa->id)
            ->assertDispatched('notify');

        $this->assertTrue(Hash::check('20042008', $siswaUser->fresh()->password));
    }

    public function test_delete_guru_blocked_when_active_wali_kelas_or_has_schedule(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $ta = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
            'status' => 'Aktif',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
        ]);

        $guruUser = User::factory()->create(['name' => 'Pak Guru Budi']);
        $guruUser->assignRole('guru');

        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'nip' => '198501012010011001',
            'jabatan' => 'Guru Tetap',
            'tanggal_lahir' => '1985-01-01',
        ]);

        $kelas = Kelas::create([
            'nama_kelas' => 'XII-IPA-1',
            'jenjang' => 'SMA',
            'wali_kelas_id' => $guru->id,
        ]);

        Rombel::create([
            'kelas_id' => $kelas->id,
            'tahun_ajaran_id' => $ta->id,
            'wali_kelas_id' => $guru->id,
            'status' => 'Aktif',
        ]);

        $this->actingAs($admin);

        // Harus ditolak karena wali kelas aktif
        Livewire::test(DataGuruIndex::class)
            ->set('confirmDeleteId', $guru->id)
            ->call('delete')
            ->assertDispatched('notify', fn ($event, $params) => $params['type'] === 'error');

        $this->assertDatabaseHas('gurus', ['id' => $guru->id]);
        $this->assertDatabaseHas('users', ['id' => $guruUser->id]);
    }

    public function test_guru_detail_filters_active_academic_year_schedules(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $ta1 = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
            'status' => 'Aktif',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
        ]);

        $ta2 = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'is_active' => false,
            'status' => 'Ditutup',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-06-30',
        ]);

        $guruUser = User::factory()->create();
        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'nip' => '198701012011011002',
            'jabatan' => 'Guru Tetap',
        ]);

        $kelas = Kelas::create(['nama_kelas' => 'XI-IPA', 'jenjang' => 'SMA']);
        $mapel = Mapel::create(['kode_mapel' => 'MAT-01', 'nama_mapel' => 'Matematika']);

        // Jadwal aktif
        Jadwal::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'guru_id' => $guru->id,
            'tahun_ajaran_id' => $ta1->id,
            'hari' => 'Senin',
            'jam_mulai' => '07:00',
            'jam_selesai' => '08:30',
        ]);

        // Jadwal lampau
        Jadwal::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'guru_id' => $guru->id,
            'tahun_ajaran_id' => $ta2->id,
            'hari' => 'Selasa',
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:30',
        ]);

        $this->actingAs($admin);

        $component = Livewire::test(DataGuruDetail::class, ['guru' => $guru]);

        $jadwals = $component->get('guru')->jadwals;
        $this->assertCount(1, $jadwals);
        $this->assertEquals($ta1->id, $jadwals->first()->tahun_ajaran_id);
    }

    public function test_create_user_in_data_pengguna_creates_user_and_guru_record_if_role_guru(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        Livewire::test(DataPenggunaIndex::class)
            ->set('newUserName', 'Staff Tata Usaha Baru')
            ->set('newUserEmail', 'tu.baru@sekolah.sch.id')
            ->set('newUserPassword', 'secret123')
            ->set('newUserRole', 'operator')
            ->set('newUserStaffPosition', 'Kepala Tata Usaha')
            ->call('createUser')
            ->assertDispatched('notify');

        $tuUser = User::where('email', 'tu.baru@sekolah.sch.id')->first();
        $this->assertNotNull($tuUser);
        $this->assertEquals('Kepala Tata Usaha', $tuUser->staff_position);
        $this->assertTrue($tuUser->hasRole('operator'));

        // Tes pembuatan user role guru
        Livewire::test(DataPenggunaIndex::class)
            ->set('newUserName', 'Guru Pengajar Baru')
            ->set('newUserEmail', 'guru.baru@sekolah.sch.id')
            ->set('newUserPassword', 'secret123')
            ->set('newUserRole', 'guru')
            ->call('createUser')
            ->assertDispatched('notify');

        $newGuruUser = User::where('email', 'guru.baru@sekolah.sch.id')->first();
        $this->assertNotNull($newGuruUser);
        $this->assertTrue($newGuruUser->hasRole('guru'));
        $this->assertNotNull($newGuruUser->guru);
    }

    public function test_delete_core_roles_is_blocked_in_backend(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $guruRole = Role::findByName('guru');
        $siswaRole = Role::findByName('siswa');

        Livewire::test(DataPenggunaIndex::class)
            ->call('deleteRole', $guruRole->id)
            ->assertHasErrors(['action']);

        $this->assertDatabaseHas('roles', ['name' => 'guru']);

        Livewire::test(DataPenggunaIndex::class)
            ->call('deleteRole', $siswaRole->id)
            ->assertHasErrors(['action']);

        $this->assertDatabaseHas('roles', ['name' => 'siswa']);
    }

    public function test_toggle_status_guru_toggles_user_is_active(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $guruUser = User::factory()->create(['is_active' => true]);
        $guru = Guru::create([
            'user_id' => $guruUser->id,
            'nip' => '199001012015011003',
            'jabatan' => 'Guru Tetap',
        ]);

        $this->actingAs($admin);

        Livewire::test(DataGuruIndex::class)
            ->call('toggleStatus', $guru->id);

        $this->assertFalse((bool) $guruUser->fresh()->is_active);

        Livewire::test(DataGuruIndex::class)
            ->call('toggleStatus', $guru->id);

        $this->assertTrue((bool) $guruUser->fresh()->is_active);
    }
}

