<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Gallery;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kategori;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\PembayaranSpp;
use App\Models\Post;
use App\Models\Rapor;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\Slider;
use App\Models\Spp;
use App\Models\Tag;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Models\KalenderAkademik;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('id_ID');

        // 1. Admin User (Ambil dari yang sudah ada)
        $admin = User::where('email', 'admin@sekolah.com')->first();

        // 2. Tahun Ajaran
        TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => false,
        ]);

        $ta = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Genap',
            'is_active' => true,
        ]);

        // 2.5 Hari Libur (Kalender Akademik)
        $holidays = [
            ['tanggal' => '2026-01-01', 'jenis_libur' => 'nasional', 'keterangan' => 'Tahun Baru Masehi 2026', 'is_libur' => true],
            ['tanggal' => '2026-03-31', 'jenis_libur' => 'nasional', 'keterangan' => 'Hari Raya Idul Fitri 1447H', 'is_libur' => true],
            ['tanggal' => '2026-04-01', 'jenis_libur' => 'nasional', 'keterangan' => 'Cuti Bersama Idul Fitri', 'is_libur' => true],
            ['tanggal' => '2026-06-06', 'jenis_libur' => 'nasional', 'keterangan' => 'Hari Raya Idul Adha 1447H', 'is_libur' => true],
            ['tanggal' => '2026-08-17', 'jenis_libur' => 'nasional', 'keterangan' => 'Hari Kemerdekaan RI ke-81', 'is_libur' => true],
            ['tanggal' => '2026-12-25', 'jenis_libur' => 'nasional', 'keterangan' => 'Hari Raya Natal', 'is_libur' => true],
        ];

        foreach ($holidays as $h) {
            KalenderAkademik::create($h);
        }

        // 3. Guru
        $gurus = [];
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "guru{$i}@sekolah.com",
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('guru');
            $gurus[] = Guru::create([
                'user_id' => $user->id,
                'nip' => $faker->unique()->numerify('19##########'),
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date('Y-m-d', '1990-01-01'),
                'agama' => $faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
                'alamat' => $faker->address,
                'no_telp' => $faker->phoneNumber,
                'jabatan' => $faker->randomElement(['Guru Tetap', 'Wali Kelas', 'Guru Honorer']),
            ]);
        }

        // 4. Mata Pelajaran (Struktur Baru)
        $dataMapel = [
            ['kode' => 'PAI', 'nama' => 'Pendidikan Agama Islam', 'kelompok' => 'Nasional', 'jenjang' => 'Umum'],
            ['kode' => 'MTK-SMP', 'nama' => 'Matematika SMP', 'kelompok' => 'Nasional', 'jenjang' => 'SMP'],
            ['kode' => 'MTK-SMA', 'nama' => 'Matematika SMA', 'kelompok' => 'Nasional', 'jenjang' => 'SMA'],
            ['kode' => 'FIS-SMA', 'nama' => 'Fisika Peminatan', 'kelompok' => 'Peminatan', 'jenjang' => 'SMA'],
            ['kode' => 'IPA-SMP', 'nama' => 'IPA Terpadu', 'kelompok' => 'Nasional', 'jenjang' => 'SMP'],
            ['kode' => 'B-ING', 'nama' => 'Bahasa Inggris', 'kelompok' => 'Nasional', 'jenjang' => 'Umum'],
        ];

        $mapelIds = [];
        foreach ($dataMapel as $m) {
            $mapelObj = Mapel::create([
                'kode_mapel' => $m['kode'],
                'nama_mapel' => $m['nama'],
                'kelompok' => $m['kelompok'],
                'jenjang' => $m['jenjang'],
            ]);
            $mapelIds[] = $mapelObj->id;
        }

        // 5. Kelas (SMP & SMA)
        $kelasIds = [];
        $daftarKelas = [
            ['nama' => '7A', 'jenjang' => 'SMP'],
            ['nama' => '8A', 'jenjang' => 'SMP'],
            ['nama' => '10 IPA 1', 'jenjang' => 'SMA'],
            ['nama' => '11 IPS 2', 'jenjang' => 'SMA'],
        ];

        foreach ($daftarKelas as $index => $kls) {
            $k = Kelas::create([
                'wali_kelas_id' => $gurus[$index]->id ?? null,
                'nama_kelas' => $kls['nama'],
                'jenjang' => $kls['jenjang'],
            ]);
            $kelasIds[] = $k->id;
        }

        // 6. Siswa — 32 siswa per kelas
        $siswas = [];
        $siswaCounter = 1; // untuk email unik

        foreach ($kelasIds as $kelasId) {
            $kelasObj = Kelas::find($kelasId);

            for ($i = 1; $i <= 32; $i++) {
                $user = User::create([
                    'name'     => $faker->name,
                    'email'    => "siswa{$siswaCounter}@sekolah.com",
                    'password' => bcrypt('password'),
                ]);
                $user->assignRole('siswa');

                $siswas[] = Siswa::create([
                    'user_id'       => $user->id,
                    'kelas_id'      => $kelasId,
                    'nisn'          => $faker->unique()->numerify('00########'),
                    'nis'           => $faker->unique()->numerify('1####'),
                    'jenjang'       => $kelasObj->jenjang,
                    'tempat_lahir'  => $faker->city,
                    'tanggal_lahir' => $faker->date('Y-m-d', '2010-01-01'),
                    'agama'         => $faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
                    'jenis_kelamin' => $faker->randomElement(['Laki-Laki', 'Perempuan']),
                    'alamat'        => $faker->address,
                    'nama_ayah'     => $faker->name('male'),
                    'nama_ibu'      => $faker->name('female'),
                    'no_telp_ortu'  => $faker->phoneNumber,
                    'status'        => 'Aktif',
                ]);

                $siswaCounter++;
            }
        }

        // 6.5 Ruangan
        $ruanganNames = ['Lab Komputer 1', 'Lab IPA', 'Aula', 'Ruang Kelas 10-A', 'Ruang Kelas 10-B', 'Perpustakaan'];
        $ruangans = [];
        foreach ($ruanganNames as $r) {
            $ruangans[] = \App\Models\Ruangan::create(['nama_ruangan' => $r, 'kapasitas' => 36]);
        }

        // 7. Jadwal
        // 7. Jadwal Pelajaran Cerdas (Tanpa Bentrok)
        $jadwals = [];
        $slotMulai = ['07:15', '08:00', '08:45', '10:00', '10:45'];
        $slotSelesai = ['08:00', '08:45', '09:30', '10:45', '11:30'];
        
        $hariMinggu = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $counter = 0;

        foreach ($kelasIds as $idxKelas => $k_id) {
            // Beri 2 jadwal per kelas (total 8 jadwal)
            for ($j = 0; $j < 2; $j++) {
                $jadwals[] = Jadwal::create([
                    'kelas_id' => $k_id,
                    'mapel_id' => $faker->randomElement($mapelIds),
                    // Pastikan beda guru untuk setiap kelas biar gampang tidak bentrok guru
                    'guru_id' => $gurus[($idxKelas + $j) % count($gurus)]->id, 
                    'ruangan_id' => $ruangans[$idxKelas % count($ruangans)]->id, // Beda kelas beda ruangan (fixed)
                    'hari' => $hariMinggu[$j],
                    'jam_mulai' => $slotMulai[$j], // Jam ke-1 dan Jam ke-2
                    'jam_selesai' => $slotSelesai[$j],
                ]);
            }
        }

        // 8. Transaksi & Laporan (Hanya untuk beberapa siswa)
        foreach (array_slice($siswas, 0, 5) as $siswa) {
            // Absensi
            Absensi::create([
                'jadwal_id' => $faker->randomElement($jadwals)->id,
                'siswa_id' => $siswa->id,
                'tanggal' => date('Y-m-d'),
                'status' => 'hadir',
            ]);

            // Nilai
            Nilai::create([
                'siswa_id'        => $siswa->id,
                'mapel_id'        => $faker->randomElement($mapelIds),
                'tahun_ajaran_id' => $ta->id,
                'pts'             => $faker->numberBetween(70, 90),
                'pas'             => $faker->numberBetween(70, 90),
            ]);

            // Rapor
            Rapor::create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $ta->id,
                'kelas_id' => $siswa->kelas_id,
                'catatan_wali_kelas' => 'Sangat aktif di kelas.',
                'keputusan' => 'Naik Kelas',
            ]);
        }

        // 9. Keuangan — Tarif SPP per jenjang
        $sppSmp = Spp::create([
            'tahun_ajaran_id' => $ta->id,
            'jenjang'        => 'SMP',
            'kategori'       => 'SPP Bulanan',
            'nominal'        => 300000,
            'keterangan'     => 'Tarif SPP Bulanan SMP TA 2025/2026',
        ]);

        $sppSma = Spp::create([
            'tahun_ajaran_id' => $ta->id,
            'jenjang'        => 'SMA',
            'kategori'       => 'SPP Bulanan',
            'nominal'        => 450000,
            'keterangan'     => 'Tarif SPP Bulanan SMA TA 2025/2026',
        ]);

        Spp::create([
            'tahun_ajaran_id' => $ta->id,
            'jenjang'        => 'Semua',
            'kategori'       => 'Uang Bangunan',
            'nominal'        => 1500000,
            'keterangan'     => 'Uang Bangunan Tahunan — Satu kali bayar per tahun ajaran',
        ]);

        // Demo pembayaran: beberapa siswa SMP sudah bayar Jan-Mar 2026
        $tahunTagihan = 2026;
        foreach (array_slice($siswas, 0, 10) as $idx => $siswa) {
            // Siswa SMP bayar 3 bulan pertama
            if ($siswa->jenjang === 'SMP') {
                $bulanBayar = [1, 2, 3];
                foreach ($bulanBayar as $bln) {
                    PembayaranSpp::create([
                        'siswa_id'     => $siswa->id,
                        'spp_id'       => $sppSmp->id,
                        'user_id'      => $admin->id,
                        'tahun'        => $tahunTagihan,
                        'bulan'        => $bln,
                        'tanggal_bayar' => date('Y-m-d', strtotime("2026-0{$bln}-10")),
                        'jumlah_bayar' => 300000,
                        'potongan'     => 0,
                        'status'       => 'Lunas',
                    ]);
                }
            }
        }

        // Demo pembayaran: beberapa siswa SMA bayar Jan-Feb 2026
        foreach (array_slice($siswas, 64, 8) as $siswa) {
            if ($siswa->jenjang === 'SMA') {
                foreach ([1, 2] as $bln) {
                    PembayaranSpp::create([
                        'siswa_id'     => $siswa->id,
                        'spp_id'       => $sppSma->id,
                        'user_id'      => $admin->id,
                        'tahun'        => $tahunTagihan,
                        'bulan'        => $bln,
                        'tanggal_bayar' => date('Y-m-d', strtotime("2026-0{$bln}-12")),
                        'jumlah_bayar' => 450000,
                        'potongan'     => 0,
                        'status'       => 'Lunas',
                    ]);
                }
            }
        }

        // 10. Blog & CMS


        $kat = Kategori::create(['nama_kategori' => 'Kegiatan Sekolah', 'slug' => 'kegiatan-sekolah']);
        $tag = Tag::create(['nama_tag' => 'Edukasi', 'slug' => 'edukasi']);

        $judulPost = 'Penerimaan Siswa Baru Tahun 2026';
        $post = Post::create([
            'user_id' => $admin->id,
            'kategori_id' => $kat->id,
            'judul' => $judulPost,
            'slug' => Str::slug($judulPost),
            'konten' => 'Pendaftaran telah dibuka untuk jenjang SMP dan SMA.',
            'status' => 'Published',
        ]);
        $post->tags()->attach([$tag->id]);

        Setting::create(['key' => 'site_name', 'value' => 'SmartSchool Management']);
        Setting::create(['key' => 'durasi_jam_pelajaran', 'value' => '45']);
        Setting::create(['key' => 'jam_mulai_pelajaran', 'value' => '07:15']);
        Slider::create(['judul' => 'Selamat Datang', 'foto' => 'welcome.jpg', 'is_active' => true]);
        Gallery::create(['judul' => 'Kegiatan Pramuka', 'foto' => 'pramuka.jpg']);
    }
}
