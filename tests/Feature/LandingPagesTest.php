<?php

namespace Tests\Feature;

use App\Livewire\Admin\DataMaster\JurusanIndex;
use App\Livewire\Admin\Keuangan\MasterSppIndex;
use App\Livewire\Admin\Website\StructureOrganizationIndex;
use App\Livewire\Landing\PpdbPage;
use App\Models\Jurusan;
use App\Models\PpdbGelombang;
use App\Models\Setting;
use App\Models\Spp;
use App\Models\TahunAjaran;
use Database\Seeders\DummyDataSeeder;
use Database\Seeders\RoleAndUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LandingPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_landing_pages_are_accessible(): void
    {
        foreach (
            [
                '/tentang',
                '/akademik/ipa',
                '/akademik/ips',
                '/akademik/bahasa',
                '/akademik/kurikulum',
                '/kepegawaian/guru',
                '/kepegawaian/tata-usaha',
                '/kepegawaian/struktur-organisasi',
                '/fasilitas-sekolah',
                '/blog',
                '/ppdb',
                '/ppdb/syarat',
                '/ppdb/jalur',
                '/ppdb/biaya',
                '/ppdb/daftar',
                '/kontak',
            ] as $uri
        ) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_public_ppdb_registration_is_saved_for_the_open_wave(): void
    {
        $wave = PpdbGelombang::create([
            'tahun_pendaftaran' => now()->year,
            'nama_gelombang' => 'Gelombang Tes',
            'tanggal_mulai' => today(),
            'tanggal_selesai' => today()->addMonth(),
            'status' => 'Dibuka',
        ]);

        Livewire::test(PpdbPage::class, ['section' => 'daftar'])
            ->set('nama_lengkap', 'Calon Siswa')
            ->set('nisn', '0012345678')
            ->set('tanggal_lahir', '2010-01-01')
            ->set('jenis_kelamin', 'Laki-Laki')
            ->set('no_hp', '081234567890')
            ->set('alamat', 'Jl. Pendidikan No. 1')
            ->set('sekolah_asal', 'SMP Negeri 1')
            ->set('tahun_lulus', (string) now()->year)
            ->set('jurusan_pilihan_1', 'IPA')
            ->set('no_telp_ortu', '081234567891')
            ->call('register')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pendaftaran_murid_barus', [
            'ppdb_gelombang_id' => $wave->id,
            'nama_lengkap' => 'Calon Siswa',
            'nisn' => '0012345678',
            'status' => 'Baru',
        ]);
    }

    public function test_ppdb_only_displays_active_public_fees_for_the_active_academic_year(): void
    {
        $activeYear = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'status' => 'Aktif',
            'is_active' => true,
        ]);

        $oldYear = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'status' => 'Ditutup',
            'is_active' => false,
        ]);

        Spp::create([
            'tahun_ajaran_id' => $activeYear->id,
            'jenjang' => 'SMA',
            'kategori' => 'SPP Bulanan',
            'nominal' => 450000,
            'is_active' => true,
        ]);

        Spp::create([
            'tahun_ajaran_id' => $activeYear->id,
            'jenjang' => 'Semua',
            'kategori' => 'Uang Bangunan',
            'nominal' => 1500000,
            'is_active' => true,
        ]);

        Spp::create([
            'tahun_ajaran_id' => $activeYear->id,
            'jenjang' => 'SMA',
            'kategori' => 'Uang Seragam',
            'nominal' => 700000,
            'is_active' => true,
        ]);

        Spp::create([
            'tahun_ajaran_id' => $oldYear->id,
            'jenjang' => 'SMA',
            'kategori' => 'SPP Bulanan',
            'nominal' => 999000,
            'is_active' => true,
        ]);

        Livewire::test(PpdbPage::class, ['section' => 'biaya'])
            ->assertSee('Rp 450.000')
            ->assertSee('Rp 1.500.000')
            ->assertDontSee('Rp 700.000')
            ->assertDontSee('Rp 999.000');
    }

    public function test_admin_fee_form_can_create_an_active_building_fee(): void
    {
        $academicYear = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'status' => 'Aktif',
            'is_active' => true,
        ]);

        Livewire::test(MasterSppIndex::class)
            ->set('tahun_ajaran_id', (string) $academicYear->id)
            ->set('jenjang', 'Semua')
            ->set('kategori', 'Uang Bangunan')
            ->set('nominal', '1750000')
            ->set('keterangan', 'Dibayar satu kali per tahun ajaran')
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('spps', [
            'tahun_ajaran_id' => $academicYear->id,
            'jenjang' => 'Semua',
            'kategori' => 'Uang Bangunan',
            'nominal' => 1750000,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_a_vocational_major(): void
    {
        Livewire::test(JurusanIndex::class)
            ->set('kode', 'RPL')
            ->set('nama', 'Rekayasa Perangkat Lunak')
            ->set('deskripsi', 'Pengembangan perangkat lunak')
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('jurusans', [
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);
    }

    public function test_smk_fee_can_be_different_by_major_and_academic_year(): void
    {
        $rpl = Jurusan::create(['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak', 'is_active' => true]);
        $yearOne = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $yearTwo = TahunAjaran::create(['tahun' => '2027/2028', 'semester' => 'Ganjil', 'status' => 'Draft', 'is_active' => false]);

        foreach ([[$yearOne, 550000], [$yearTwo, 625000]] as [$year, $nominal]) {
            Livewire::test(MasterSppIndex::class)
                ->set('tahun_ajaran_id', (string) $year->id)
                ->set('jenjang', 'SMK')
                ->set('jurusan_id', (string) $rpl->id)
                ->set('kategori', 'SPP Bulanan')
                ->set('nominal', (string) $nominal)
                ->set('is_active', true)
                ->call('save')
                ->assertHasNoErrors();
        }

        $this->assertDatabaseHas('spps', [
            'tahun_ajaran_id' => $yearOne->id,
            'jurusan_id' => $rpl->id,
            'nominal' => 550000,
        ]);
        $this->assertDatabaseHas('spps', [
            'tahun_ajaran_id' => $yearTwo->id,
            'jurusan_id' => $rpl->id,
            'nominal' => 625000,
        ]);
    }

    public function test_dummy_seeder_creates_smk_majors_classes_students_and_yearly_fees(): void
    {
        $this->seed([
            RoleAndUserSeeder::class,
            DummyDataSeeder::class,
        ]);

        $rpl = Jurusan::where('kode', 'RPL')->firstOrFail();

        $this->assertDatabaseHas('kelas', [
            'jenjang' => 'SMK',
            'jurusan_id' => $rpl->id,
        ]);
        $this->assertDatabaseHas('siswas', [
            'jenjang' => 'SMK',
            'jurusan_id' => $rpl->id,
        ]);
        $this->assertDatabaseCount('spps', 9);
        $this->assertSame(
            2,
            Spp::where('jenjang', 'SMK')
                ->where('jurusan_id', $rpl->id)
                ->where('kategori', 'SPP Bulanan')
                ->distinct('tahun_ajaran_id')
                ->count('tahun_ajaran_id')
        );
    }

    public function test_admin_can_upload_organization_structure_image(): void
    {
        Storage::fake('public');

        Livewire::test(StructureOrganizationIndex::class)
            ->set('organizationImage', UploadedFile::fake()->image('struktur.png', 1600, 900))
            ->call('save')
            ->assertHasNoErrors();

        $path = Setting::where('key', 'organization_structure_image')->value('value');

        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }
}
