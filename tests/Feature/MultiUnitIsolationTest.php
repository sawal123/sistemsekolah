<?php

namespace Tests\Feature;

use App\Livewire\Admin\Civitas\DataGuruDetail;
use App\Livewire\Admin\Civitas\DataGuruIndex;
use App\Livewire\Admin\Civitas\DataPenggunaIndex;
use App\Livewire\Admin\Civitas\DataSiswaIndex;
use App\Livewire\Admin\DataMaster\DataKelasIndex;
use App\Livewire\Admin\DataMaster\TahunAjaranIndex;
use App\Livewire\Admin\DataMaster\UnitIndex;
use App\Livewire\Admin\Keuangan\LaporanKeuanganIndex;
use App\Livewire\Admin\Keuangan\PaymentSubmissionReviewIndex;
use App\Livewire\Admin\Keuangan\TransaksiPembayaranIndex;
use App\Livewire\Components\UnitSwitcher;
use App\Models\AnggotaRombel;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\PaymentSubmission;
use App\Models\Pembayaran;
use App\Models\Rapor;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\Unit;
use App\Models\User;
use App\Support\UnitContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MultiUnitIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Unit $unitSd;

    protected Unit $unitSma;

    protected User $superAdmin;

    protected User $adminSd;

    protected User $adminSma;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super_admin', 'admin', 'admin_unit', 'guru', 'siswa', 'alumni', 'operator'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->unitSd = Unit::firstOrCreate(['kode' => 'SD'], [
            'nama' => 'SD Nusantara',
            'npsn' => '20201111',
            'tingkat_awal' => 1,
            'tingkat_akhir' => 6,
            'kepala_sekolah' => 'Hj. Siti Aminah, S.Pd.SD',
            'nip_kepala_sekolah' => '197501012000012001',
            'is_active' => true,
        ]);

        $this->unitSma = Unit::firstOrCreate(['kode' => 'SMA'], [
            'nama' => 'SMA Nusantara',
            'npsn' => '20203333',
            'tingkat_awal' => 10,
            'tingkat_akhir' => 12,
            'kepala_sekolah' => 'Dr. Budi Hartono, M.Pd',
            'nip_kepala_sekolah' => '197005051995011002',
            'is_active' => true,
        ]);

        $this->superAdmin = User::factory()->create(['unit_id' => null, 'is_active' => true]);
        $this->superAdmin->assignRole(['super_admin', 'admin']);

        $this->adminSd = User::factory()->create(['unit_id' => $this->unitSd->id, 'is_active' => true]);
        $this->adminSd->assignRole('admin_unit');

        $this->adminSma = User::factory()->create(['unit_id' => $this->unitSma->id, 'is_active' => true]);
        $this->adminSma->assignRole('admin_unit');
    }

    public function test_admin_unit_sd_cannot_view_or_mutate_sma_siswa_and_guru(): void
    {
        $kelasSd = Kelas::create(['nama_kelas' => '1A', 'jenjang' => 'SD', 'unit_id' => $this->unitSd->id]);
        $kelasSma = Kelas::create(['nama_kelas' => 'X-A', 'jenjang' => 'SMA', 'unit_id' => $this->unitSma->id]);

        $userSiswaSd = User::factory()->create(['name' => 'Siswa SD Satu', 'unit_id' => $this->unitSd->id]);
        $siswaSd = Siswa::create([
            'user_id' => $userSiswaSd->id,
            'unit_id' => $this->unitSd->id,
            'kelas_id' => $kelasSd->id,
            'nis' => 'SD001',
            'nisn' => '0011000001',
            'jenjang' => 'SD',
            'status' => 'Aktif',
        ]);

        $userSiswaSma = User::factory()->create(['name' => 'Siswa SMA Satu', 'unit_id' => $this->unitSma->id]);
        $siswaSma = Siswa::create([
            'user_id' => $userSiswaSma->id,
            'unit_id' => $this->unitSma->id,
            'kelas_id' => $kelasSma->id,
            'nis' => 'SMA001',
            'nisn' => '0012000001',
            'jenjang' => 'SMA',
            'status' => 'Aktif',
        ]);

        $userGuruSma = User::factory()->create(['name' => 'Guru SMA', 'unit_id' => $this->unitSma->id]);
        $guruSma = Guru::create([
            'user_id' => $userGuruSma->id,
            'unit_id' => $this->unitSma->id,
            'nip' => '198801012015011009',
            'jabatan' => 'Guru Tetap',
        ]);

        $this->actingAs($this->adminSd);

        // Listing siswa hanya memuat SD, tidak memuat SMA
        Livewire::test(DataSiswaIndex::class)
            ->assertSee('Siswa SD Satu')
            ->assertDontSee('Siswa SMA Satu');

        // Percobaan edit & nonaktifkan siswa SMA oleh Admin SD harus 403
        Livewire::test(DataSiswaIndex::class)
            ->call('edit', $siswaSma->id)
            ->assertForbidden();

        Livewire::test(DataSiswaIndex::class)
            ->call('delete', $siswaSma->id)
            ->assertForbidden();

        // Percobaan buka detail guru SMA oleh Admin SD harus 403
        $this->get(route('admin.civitas.data-guru.detail', $guruSma))
            ->assertForbidden();

        // Percobaan hapus guru SMA via Livewire harus 403
        Livewire::test(DataGuruIndex::class)
            ->call('confirmDelete', $guruSma->id)
            ->assertForbidden();

        // Admin SD tidak boleh mengakses Master Unit atau Website Publik Yayasan
        $this->get(route('admin.data-master.unit-sekolah'))->assertForbidden();
        $this->get(route('admin.website.pengaturan-umum'))->assertForbidden();
        $this->get(route('admin.fasilitas'))->assertForbidden();
    }

    public function test_admin_unit_sd_cannot_access_sma_keuangan_or_print_sma_pdfs(): void
    {
        $ta = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
            'status' => 'Aktif',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
        ]);

        $kelasSd = Kelas::create(['nama_kelas' => '1A', 'jenjang' => 'SD', 'unit_id' => $this->unitSd->id]);
        $kelasSma = Kelas::create(['nama_kelas' => 'X-A', 'jenjang' => 'SMA', 'unit_id' => $this->unitSma->id]);

        $uSd = User::factory()->create(['name' => 'Ani SD', 'unit_id' => $this->unitSd->id]);
        $siswaSd = Siswa::create([
            'user_id' => $uSd->id,
            'unit_id' => $this->unitSd->id,
            'kelas_id' => $kelasSd->id,
            'nis' => 'SD002',
            'nisn' => '0011000002',
            'jenjang' => 'SD',
            'status' => 'Aktif',
        ]);

        $uSma = User::factory()->create(['name' => 'Budi SMA', 'unit_id' => $this->unitSma->id]);
        $siswaSma = Siswa::create([
            'user_id' => $uSma->id,
            'unit_id' => $this->unitSma->id,
            'kelas_id' => $kelasSma->id,
            'nis' => 'SMA002',
            'nisn' => '0012000002',
            'jenjang' => 'SMA',
            'status' => 'Aktif',
        ]);

        $sppSd = Spp::create([
            'tahun_ajaran_id' => $ta->id,
            'jenjang' => 'SD',
            'unit_id' => $this->unitSd->id,
            'kategori' => 'SPP Bulanan',
            'nominal' => 250000,
            'is_active' => true,
        ]);

        $sppSma = Spp::create([
            'tahun_ajaran_id' => $ta->id,
            'jenjang' => 'SMA',
            'unit_id' => $this->unitSma->id,
            'kategori' => 'SPP Bulanan',
            'nominal' => 500000,
            'is_active' => true,
        ]);

        $tagihanSd = Tagihan::generateDariSpp($siswaSd, $sppSd, 2026, 7);
        $pembayaranSd = $tagihanSd->bayar(250000, 'Tunai', $this->adminSd->id);

        $tagihanSma = Tagihan::generateDariSpp($siswaSma, $sppSma, 2026, 7);
        $pembayaranSma = $tagihanSma->bayar(500000, 'Tunai', $this->adminSma->id);

        $submissionSma = PaymentSubmission::create([
            'siswa_id' => $siswaSma->id,
            'tagihan_id' => $tagihanSma->id,
            'submitted_by' => $uSma->id,
            'nominal' => 500000,
            'metode' => 'Transfer',
            'tanggal_transfer' => now()->toDateString(),
            'bukti_path' => 'payment_proofs/dummy.jpg',
            'status' => 'Menunggu Verifikasi',
        ]);

        $this->actingAs($this->adminSd);

        // Laporan Keuangan Admin SD hanya menghitung Rp 250.000 (bukan 750.000)
        Livewire::test(LaporanKeuanganIndex::class)
            ->assertViewHas('totalHariIni', fn ($v) => (float) $v === 250000.0);

        // Admin SD tidak boleh memilih siswa SMA di transaksi pembayaran
        Livewire::test(TransaksiPembayaranIndex::class)
            ->call('selectSiswa', $siswaSma->id)
            ->assertForbidden();

        // Admin SD tidak boleh approve pengajuan bukti bayar SMA
        Livewire::test(PaymentSubmissionReviewIndex::class)
            ->call('approve', $submissionSma->id)
            ->assertForbidden();

        // Admin SD tidak boleh cetak kuitansi SMA (403), tapi boleh cetak kuitansi SD (200)
        $this->get(route('admin.keuangan.kuitansi.cetak', ['ids' => $pembayaranSma->id]))
            ->assertForbidden();

        $this->get(route('admin.keuangan.kuitansi.cetak', ['ids' => $pembayaranSd->id]))
            ->assertOk();

        // Admin SD tidak boleh cetak e-Rapor siswa SMA (403)
        Rapor::create([
            'siswa_id' => $siswaSma->id,
            'kelas_id' => $kelasSma->id,
            'tahun_ajaran_id' => $ta->id,
        ]);

        $this->get(route('admin.kbm.e-rapor.cetak', ['siswa_id' => $siswaSma->id, 'ta' => $ta->id]))
            ->assertForbidden();
    }

    public function test_super_admin_unit_switcher_and_master_unit_management(): void
    {
        $kelasSd = Kelas::create(['nama_kelas' => '1A', 'jenjang' => 'SD', 'unit_id' => $this->unitSd->id]);
        $kelasSma = Kelas::create(['nama_kelas' => 'X-A', 'jenjang' => 'SMA', 'unit_id' => $this->unitSma->id]);

        $this->actingAs($this->superAdmin);

        // Super Admin dapat mengakses Master Unit
        $this->get(route('admin.data-master.unit-sekolah'))->assertOk();

        // Default (Semua Unit): melihat kelas SD dan SMA
        UnitContext::setSuperAdminUnit(null);
        Livewire::test(DataKelasIndex::class)
            ->assertSee('1A')
            ->assertSee('X-A');

        // Pindah switcher ke Unit SD: hanya melihat kelas SD
        Livewire::test(UnitSwitcher::class)
            ->set('selectedUnitId', (string) $this->unitSd->id);

        $this->assertEquals($this->unitSd->id, UnitContext::activeUnitId($this->superAdmin));

        Livewire::test(DataKelasIndex::class)
            ->assertSee('1A')
            ->assertDontSee('X-A');

        // Reset ke Semua Unit
        UnitContext::setSuperAdminUnit(null);
        $this->assertNull(UnitContext::activeUnitId($this->superAdmin));
    }

    public function test_sd_kenaikan_kelas_1_to_6_and_graduation_at_grade_6(): void
    {
        $taLama = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'is_active' => true,
            'status' => 'Aktif',
            'tanggal_mulai' => '2026-01-01',
            'tanggal_selesai' => '2026-06-30',
        ]);

        $taBaru = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => false,
            'status' => 'Draft',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
        ]);

        $kelas1A = Kelas::create(['nama_kelas' => '1A', 'jenjang' => 'SD', 'unit_id' => $this->unitSd->id]);
        $kelas2A = Kelas::create(['nama_kelas' => '2A', 'jenjang' => 'SD', 'unit_id' => $this->unitSd->id]);
        $kelas6A = Kelas::create(['nama_kelas' => '6A', 'jenjang' => 'SD', 'unit_id' => $this->unitSd->id]);

        $rombel1A = Rombel::create(['kelas_id' => $kelas1A->id, 'tahun_ajaran_id' => $taLama->id, 'status' => 'Aktif']);
        $rombel6A = Rombel::create(['kelas_id' => $kelas6A->id, 'tahun_ajaran_id' => $taLama->id, 'status' => 'Aktif']);

        $u1 = User::factory()->create(['unit_id' => $this->unitSd->id]);
        $siswa1 = Siswa::create([
            'user_id' => $u1->id, 'unit_id' => $this->unitSd->id, 'kelas_id' => $kelas1A->id,
            'nis' => 'SD101', 'nisn' => '0011000101', 'jenjang' => 'SD', 'status' => 'Aktif',
        ]);
        AnggotaRombel::create(['rombel_id' => $rombel1A->id, 'siswa_id' => $siswa1->id, 'status' => 'Aktif', 'tanggal_masuk' => '2026-01-05']);

        $u6 = User::factory()->create(['unit_id' => $this->unitSd->id]);
        $siswa6 = Siswa::create([
            'user_id' => $u6->id, 'unit_id' => $this->unitSd->id, 'kelas_id' => $kelas6A->id,
            'nis' => 'SD601', 'nisn' => '0011000601', 'jenjang' => 'SD', 'status' => 'Aktif',
        ]);
        AnggotaRombel::create(['rombel_id' => $rombel6A->id, 'siswa_id' => $siswa6->id, 'status' => 'Aktif', 'tanggal_masuk' => '2026-01-05']);

        $this->actingAs($this->adminSd);

        $component = Livewire::test(TahunAjaranIndex::class)
            ->call('previewKenaikanKelas', $taBaru->id);

        $preview = collect($component->get('kenaikanPreview'));
        $row1A = $preview->firstWhere('kelas_asal', '1A');
        $row6A = $preview->firstWhere('kelas_asal', '6A');

        $this->assertNotNull($row1A);
        $this->assertEquals('2A', $row1A['kelas_tujuan']);
        $this->assertFalse($row1A['is_error']);
        $this->assertFalse($row1A['is_lulus']);

        $this->assertNotNull($row6A);
        $this->assertTrue($row6A['is_lulus']);
        $this->assertFalse($row6A['is_error']);
    }

    public function test_admin_unit_cannot_create_super_admin_or_delete_roles(): void
    {
        $this->actingAs($this->adminSd);

        Livewire::test(DataPenggunaIndex::class)
            ->set('newUserSource', 'new')
            ->set('newUserName', 'Hacker Admin')
            ->set('newUserEmail', 'hacker@sekolah.com')
            ->set('newUserRole', 'super_admin')
            ->call('createUser')
            ->assertHasErrors(['newUserRole']);

        $operatorRole = Role::findByName('operator');
        Livewire::test(DataPenggunaIndex::class)
            ->call('deleteRole', $operatorRole->id)
            ->assertForbidden();
    }

    public function test_data_siswa_renders_cleanly_with_jurusan_and_modal_classes(): void
    {
        $unitSmk = Unit::firstOrCreate(
            ['kode' => 'SMK'],
            ['nama' => 'Sekolah Menengah Kejuruan', 'tingkat_awal' => 10, 'tingkat_akhir' => 12, 'is_active' => true]
        );

        $jurusan = \App\Models\Jurusan::create([
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'is_active' => true,
        ]);

        $kelasSmk = Kelas::create([
            'unit_id' => $unitSmk->id,
            'nama_kelas' => 'X RPL 1',
            'tingkat' => 10,
            'jenjang' => 'SMK',
            'jurusan_id' => $jurusan->id,
            'kapasitas' => 36,
        ]);

        $userSiswaSmk = User::factory()->create(['name' => 'Siswa SMK RPL', 'unit_id' => $unitSmk->id]);
        $siswaSmk = Siswa::create([
            'user_id' => $userSiswaSmk->id,
            'unit_id' => $unitSmk->id,
            'kelas_id' => $kelasSmk->id,
            'jurusan_id' => $jurusan->id,
            'nis' => 'SMK001',
            'nisn' => '0013000001',
            'jenjang' => 'SMK',
            'status' => 'Aktif',
        ]);

        // Super Admin mengakses DataSiswaIndex (Semua Unit atau SMK)
        $this->actingAs($this->superAdmin);

        Livewire::test(DataSiswaIndex::class)
            ->assertOk()
            ->assertSee('Siswa SMK RPL')
            ->call('openModal')
            ->set('jenjang', 'SMK')
            ->assertOk();
    }
}
