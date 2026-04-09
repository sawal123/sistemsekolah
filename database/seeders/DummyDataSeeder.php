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
        $ta = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

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

        // 6. Siswa
        $siswas = [];
        for ($i = 1; $i <= 20; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "siswa{$i}@sekolah.com",
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('siswa');

            $kelasAcak = Kelas::find($faker->randomElement($kelasIds));

            $siswas[] = Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $kelasAcak->id,
                'nisn' => $faker->unique()->numerify('00########'),
                'nis' => $faker->unique()->numerify('1####'),
                'jenjang' => $kelasAcak->jenjang,
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date('Y-m-d', '2010-01-01'),
                'agama' => 'Islam',
                'alamat' => $faker->address,
                'nama_ayah' => $faker->name('male'),
                'nama_ibu' => $faker->name('female'),
                'no_telp_ortu' => $faker->phoneNumber,
                'status' => 'Aktif',
            ]);
        }

        // 7. Jadwal
        $jadwals = [];
        for ($i = 0; $i < 10; $i++) {
            $jadwals[] = Jadwal::create([
                'kelas_id' => $faker->randomElement($kelasIds),
                'mapel_id' => $faker->randomElement($mapelIds),
                'guru_id' => $faker->randomElement(collect($gurus)->pluck('id')->toArray()),
                'hari' => $faker->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']),
                'jam_mulai' => $faker->time('H:i'),
            ]);
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
                'siswa_id' => $siswa->id,
                'mapel_id' => $faker->randomElement($mapelIds),
                'tahun_ajaran_id' => $ta->id,
                'jenis_nilai' => 'UTS',
                'skor' => $faker->numberBetween(75, 95),
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

        // 9. Keuangan
        $spp = Spp::create([
            'tahun_ajaran_id' => $ta->id,
            'nominal' => 300000,
        ]);

        PembayaranSpp::create([
            'siswa_id' => $siswas[0]->id,
            'spp_id' => $spp->id,
            'bulan' => 'Januari',
            'tanggal_bayar' => date('Y-m-d'),
            'jumlah_bayar' => 300000,
            'status' => 'Lunas',
        ]);

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
        Slider::create(['judul' => 'Selamat Datang', 'foto' => 'welcome.jpg', 'is_active' => true]);
        Gallery::create(['judul' => 'Kegiatan Pramuka', 'foto' => 'pramuka.jpg']);
    }
}
