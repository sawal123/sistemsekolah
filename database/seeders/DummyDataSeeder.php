<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Gallery;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kategori;
use App\Models\KegiatanAlumni;
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

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('id_ID');

        // Admin User
        $admin = User::create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@sekolah.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Gurus
        $gurus = [];
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "guru{$i}@sekolah.com",
                'password' => bcrypt('password'),
                'role' => 'guru',
            ]);
            $gurus[] = Guru::create([
                'user_id' => $user->id,
                'nip' => $faker->unique()->numerify('19##########'),
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date(),
                'agama' => $faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
                'alamat' => $faker->address,
                'no_telp' => $faker->phoneNumber,
                'jabatan' => 'Guru Mata Pelajaran',
            ]);
        }

        // Kelas
        $kelasIds = [];
        foreach (['7A', '7B', '8A', '9A'] as $index => $nama) {
            $k = Kelas::create([
                'wali_kelas_id' => $gurus[$index]->id ?? null,
                'nama_kelas' => $nama,
                'jenjang' => 'SMP',
            ]);
            $kelasIds[] = $k->id;
        }

        // Siswas
        $siswas = [];
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "siswa{$i}@sekolah.com",
                'password' => bcrypt('password'),
                'role' => 'siswa',
            ]);
            $siswas[] = Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $faker->randomElement($kelasIds),
                'nisn' => $faker->unique()->numerify('00########'),
                'nis' => $faker->unique()->numerify('1####'),
                'jenjang' => 'SMP',
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date(),
                'agama' => 'Islam',
                'alamat' => $faker->address,
                'nama_ayah' => $faker->name('male'),
                'nama_ibu' => $faker->name('female'),
                'pekerjaan_ayah' => 'Wiraswasta',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'no_telp_ortu' => $faker->phoneNumber,
                'status' => 'Aktif',
            ]);
        }

        // Mapel
        $mapels = [];
        foreach (['Matematika', 'Bahasa Indonesia', 'IPA', 'IPS', 'Bahasa Inggris'] as $nama) {
            $mapels[] = Mapel::create([
                'nama_mapel' => $nama,
                'tipe' => 'Wajib',
            ]);
        }

        // Tahun Ajaran
        $ta = TahunAjaran::create([
            'tahun' => '2023/2024',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        // Jadwal
        $jadwals = [];
        for ($i = 0; $i < 5; $i++) {
            $jadwals[] = Jadwal::create([
                'kelas_id' => $faker->randomElement($kelasIds),
                'mapel_id' => $faker->randomElement(array_column($mapels, 'id')),
                'guru_id' => $faker->randomElement(array_column($gurus, 'id')),
                'hari' => $faker->randomElement(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']),
                'jam_mulai' => $faker->time('H:i'),
            ]);
        }

        // Absensi & Nilai for specific student
        foreach ($siswas as $siswa) {
            Absensi::create([
                'jadwal_id' => $jadwals[0]->id,
                'siswa_id' => $siswa->id,
                'tanggal' => date('Y-m-d'),
                'status' => 'hadir',
            ]);

            Nilai::create([
                'siswa_id' => $siswa->id,
                'mapel_id' => $mapels[0]->id,
                'tahun_ajaran_id' => $ta->id,
                'jenis_nilai' => 'UTS',
                'skor' => $faker->randomFloat(2, 70, 100),
            ]);

            Rapor::create([
                'siswa_id' => $siswa->id,
                'tahun_ajaran_id' => $ta->id,
                'kelas_id' => $siswa->kelas_id,
                'catatan_wali_kelas' => 'Tingkatkan belajarnya.',
                'keputusan' => 'Naik Kelas',
            ]);
        }

        // SPP & Pembayaran
        $spp = Spp::create([
            'tahun_ajaran_id' => $ta->id,
            'nominal' => 250000,
        ]);

        PembayaranSpp::create([
            'siswa_id' => $siswas[0]->id,
            'spp_id' => $spp->id,
            'bulan' => 'Juli',
            'tanggal_bayar' => date('Y-m-d'),
            'jumlah_bayar' => 250000,
            'status' => 'Lunas',
            'keterangan' => 'Via Transfer',
        ]);

        // Kegiatan Alumni for simulation
        KegiatanAlumni::create([
            'siswa_id' => $siswas[0]->id,
            'jenis_kegiatan' => 'Kuliah',
            'nama_instansi' => 'Universitas Indonesia',
            'posisi_jurusan' => 'Sistem Informasi',
            'tahun_mulai' => '2025',
        ]);

        // Blogs stuff
        $kat = Kategori::create(['nama_kategori' => 'Berita', 'slug' => 'berita']);
        $tag = Tag::create(['nama_tag' => 'Pendidikan', 'slug' => 'pendidikan']);
        $post = Post::create([
            'user_id' => $admin->id,
            'kategori_id' => $kat->id,
            'judul' => 'Sekolah Bebas Narkoba',
            'slug' => 'sekolah-bebas-narkoba',
            'konten' => 'Isi berita lengkap ada disini.',
            'status' => 'Published',
        ]);
        $post->tags()->attach([$tag->id]);

        // CMS settings
        Setting::create(['key' => 'site_name', 'value' => 'Sistem Sekolah Pintar']);
        Slider::create(['judul' => 'Pendaftaran Dibuka', 'is_active' => true]);
        Gallery::create(['judul' => 'Kegiatan Pramuka', 'foto' => 'pramuka.jpg']);
    }
}
