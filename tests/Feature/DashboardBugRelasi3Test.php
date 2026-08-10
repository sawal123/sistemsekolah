<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\AnggotaRombel;
use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardBugRelasi3Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles untuk test PPDB (assignRole memerlukan role)
        if (! Role::where('name', 'siswa')->exists()) {
            Role::create(['name' => 'siswa', 'guard_name' => 'web']);
        }
        if (! Role::where('name', 'guru')->exists()) {
            Role::create(['name' => 'guru', 'guard_name' => 'web']);
        }
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: Migration order & potongan
    // ═══════════════════════════════════════════════════════════

    public function test_tagihans_table_has_potongan_column(): void
    {
        $this->assertTrue(Schema::hasColumn('tagihans', 'potongan'));
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: TahunAjaran unique constraint with preflight
    // ═══════════════════════════════════════════════════════════

    public function test_tahun_ajaran_unique_prevents_duplicate(): void
    {
        TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Draft', 'is_active' => false]);
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: Historical roster includes Lulus/Pindah students
    // ═══════════════════════════════════════════════════════════

    public function test_in_kelas_pada_tahun_ajaran_includes_graduated_students(): void
    {
        $ta = TahunAjaran::create(['tahun' => '2025/2026', 'semester' => 'Ganjil', 'status' => 'Ditutup', 'is_active' => false]);
        $kelas = Kelas::create(['nama_kelas' => 'IX A', 'jenjang' => 'SMP']);
        $rombel = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $ta->id, 'status' => 'Ditutup']);

        $user = User::create(['name' => 'Lulusan', 'email' => 'lulus@test.com', 'password' => 'password']);
        $siswa = Siswa::create([
            'user_id' => $user->id,
            'kelas_id' => $kelas->id,
            'nisn' => '009',
            'nis' => 'S009',
            'jenjang' => 'SMP',
            'status' => 'Lulus',
            'tahun_lulus' => 2025,
        ]);
        AnggotaRombel::create(['siswa_id' => $siswa->id, 'rombel_id' => $rombel->id, 'status' => 'Aktif']);

        $result = Siswa::inKelasPadaTahunAjaran($kelas->id, $ta->id)->get();
        $this->assertCount(1, $result);
        $this->assertEquals($siswa->id, $result->first()->id);
    }

    public function test_aktif_di_kelas_filters_by_membership_not_student_status(): void
    {
        $ta = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $rombel = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $ta->id, 'status' => 'Aktif']);

        // Siswa aktif
        $u1 = User::create(['name' => 'Aktif', 'email' => 'aktif@test.com', 'password' => 'p']);
        $s1 = Siswa::create(['user_id' => $u1->id, 'kelas_id' => $kelas->id, 'nisn' => '001', 'nis' => 'S001', 'jenjang' => 'SMP', 'status' => 'Aktif']);
        AnggotaRombel::create(['siswa_id' => $s1->id, 'rombel_id' => $rombel->id, 'status' => 'Aktif']);

        // Siswa pindah (masih punya membership aktif? tidak — sudah Pindah)
        $u2 = User::create(['name' => 'Pindah', 'email' => 'pindah@test.com', 'password' => 'p']);
        $s2 = Siswa::create(['user_id' => $u2->id, 'kelas_id' => $kelas->id, 'nisn' => '002', 'nis' => 'S002', 'jenjang' => 'SMP', 'status' => 'Pindah']);
        AnggotaRombel::create(['siswa_id' => $s2->id, 'rombel_id' => $rombel->id, 'status' => 'Pindah', 'tanggal_keluar' => now()]);

        $result = Siswa::aktifDiKelasPadaTahunAjaran($kelas->id, $ta->id)->get();
        $this->assertCount(1, $result);
        $this->assertEquals($s1->id, $result->first()->id);
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: inKelasPadaTanggal tanpa filter status
    // ═══════════════════════════════════════════════════════════

    public function test_in_kelas_pada_tanggal_uses_date_range_not_status(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $ta = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $rombel = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $ta->id, 'status' => 'Aktif']);

        $user = User::create(['name' => 'Budi', 'email' => 'budi@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'kelas_id' => $kelas->id, 'nisn' => '003', 'nis' => 'S003', 'jenjang' => 'SMP', 'status' => 'Pindah']);
        AnggotaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel->id,
            'status' => 'Pindah',
            'tanggal_masuk' => '2026-08-01',
            'tanggal_keluar' => '2026-10-15',
        ]);

        // September: masih dalam range → muncul
        $result = Siswa::inKelasPadaTanggal($kelas->id, '2026-09-01')->get();
        $this->assertCount(1, $result);

        // November: di luar range → tidak muncul
        $result = Siswa::inKelasPadaTanggal($kelas->id, '2026-11-01')->get();
        $this->assertCount(0, $result);
    }

    public function test_in_kelas_pada_tanggal_handles_null_tanggal_masuk(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $ta = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $rombel = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $ta->id, 'status' => 'Aktif']);

        $user = User::create(['name' => 'Legacy', 'email' => 'legacy@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'kelas_id' => $kelas->id, 'nisn' => '0031', 'nis' => 'S031', 'jenjang' => 'SMP', 'status' => 'Aktif']);
        // Membership lama tanpa tanggal_masuk (legacy) — dianggap sudah di kelas sejak awal
        AnggotaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel->id,
            'status' => 'Aktif',
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
        ]);

        $result = Siswa::inKelasPadaTanggal($kelas->id, '2026-08-01')->get();
        $this->assertCount(1, $result);
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: Kenaikan kelas bedakan Lulus vs tidak ditemukan
    //  (via reflection — hindari render view yang bermasalah)
    // ═══════════════════════════════════════════════════════════

    public function test_cari_kelas_tujuan_lulus_vs_tidak_ditemukan(): void
    {
        $component = new \App\Livewire\Admin\DataMaster\TahunAjaranIndex();

        $kelas7 = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $kelas8 = Kelas::create(['nama_kelas' => 'VIII A', 'jenjang' => 'SMP']);
        $kelas9 = Kelas::create(['nama_kelas' => 'IX A', 'jenjang' => 'SMP']);
        $semua = Kelas::all();

        $ref = new \ReflectionMethod($component, 'cariKelasTujuan');
        $ref->setAccessible(true);

        // VII → VIII (ok)
        $r1 = $ref->invoke($component, $kelas7, $semua);
        $this->assertEquals('ok', $r1['disposition']);
        $this->assertEquals($kelas8->id, $r1['kelas']->id);

        // VIII → IX (ok)
        $r2 = $ref->invoke($component, $kelas8, $semua);
        $this->assertEquals('ok', $r2['disposition']);
        $this->assertEquals($kelas9->id, $r2['kelas']->id);

        // IX → Lulus (bukan error)
        $r3 = $ref->invoke($component, $kelas9, $semua);
        $this->assertEquals('lulus', $r3['disposition']);
        $this->assertNull($r3['kelas']);
    }

    public function test_cari_kelas_tujuan_tidak_ditemukan_saat_kelas_tujuan_tidak_ada(): void
    {
        $component = new \App\Livewire\Admin\DataMaster\TahunAjaranIndex();

        // VIII A ada, tapi IX A TIDAK ada di database
        $kelas8 = Kelas::create(['nama_kelas' => 'VIII A', 'jenjang' => 'SMP']);
        $semua = Kelas::all();

        $ref = new \ReflectionMethod($component, 'cariKelasTujuan');
        $ref->setAccessible(true);

        $r = $ref->invoke($component, $kelas8, $semua);
        $this->assertEquals('tidak_ditemukan', $r['disposition']);
        $this->assertNull($r['kelas']);
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: Tagihan refreshStatus dengan sisa_tagihan
    // ═══════════════════════════════════════════════════════════

    public function test_tagihan_refresh_status_uses_sisa_tagihan(): void
    {
        $user = User::create(['name' => 'Siswa', 'email' => 's@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'nisn' => '004', 'nis' => 'S004', 'jenjang' => 'SMP', 'status' => 'Aktif']);

        $ta = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $spp = Spp::create(['tahun_ajaran_id' => $ta->id, 'jenjang' => 'SMP', 'kategori' => 'SPP Bulanan', 'nominal' => 500000, 'is_active' => true]);

        // Tagihan dengan potongan — bayar sebagian
        $tagihan = Tagihan::create([
            'siswa_id' => $siswa->id,
            'spp_id' => $spp->id,
            'jenis_biaya' => 'SPP Bulanan',
            'tahun' => 2026,
            'bulan' => 8,
            'nominal' => 500000,
            'potongan' => 100000,
            'status' => 'Belum Lunas',
        ]);

        // Bayar 400.000 (nominal - potongan = lunas)
        $tagihan->bayar(400000);
        $tagihan->refresh();

        $this->assertEquals('Lunas', $tagihan->status);
        $this->assertEquals(0.0, $tagihan->sisa_tagihan);
    }

    public function test_tagihan_overpayment_dicegah(): void
    {
        $user = User::create(['name' => 'Siswa', 'email' => 's2@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'nisn' => '005', 'nis' => 'S005', 'jenjang' => 'SMP', 'status' => 'Aktif']);

        $tagihan = Tagihan::create([
            'siswa_id' => $siswa->id,
            'spp_id' => null,
            'jenis_biaya' => 'SPP Bulanan',
            'tahun' => 2026,
            'bulan' => 8,
            'nominal' => 500000,
            'potongan' => 0,
            'status' => 'Belum Lunas',
        ]);

        $tagihan->bayar(200000);

        $this->expectException(\InvalidArgumentException::class);
        $tagihan->bayar(400000); // 200k + 400k > 500k
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: PPDB tanpa NISN
    // ═══════════════════════════════════════════════════════════

    public function test_ppdb_konversi_tanpa_nisn(): void
    {
        $ta = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $gelombang = \App\Models\PpdbGelombang::create([
            'tahun_pendaftaran' => 2026,
            'nama_gelombang' => 'Gelombang 1',
            'status' => 'Dibuka',
        ]);

        $ppdb = \App\Models\PendaftaranMuridBaru::create([
            'ppdb_gelombang_id' => $gelombang->id,
            'nama_lengkap' => 'Tanpa NISN',
            'nisn' => null,
            'status' => 'Diterima',
            'jenjang_pilihan' => 'SMP',
        ]);

        $component = \Livewire\Livewire::test(\App\Livewire\Admin\Ppdb\PendaftaranMuridBaruIndex::class);
        $component->call('konversiKeSiswa', $ppdb->id);

        $siswa = Siswa::where('user_id', User::where('name', 'Tanpa NISN')->first()->id)->first();
        $this->assertNotNull($siswa);
        $this->assertNull($siswa->nisn);
        $this->assertNotNull($siswa->nis);
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: Backfill pembayaran_spps dengan potongan
    // ═══════════════════════════════════════════════════════════

    public function test_backfill_with_potongan_is_consistent(): void
    {
        // Setup data lama
        $ta = TahunAjaran::create(['tahun' => '2025/2026', 'semester' => 'Ganjil', 'status' => 'Ditutup', 'is_active' => false]);
        $spp = Spp::create(['tahun_ajaran_id' => $ta->id, 'jenjang' => 'SMP', 'kategori' => 'SPP Bulanan', 'nominal' => 500000, 'is_active' => true]);

        $user = User::create(['name' => 'Old', 'email' => 'old@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'nisn' => '006', 'nis' => 'S006', 'jenjang' => 'SMP', 'status' => 'Lulus']);

        PembayaranSpp::create([
            'siswa_id' => $siswa->id,
            'spp_id' => $spp->id,
            'user_id' => null,
            'tahun' => 2025,
            'bulan' => 7,
            'tanggal_bayar' => '2025-07-15',
            'jumlah_bayar' => 500000,
            'potongan' => 100000,
            'status' => 'Lunas',
        ]);

        // Simulasikan hasil backfill (mencerminkan logika migration 000006):
        // tagihan.nominal = full SPP, tagihan.potongan = diskon, pembayaran = netto
        $netto = 500000 - 100000;
        $tagihan = Tagihan::create([
            'siswa_id' => $siswa->id,
            'spp_id' => $spp->id,
            'jenis_biaya' => 'SPP Bulanan',
            'tahun' => 2025,
            'bulan' => 7,
            'nominal' => 500000,
            'potongan' => 100000,
            'status' => 'Lunas',
            'keterangan' => 'Migrasi dari sistem lama',
        ]);
        $tagihan->pembayarans()->create([
            'tanggal_bayar' => '2025-07-15',
            'nominal' => $netto,
            'metode' => 'Tunai',
            'petugas_id' => null,
            'keterangan' => 'Migrasi dari sistem lama',
        ]);

        // Invariant: sisa = 0, status Lunas, total terbayar = netto
        $tagihan->refresh();
        $this->assertEquals('Lunas', $tagihan->status);
        $this->assertEquals(0.0, $tagihan->sisa_tagihan);
        $this->assertEquals($netto, $tagihan->total_terbayar);
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: Tunggakan partial dihitung dengan sisa_tagihan
    // ═══════════════════════════════════════════════════════════

    public function test_tunggakan_partial_uses_sisa_tagihan(): void
    {
        $ta = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $spp = Spp::create(['tahun_ajaran_id' => $ta->id, 'jenjang' => 'SMP', 'kategori' => 'SPP Bulanan', 'nominal' => 500000, 'is_active' => true]);
        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $rombel = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $ta->id, 'status' => 'Aktif']);

        $user = User::create(['name' => 'Partial', 'email' => 'part@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'kelas_id' => $kelas->id, 'nisn' => '007', 'nis' => 'S007', 'jenjang' => 'SMP', 'status' => 'Aktif']);
        AnggotaRombel::create(['siswa_id' => $siswa->id, 'rombel_id' => $rombel->id, 'status' => 'Aktif']);

        // Tagihan Agustus: sudah bayar 200rb, sisa 300rb
        $tagihan = Tagihan::create([
            'siswa_id' => $siswa->id,
            'spp_id' => $spp->id,
            'jenis_biaya' => 'SPP Bulanan',
            'tahun' => 2026,
            'bulan' => 8,
            'nominal' => 500000,
            'potongan' => 0,
            'status' => 'Lunas Sebagian',
        ]);
        $tagihan->bayar(200000);

        $this->assertEquals(300000.0, $tagihan->fresh()->sisa_tagihan);
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: Aktivasi tidak bypass workflow
    //  (atomik via DashboardAtomicActivationTest; ini via instance langsung)
    // ═══════════════════════════════════════════════════════════

    public function test_execute_kenaikan_kelas_blocked_when_preview_has_error(): void
    {
        $taLama = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $taBaru = TahunAjaran::create(['tahun' => '2027/2028', 'semester' => 'Ganjil', 'status' => 'Draft', 'is_active' => false]);

        $user = User::create(['name' => 'Siswa8', 'email' => 's8@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'nisn' => '0098', 'nis' => 'S098', 'jenjang' => 'SMP', 'status' => 'Aktif']);

        $component = new \App\Livewire\Admin\DataMaster\TahunAjaranIndex();
        $component->targetTahunAjaranId = $taBaru->id;
        $component->kenaikanPreview = [
            [
                'kelas_asal' => 'VIII A',
                'kelas_tujuan' => null,
                'kelas_tujuan_id' => null,
                'is_lulus' => false,
                'is_error' => true,
                'jumlah_siswa' => 1,
                'siswa' => [['id' => $siswa->id, 'nama' => 'Siswa8']],
            ],
        ];

        $component->executeKenaikanKelas();

        // Blok: target TIDAK aktif, siswa TIDAK diluluskan/dipindah
        $this->assertFalse(TahunAjaran::find($taBaru->id)->is_active);
        $this->assertTrue(TahunAjaran::find($taLama->id)->is_active);
        $this->assertEquals('Aktif', Siswa::find($siswa->id)->status);
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: Tarif SPP lintas semester (forDate per bulan)
    // ═══════════════════════════════════════════════════════════

    public function test_spp_tarif_lintas_semester_per_bulan(): void
    {
        // 2025/2026 Genap = Jan–Jun 2026 (tarif 400k)
        $taGenap = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'tanggal_mulai' => '2026-01-05',
            'tanggal_selesai' => '2026-06-30',
            'status' => 'Ditutup',
            'is_active' => false,
        ]);
        // 2026/2027 Ganjil = Jul–Des 2026 (tarif 500k)
        $taGanjil = TahunAjaran::create([
            'tahun' => '2026/2027',
            'semester' => 'Ganjil',
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-12-31',
            'status' => 'Aktif',
            'is_active' => true,
        ]);

        $sppGenap = Spp::create(['tahun_ajaran_id' => $taGenap->id, 'jenjang' => 'SMP', 'kategori' => 'SPP Bulanan', 'nominal' => 400000, 'is_active' => true]);
        $sppGanjil = Spp::create(['tahun_ajaran_id' => $taGanjil->id, 'jenjang' => 'SMP', 'kategori' => 'SPP Bulanan', 'nominal' => 500000, 'is_active' => true]);

        $user = User::create(['name' => 'Tarif', 'email' => 'tarif@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'nisn' => '0090', 'nis' => 'S090', 'jenjang' => 'SMP', 'status' => 'Aktif']);

        $component = \Livewire\Livewire::test(\App\Livewire\Admin\Keuangan\TransaksiPembayaranIndex::class);
        $component->set('selectedSiswaId', $siswa->id);
        $component->set('selectedSiswaData', [
            'id' => $siswa->id,
            'nama' => 'Tarif',
            'nisn' => $siswa->nisn,
            'nis' => $siswa->nis,
            'jenjang' => 'SMP',
            'jurusan_id' => null,
            'kelas' => 'Belum Ada Kelas',
            'status' => 'Aktif',
        ]);
        $component->set('selectedTahun', 2026);
        $component->call('loadPaymentMatrix');

        $matrix = $component->get('sppMatrix');

        // Harus ada 2 SPP bulanan (Genap & Ganjil)
        $bulanan = collect($matrix)->where('is_bulanan', true);
        $this->assertCount(2, $bulanan);

        // Tagihan bulan 1 (Jan) memakai SPP Genap 400k
        $janTagihan = Tagihan::where('siswa_id', $siswa->id)->where('bulan', 1)->first();
        $this->assertNotNull($janTagihan);
        $this->assertEquals(400000, (float) $janTagihan->nominal);

        // Tagihan bulan 7 (Jul) memakai SPP Ganjil 500k
        $julTagihan = Tagihan::where('siswa_id', $siswa->id)->where('bulan', 7)->first();
        $this->assertNotNull($julTagihan);
        $this->assertEquals(500000, (float) $julTagihan->nominal);
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: Mutasi kelas tengah bulan (absensi)
    // ═══════════════════════════════════════════════════════════

    public function test_in_kelas_pada_rentang_tanggal_overlap(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $ta = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $rombel = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $ta->id, 'status' => 'Aktif']);

        $user = User::create(['name' => 'Budi', 'email' => 'budi2@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'kelas_id' => $kelas->id, 'nisn' => '0099', 'nis' => 'S099', 'jenjang' => 'SMP', 'status' => 'Aktif']);
        // Masuk awal bulan, pindah keluar pertengahan bulan (overlap bulan tersebut)
        AnggotaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel->id,
            'status' => 'Pindah',
            'tanggal_masuk' => '2026-08-01',
            'tanggal_keluar' => '2026-08-15',
        ]);

        // Overlap bulan Agustus (1–31) → Budi masuk roster
        $result = Siswa::inKelasPadaRentangTanggal($kelas->id, '2026-08-01', '2026-08-31')->get();
        $this->assertCount(1, $result);

        // Bulan September → tidak overlap → tidak muncul
        $result = Siswa::inKelasPadaRentangTanggal($kelas->id, '2026-09-01', '2026-09-30')->get();
        $this->assertCount(0, $result);
    }

    public function test_rekap_absensi_mutasi_tengah_bulan_validasi_per_tanggal(): void
    {
        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $ta = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $rombel = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $ta->id, 'status' => 'Aktif']);

        $user = User::create(['name' => 'Budi', 'email' => 'budi3@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'kelas_id' => $kelas->id, 'nisn' => '0097', 'nis' => 'S097', 'jenjang' => 'SMP', 'status' => 'Pindah']);
        AnggotaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel->id,
            'status' => 'Pindah',
            'tanggal_masuk' => '2026-08-01',
            'tanggal_keluar' => '2026-08-15',
        ]);

        // Absensi valid (saat masih di kelas) & invalid (setelah pindah)
        Absensi::create(['siswa_id' => $siswa->id, 'tanggal' => '2026-08-10', 'status' => 'hadir']);
        Absensi::create(['siswa_id' => $siswa->id, 'tanggal' => '2026-08-20', 'status' => 'alpa']);

        $component = new \App\Livewire\Admin\Kbm\RekapAbsensiIndex();
        $component->filterKelas = $kelas->id;
        $component->filterBulan = '08';
        $component->filterTahun = '2026';
        $component->loadData();

        // Absensi 10-08 (dalam rentang membership) → tampil
        $this->assertArrayHasKey($siswa->id, $component->absensiData);
        $this->assertEquals('hadir', $component->absensiData[$siswa->id]['2026-08-10']);

        // Absensi 20-08 (setelah tanggal_keluar) → TIDAK tampil
        $this->assertArrayNotHasKey('2026-08-20', $component->absensiData[$siswa->id] ?? []);
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: updateAbsensi manual — validasi membership per tanggal
    // ═══════════════════════════════════════════════════════════

    private function makeAbsensiKelasDanSiswa(?string $masuk = null, ?string $keluar = null): array
    {
        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $ta = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $rombel = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $ta->id, 'status' => 'Aktif']);

        $user = User::create(['name' => 'Budi', 'email' => 'budi@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'kelas_id' => $kelas->id, 'nisn' => '0100', 'nis' => 'S100', 'jenjang' => 'SMP', 'status' => 'Aktif']);
        AnggotaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel->id,
            'status' => 'Aktif',
            'tanggal_masuk' => $masuk,
            'tanggal_keluar' => $keluar,
        ]);

        return [$kelas, $siswa];
    }

    public function test_update_absensi_manual_sebelum_masuk_ditolak(): void
    {
        // Siswa baru masuk 10-08, input absen 05-08 → ditolak
        [$kelas, $siswa] = $this->makeAbsensiKelasDanSiswa('2026-08-10', null);

        $component = new \App\Livewire\Admin\Kbm\RekapAbsensiIndex();
        $component->filterKelas = $kelas->id;
        $component->updateAbsensi($siswa->id, '2026-08-05', 'hadir');

        $this->assertFalse(Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', '2026-08-05')->exists());
    }

    public function test_update_absensi_manual_setelah_keluar_ditolak(): void
    {
        // Siswa keluar 15-08, input absen 20-08 → ditolak
        [$kelas, $siswa] = $this->makeAbsensiKelasDanSiswa('2026-08-01', '2026-08-15');

        $component = new \App\Livewire\Admin\Kbm\RekapAbsensiIndex();
        $component->filterKelas = $kelas->id;
        $component->updateAbsensi($siswa->id, '2026-08-20', 'hadir');

        $this->assertFalse(Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', '2026-08-20')->exists());
    }

    public function test_update_absensi_manual_saat_membership_aktif_diterima(): void
    {
        // Siswa aktif (tanpa tanggal_keluar), input absen 10-08 → diterima
        [$kelas, $siswa] = $this->makeAbsensiKelasDanSiswa('2026-08-01', null);

        $component = new \App\Livewire\Admin\Kbm\RekapAbsensiIndex();
        $component->filterKelas = $kelas->id;
        $component->updateAbsensi($siswa->id, '2026-08-10', 'hadir');

        $this->assertTrue(Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', '2026-08-10')->where('status', 'hadir')->exists());
    }

    public function test_update_absensi_multi_interval_kelas_sama(): void
    {
        // Siswa pernah di VII A dua kali (dua rombel / dua TA berbeda)
        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $taGanjil = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Ditutup', 'is_active' => false]);
        $taGenap = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Genap', 'status' => 'Aktif', 'is_active' => true]);

        $rombelGanjil = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $taGanjil->id, 'status' => 'Ditutup']);
        $rombelGenap = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $taGenap->id, 'status' => 'Aktif']);

        $user = User::create(['name' => 'Budi', 'email' => 'budi2@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'kelas_id' => $kelas->id, 'nisn' => '0101', 'nis' => 'S101', 'jenjang' => 'SMP', 'status' => 'Aktif']);

        // Interval 1 (Ganjil): 01-08 s/d 15-08
        AnggotaRombel::create([
            'siswa_id' => $siswa->id, 'rombel_id' => $rombelGanjil->id,
            'status' => 'Pindah', 'tanggal_masuk' => '2026-08-01', 'tanggal_keluar' => '2026-08-15',
        ]);
        // Interval 2 (Genap): 05-01-2027 s/d 15-06-2027
        AnggotaRombel::create([
            'siswa_id' => $siswa->id, 'rombel_id' => $rombelGenap->id,
            'status' => 'Aktif', 'tanggal_masuk' => '2027-01-05', 'tanggal_keluar' => '2027-06-15',
        ]);

        $component = new \App\Livewire\Admin\Kbm\RekapAbsensiIndex();
        $component->filterKelas = $kelas->id;

        // Antara dua interval (10-2026) → ditolak
        $component->updateAbsensi($siswa->id, '2026-10-01', 'hadir');
        $this->assertFalse(Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', '2026-10-01')->exists());

        // Dalam interval 2 (02-2027) → diterima
        $component->updateAbsensi($siswa->id, '2027-02-10', 'hadir');
        $this->assertTrue(Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', '2027-02-10')->where('status', 'hadir')->exists());
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: scanAbsensi memvalidasi membership per tanggal
    // ═══════════════════════════════════════════════════════════

    public function test_scan_absensi_menolak_membership_diluar_tanggal(): void
    {
        if (! function_exists('imagecreatefrompng')) {
            $this->markTestSkipped('Ekstensi GD tidak tersedia.');
        }

        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $ta = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $rombel = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $ta->id, 'status' => 'Aktif']);

        $user = User::create(['name' => 'Budi', 'email' => 'budi4@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'kelas_id' => $kelas->id, 'nisn' => '0094', 'nis' => 'S094', 'jenjang' => 'SMP', 'status' => 'Pindah']);
        // Pindah tengah bulan: masuk 01-08, keluar 15-08
        AnggotaRombel::create([
            'siswa_id' => $siswa->id,
            'rombel_id' => $rombel->id,
            'status' => 'Pindah',
            'tanggal_masuk' => '2026-08-01',
            'tanggal_keluar' => '2026-08-15',
        ]);

        // Autentikasi admin agar mount() tidak null-guard auth()->user()
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'p']);
        \Livewire\Livewire::actingAs($admin);

        // Mock AI scanner: absen 10-08 (valid, masih anggota) & 20-08 (invalid, sudah keluar)
        \Mockery::mock('alias:App\Services\GeminiAiScanner', function ($mock) use ($siswa) {
            $mock->shouldReceive('scanAbsenMatriks')->once()->andReturn([
                $siswa->id => [
                    ['tanggal' => '2026-08-10', 'status' => 'hadir'],
                    ['tanggal' => '2026-08-20', 'status' => 'alpa'],
                ],
            ]);
        });

        $component = \Livewire\Livewire::test(\App\Livewire\Admin\Kbm\RekapAbsensiIndex::class);
        $component->set('filterKelas', $kelas->id);
        $component->set('filterBulan', '08');
        $component->set('filterTahun', '2026');
        $component->set('fotoKertas', \Illuminate\Http\UploadedFile::fake()->image('absen.png'));
        $component->call('scanAbsensi');

        // 10-08 (masih anggota kelas) → disimpan
        $this->assertTrue(Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', '2026-08-10')->where('status', 'hadir')->exists());
        // 20-08 (sudah keluar dari kelas) → ditolak
        $this->assertFalse(Absensi::where('siswa_id', $siswa->id)->whereDate('tanggal', '2026-08-20')->exists());
    }

    // ═══════════════════════════════════════════════════════════
    //  Test: Rollback aktivasi saat proses gagal
    // ═══════════════════════════════════════════════════════════

    public function test_salin_rombel_rollback_saat_gagal(): void
    {
        $taGanjil = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $taGenap = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Genap', 'status' => 'Draft', 'is_active' => false]);

        $kelas = Kelas::create(['nama_kelas' => 'VII A', 'jenjang' => 'SMP']);
        $rombelGanjil = Rombel::create(['kelas_id' => $kelas->id, 'tahun_ajaran_id' => $taGanjil->id, 'status' => 'Aktif']);

        $user = User::create(['name' => 'Siswa', 'email' => 'roll@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'kelas_id' => $kelas->id, 'nisn' => '0096', 'nis' => 'S096', 'jenjang' => 'SMP', 'status' => 'Aktif']);
        AnggotaRombel::create(['siswa_id' => $siswa->id, 'rombel_id' => $rombelGanjil->id, 'status' => 'Aktif']);

        // Paksa kegagalan di tengah transaksi (insert anggota_rombels selalu gagal)
        \Illuminate\Support\Facades\DB::statement("CREATE TRIGGER fail_salin BEFORE INSERT ON anggota_rombels BEGIN SELECT RAISE(ABORT, 'forced failure'); END;");

        try {
            $component = new \App\Livewire\Admin\DataMaster\TahunAjaranIndex();
            $component->salinTargetTahunAjaranId = $taGenap->id;
            try {
                $component->salinRombelDariSemesterSebelumnya();
                $this->fail('Harusnya throw karena trigger memaksa kegagalan.');
            } catch (\Throwable $e) {
                // expected
            }
        } finally {
            \Illuminate\Support\Facades\DB::statement('DROP TRIGGER IF EXISTS fail_salin');
        }

        // Rollback: Genap TIDAK aktif, Ganjil tetap aktif, rombel Genap tidak dibuat
        $this->assertFalse(TahunAjaran::find($taGenap->id)->is_active);
        $this->assertTrue(TahunAjaran::find($taGanjil->id)->is_active);
        $this->assertFalse(Rombel::where('kelas_id', $kelas->id)->where('tahun_ajaran_id', $taGenap->id)->exists());
    }

    public function test_kenaikan_kelas_rollback_saat_gagal(): void
    {
        $taLama = TahunAjaran::create(['tahun' => '2026/2027', 'semester' => 'Ganjil', 'status' => 'Aktif', 'is_active' => true]);
        $taBaru = TahunAjaran::create(['tahun' => '2027/2028', 'semester' => 'Ganjil', 'status' => 'Draft', 'is_active' => false]);
        $kelas8 = Kelas::create(['nama_kelas' => 'VIII A', 'jenjang' => 'SMP']);
        $kelas9 = Kelas::create(['nama_kelas' => 'IX A', 'jenjang' => 'SMP']);

        $user = User::create(['name' => 'Siswa', 'email' => 'kena@test.com', 'password' => 'p']);
        $siswa = Siswa::create(['user_id' => $user->id, 'kelas_id' => $kelas8->id, 'nisn' => '0095', 'nis' => 'S095', 'jenjang' => 'SMP', 'status' => 'Aktif']);

        \Illuminate\Support\Facades\DB::statement("CREATE TRIGGER fail_kenaikan BEFORE INSERT ON anggota_rombels BEGIN SELECT RAISE(ABORT, 'forced failure'); END;");

        try {
            $component = new \App\Livewire\Admin\DataMaster\TahunAjaranIndex();
            $component->targetTahunAjaranId = $taBaru->id;
            $component->kenaikanPreview = [
                [
                    'kelas_asal' => 'VIII A',
                    'kelas_tujuan' => 'IX A',
                    'kelas_tujuan_id' => $kelas9->id,
                    'is_lulus' => false,
                    'is_error' => false,
                    'jumlah_siswa' => 1,
                    'siswa' => [['id' => $siswa->id, 'nama' => 'Siswa']],
                ],
            ];
            try {
                $component->executeKenaikanKelas();
                $this->fail('Harusnya throw karena trigger memaksa kegagalan.');
            } catch (\Throwable $e) {
                // expected
            }
        } finally {
            \Illuminate\Support\Facades\DB::statement('DROP TRIGGER IF EXISTS fail_kenaikan');
        }

        // Rollback: TA baru TIDAK aktif, TA lama tetap aktif, siswa TIDAK pindah
        $this->assertFalse(TahunAjaran::find($taBaru->id)->is_active);
        $this->assertTrue(TahunAjaran::find($taLama->id)->is_active);
        $this->assertEquals($kelas8->id, Siswa::find($siswa->id)->kelas_id);
        $this->assertFalse(Rombel::where('kelas_id', $kelas9->id)->where('tahun_ajaran_id', $taBaru->id)->exists());
    }
}
