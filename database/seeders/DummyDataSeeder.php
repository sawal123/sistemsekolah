<?php

namespace Database\Seeders;

use App\Models\{Absensi, AnggotaRombel, Fasilitas, Gallery, Guru, Jadwal, Jurusan, KalenderAkademik, Kategori, KegiatanAlumni, Kelas, Mapel, Nilai, PendaftaranMuridBaru, Pembayaran, Post, PostComment, PpdbGelombang, PpdbGelombangRiwayat, Rapor, Rombel, Ruangan, Setting, Siswa, Slider, Spp, Tag, Tagihan, TahunAjaran, User, WebsitePopup};
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/** Dataset demo lengkap, konsisten, dan aman dijalankan ulang. */
class DummyDataSeeder extends Seeder
{
    private ?string $defaultPassword = null;

    public function run(): void
    {
        $this->publishSeedImages();
        $admin = User::where('email', 'admin@sekolah.com')->firstOrFail();
        $admin->update(['name' => 'Siti Rahmawati, S.E.', 'staff_position' => 'Kepala Tata Usaha', 'is_active' => true]);
        $roles = collect(['admin', 'guru', 'siswa', 'alumni'])->mapWithKeys(fn ($name) => [$name => Role::firstOrCreate(['name' => $name, 'guard_name' => 'web'])]);
        $years = $this->seedAcademicYears();
        $jurusans = $this->seedMajors();
        $this->seedCalendar();
        $teachers = $this->seedTeachers($roles['guru']);
        $this->seedStaff($roles['admin']);
        $mapels = $this->seedSubjects($jurusans);
        [$classes, $rombels] = $this->seedClassesAndRombels($teachers, $jurusans, $years['active']);
        $students = $this->seedStudents($classes, $rombels, $roles['siswa']);
        $alumni = $this->seedAlumni($roles);
        $rooms = $this->seedRooms();
        $schedules = $this->seedSchedules($classes, $teachers, $mapels, $rooms, $years['active']);
        $this->seedAcademicRecords($students, $schedules, $mapels, $years['active']);
        $fees = $this->seedFees($years, $jurusans);
        $this->seedInvoicesAndPayments($students, $fees, $admin);
        $this->seedPpdb($admin, $jurusans);
        $this->seedWebsite($admin);
    }

    private function publishSeedImages(): void
    {
        $source = public_path('assets/images');
        $destination = storage_path('app/public/seed-images');
        File::ensureDirectoryExists($destination);
        foreach (['logo-sekolah.svg', 'principal.jpg', 'hero-campus.jpg', 'hero-lab.jpg', 'facility-library.jpg', 'facility-computer.jpg', 'facility-sports.jpg', 'activity-arts.jpg', 'achievement-olympiad.jpg'] as $file) {
            if (File::exists($source.'/'.$file)) File::copy($source.'/'.$file, $destination.'/'.$file);
        }
    }

    private function seedAcademicYears(): array
    {
        $closed = TahunAjaran::updateOrCreate(['tahun' => '2025/2026', 'semester' => 'Genap'], ['tanggal_mulai' => '2026-01-05', 'tanggal_selesai' => '2026-06-20', 'status' => 'Ditutup', 'is_active' => false]);
        $active = TahunAjaran::updateOrCreate(['tahun' => '2026/2027', 'semester' => 'Ganjil'], ['tanggal_mulai' => '2026-07-13', 'tanggal_selesai' => '2026-12-19', 'status' => 'Aktif', 'is_active' => true]);
        $draft = TahunAjaran::updateOrCreate(['tahun' => '2026/2027', 'semester' => 'Genap'], ['tanggal_mulai' => '2027-01-04', 'tanggal_selesai' => '2027-06-19', 'status' => 'Draft', 'is_active' => false]);
        TahunAjaran::where('id', '!=', $active->id)->update(['is_active' => false]);
        return compact('closed', 'active', 'draft');
    }

    private function seedMajors(): array
    {
        return collect([
            'RPL' => ['Rekayasa Perangkat Lunak', 'Pengembangan aplikasi, pemrograman, dan basis data.'],
            'TKJ' => ['Teknik Komputer dan Jaringan', 'Infrastruktur jaringan, sistem, dan keamanan digital.'],
            'AKL' => ['Akuntansi dan Keuangan Lembaga', 'Akuntansi, perpajakan, dan pengelolaan keuangan.'],
        ])->mapWithKeys(fn ($item, $code) => [$code => Jurusan::updateOrCreate(['kode' => $code], ['nama' => $item[0], 'deskripsi' => $item[1], 'is_active' => true])])->all();
    }

    private function seedCalendar(): void
    {
        foreach ([
            ['2026-11-10', 'sekolah', 'Asesmen Sumatif Akhir Semester Ganjil', false], ['2026-11-25', 'sekolah', 'Rapat evaluasi pembelajaran', false],
            ['2026-12-18', 'sekolah', 'Pembagian laporan hasil belajar semester ganjil', false], ['2026-12-25', 'nasional', 'Hari Raya Natal', true],
            ['2027-01-01', 'nasional', 'Tahun Baru Masehi', true], ['2027-01-04', 'sekolah', 'Awal semester genap', false], ['2027-05-01', 'nasional', 'Hari Buruh Internasional', true],
        ] as [$date, $type, $description, $isHoliday]) {
            KalenderAkademik::updateOrCreate(['tanggal' => $date], ['jenis_libur' => $type, 'keterangan' => $description, 'is_libur' => $isHoliday]);
        }
    }

    private function seedTeachers(Role $role): array
    {
        $data = [
            ['guru@sekolah.com', 'Budi Santoso, S.Pd.', '198209012006041001', 'Guru Matematika'], ['nuraini@sekolah.com', 'Nuraini, M.Pd.', '197811112005012002', 'Guru Bahasa Indonesia'],
            ['agusrizal@sekolah.com', 'Agus Rizal, S.Pd.', '198507152010011003', 'Guru Fisika'], ['dewisartika@sekolah.com', 'Dewi Sartika, S.Pd.', '198910202011012004', 'Guru Bahasa Inggris'],
            ['rifkiansyah@sekolah.com', 'Rifki Ansyah, S.Kom.', '198601042010021005', 'Guru Informatika'], ['melati@sekolah.com', 'Melati Puspita, S.Pd.', '199002142014022006', 'Guru Biologi'],
            ['hendrawan@sekolah.com', 'Hendrawan, S.Pd.', '198305172008011007', 'Guru Ekonomi'], ['fitriani@sekolah.com', 'Fitriani, S.Pd.', '198811302012012008', 'Guru Sejarah'],
            ['yusuf@sekolah.com', 'Yusuf Maulana, S.Pd.', '198710092011011009', 'Guru PJOK'], ['lestaridewi@sekolah.com', 'Lestari Dewi, S.Sn.', '198605042010012010', 'Guru Seni Budaya'],
        ];
        return array_map(function ($row, $index) use ($role) {
            [$email, $name, $nip, $position] = $row;
            $user = User::updateOrCreate(['email' => $email], ['name' => $name, 'password' => $this->password(), 'is_active' => true, 'staff_position' => null]);
            $user->syncRoles([$role]);
            return Guru::updateOrCreate(['user_id' => $user->id], ['nip' => $nip, 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => Carbon::create(1978 + ($index % 12), 2, 10), 'agama' => 'Islam', 'alamat' => 'Kota Bandung, Jawa Barat', 'no_telp' => '0812'.str_pad((string) (3000000 + $index), 7, '0', STR_PAD_LEFT), 'jabatan' => $position]);
        }, $data, array_keys($data));
    }

    private function seedStaff(Role $role): void
    {
        foreach ([['operator.ppdb@sekolah.com', 'Dian Permata, A.Md.', 'Operator PPDB'], ['bendahara@sekolah.com', 'Rudi Hartono, S.E.', 'Bendahara'], ['perpustakaan@sekolah.com', 'Maya Lestari, S.IIP.', 'Pustakawan'], ['keamanan@sekolah.com', 'Eko Prasetyo', 'Petugas Keamanan']] as [$email, $name, $position]) {
            $user = User::updateOrCreate(['email' => $email], ['name' => $name, 'password' => $this->password(), 'is_active' => true, 'staff_position' => $position]);
            $user->syncRoles([$role]);
        }
    }

    private function seedSubjects(array $jurusans): array
    {
        $data = [['AGM', 'Pendidikan Agama', 'Nasional', 'Umum', null], ['PPKN', 'Pendidikan Pancasila', 'Nasional', 'Umum', null], ['BIN', 'Bahasa Indonesia', 'Nasional', 'Umum', null], ['BIG', 'Bahasa Inggris', 'Nasional', 'Umum', null], ['MTK', 'Matematika', 'Nasional', 'Umum', null], ['FIS', 'Fisika', 'Peminatan', 'SMA', null], ['BIO', 'Biologi', 'Peminatan', 'SMA', null], ['EKO', 'Ekonomi', 'Peminatan', 'SMA', null], ['SEJ', 'Sejarah Indonesia', 'Nasional', 'SMA', null], ['INF', 'Informatika', 'Nasional', 'Umum', null], ['PJOK', 'Pendidikan Jasmani', 'Nasional', 'Umum', null], ['PBO-RPL', 'Pemrograman Berorientasi Objek', 'Peminatan', 'SMK', $jurusans['RPL']->id], ['JAR-TKJ', 'Administrasi Infrastruktur Jaringan', 'Peminatan', 'SMK', $jurusans['TKJ']->id], ['AKT-AKL', 'Praktikum Akuntansi', 'Peminatan', 'SMK', $jurusans['AKL']->id]];
        return collect($data)->mapWithKeys(function ($item) { [$code, $name, $group, $level, $major] = $item; return [$code => Mapel::updateOrCreate(['kode_mapel' => $code], ['nama_mapel' => $name, 'kelompok' => $group, 'jenjang' => $level, 'jurusan_id' => $major, 'kkm' => 75, 'bobot_harian' => 40, 'bobot_pts' => 30, 'bobot_pas' => 30])]; })->all();
    }

    private function seedClassesAndRombels(array $teachers, array $jurusans, TahunAjaran $year): array
    {
        $data = [['7A', 'SMP', null], ['8A', 'SMP', null], ['X IPA 1', 'SMA', null], ['X IPS 1', 'SMA', null], ['XI IPA 1', 'SMA', null], ['XI IPS 1', 'SMA', null], ['X RPL 1', 'SMK', $jurusans['RPL']->id], ['X TKJ 1', 'SMK', $jurusans['TKJ']->id], ['X AKL 1', 'SMK', $jurusans['AKL']->id]];
        $classes = []; $rombels = [];
        foreach ($data as $index => [$name, $level, $major]) {
            $class = Kelas::updateOrCreate(['nama_kelas' => $name], ['jenjang' => $level, 'jurusan_id' => $major, 'wali_kelas_id' => $teachers[$index]->id]);
            $classes[] = $class;
            $rombels[$class->id] = Rombel::updateOrCreate(['kelas_id' => $class->id, 'tahun_ajaran_id' => $year->id], ['wali_kelas_id' => $teachers[$index]->id, 'kapasitas' => 36, 'status' => 'Aktif']);
        }
        return [$classes, $rombels];
    }

    private function seedStudents(array $classes, array $rombels, Role $role): array
    {
        $names = ['Alya Ramadhani', 'Bagas Pratama', 'Citra Lestari', 'Dimas Saputra', 'Elsa Maharani', 'Farhan Akbar', 'Gita Permata', 'Hanif Ramadhan', 'Intan Safitri', 'Jovan Maulana', 'Karina Putri', 'Lukman Hakim'];
        $students = []; $number = 1;
        foreach ($classes as $classIndex => $class) foreach ($names as $nameIndex => $baseName) {
            $email = $number === 1 ? 'siswa@sekolah.com' : sprintf('siswa%03d@sekolah.com', $number);
            $user = User::updateOrCreate(['email' => $email], ['name' => $number === 1 ? 'Andi Pratama' : $baseName.' '.chr(65 + $classIndex), 'password' => $this->password(), 'is_active' => true, 'staff_position' => null]);
            $user->syncRoles([$role]);
            $student = Siswa::withTrashed()->updateOrCreate(['user_id' => $user->id], ['kelas_id' => $class->id, 'nisn' => '00'.str_pad((string) (26000000 + $number), 8, '0', STR_PAD_LEFT), 'nis' => '26'.str_pad((string) $number, 4, '0', STR_PAD_LEFT), 'jenjang' => $class->jenjang, 'jurusan_id' => $class->jurusan_id, 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => Carbon::create(2009 + ($classIndex % 2), 1 + ($nameIndex % 10), 1 + ($nameIndex % 20)), 'agama' => 'Islam', 'jenis_kelamin' => $nameIndex % 2 === 0 ? 'Perempuan' : 'Laki-Laki', 'alamat' => 'Kota Bandung, Jawa Barat', 'nama_ayah' => 'Bapak '.$baseName, 'nama_ibu' => 'Ibu '.$baseName, 'pekerjaan_ayah' => 'Wiraswasta', 'pekerjaan_ibu' => 'Ibu Rumah Tangga', 'no_telp_ortu' => '0813'.str_pad((string) (4000000 + $number), 7, '0', STR_PAD_LEFT), 'status' => 'Aktif']);
            if ($student->trashed()) $student->restore();
            AnggotaRombel::updateOrCreate(['rombel_id' => $rombels[$class->id]->id, 'siswa_id' => $student->id], ['status' => 'Aktif', 'tanggal_masuk' => '2026-07-13', 'tanggal_keluar' => null]);
            $students[] = $student; $number++;
        }
        return $students;
    }

    private function seedAlumni($roles): array
    {
        $result = [];
        foreach ([['alumni.ardian@sekolah.com', 'Ardian Putra', 'Kuliah', 'Institut Teknologi Bandung', 'Teknik Informatika', '2024'], ['alumni.salsa@sekolah.com', 'Salsa Nabila', 'Kerja', 'PT Teknologi Nusantara', 'UI/UX Designer', '2023'], ['alumni.fauzan@sekolah.com', 'Fauzan Akmal', 'Wirausaha', 'Kopi Pagi', 'Pendiri Usaha', '2022']] as $index => [$email, $name, $activity, $institution, $position, $year]) {
            $user = User::updateOrCreate(['email' => $email], ['name' => $name, 'password' => $this->password(), 'is_active' => true]); $user->syncRoles([$roles['siswa'], $roles['alumni']]);
            $student = Siswa::withTrashed()->updateOrCreate(['user_id' => $user->id], ['kelas_id' => null, 'nisn' => '0099'.str_pad((string) $index, 6, '0', STR_PAD_LEFT), 'nis' => 'ALM'.($index + 1), 'jenjang' => 'SMA', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => Carbon::create(2004 + $index, 5, 10), 'agama' => 'Islam', 'jenis_kelamin' => $index === 1 ? 'Perempuan' : 'Laki-Laki', 'alamat' => 'Bandung', 'status' => 'Lulus', 'tahun_lulus' => '2022']);
            if ($student->trashed()) $student->restore();
            KegiatanAlumni::updateOrCreate(['siswa_id' => $student->id, 'nama_instansi' => $institution], ['jenis_kegiatan' => $activity, 'posisi_jurusan' => $position, 'tahun_mulai' => $year, 'deskripsi' => $name.' berbagi pengalaman dan terus terlibat dalam kegiatan alumni sekolah.', 'status_verifikasi' => 'Verified', 'is_public' => true]); $result[] = $student;
        }
        return $result;
    }

    private function seedRooms(): array
    {
        return collect([['Ruang 7A', 36], ['Ruang 8A', 36], ['Ruang 10 IPA', 36], ['Ruang 10 IPS', 36], ['Lab Komputer 1', 36], ['Lab IPA', 32], ['Perpustakaan', 40]])->mapWithKeys(fn ($item) => [$item[0] => Ruangan::updateOrCreate(['nama_ruangan' => $item[0]], ['kapasitas' => $item[1]])])->all();
    }

    private function seedSchedules(array $classes, array $teachers, array $subjects, array $rooms, TahunAjaran $year): array
    {
        $plans = [['MTK', 0, 'Ruang 7A', 'Senin', '07:15', '08:00'], ['BIN', 1, 'Ruang 8A', 'Selasa', '07:15', '08:00'], ['FIS', 2, 'Ruang 10 IPA', 'Rabu', '07:15', '08:00'], ['EKO', 6, 'Ruang 10 IPS', 'Kamis', '07:15', '08:00'], ['BIO', 5, 'Lab IPA', 'Jumat', '07:15', '08:00'], ['BIG', 3, 'Ruang 10 IPS', 'Senin', '08:00', '08:45'], ['PBO-RPL', 4, 'Lab Komputer 1', 'Selasa', '08:00', '09:30'], ['JAR-TKJ', 4, 'Lab Komputer 1', 'Rabu', '09:45', '11:15'], ['AKT-AKL', 6, 'Perpustakaan', 'Kamis', '08:00', '09:30']]; $schedules = [];
        foreach ($plans as $index => [$code, $teacher, $room, $day, $start, $end]) { $class = $classes[$index]; $schedules[] = Jadwal::updateOrCreate(['kelas_id' => $class->id, 'tahun_ajaran_id' => $year->id, 'mapel_id' => $subjects[$code]->id, 'hari' => $day, 'jam_mulai' => $start], ['guru_id' => $teachers[$teacher]->id, 'ruangan_id' => $rooms[$room]->id, 'jam_selesai' => $end]); }
        return $schedules;
    }

    private function seedAcademicRecords(array $students, array $schedules, array $subjects, TahunAjaran $year): void
    {
        $subjectKeys = array_keys($subjects);
        foreach (array_slice($students, 0, 18) as $index => $student) {
            $subject = $subjects[$subjectKeys[$index % count($subjectKeys)]];
            Nilai::updateOrCreate(['siswa_id' => $student->id, 'mapel_id' => $subject->id, 'tahun_ajaran_id' => $year->id], ['nilai_harian' => ['tugas_1' => 82 + ($index % 10), 'tugas_2' => 78 + ($index % 12)], 'pts' => 80 + ($index % 12), 'pas' => 79 + ($index % 15)]);
            Rapor::updateOrCreate(['siswa_id' => $student->id, 'tahun_ajaran_id' => $year->id], ['kelas_id' => $student->kelas_id, 'catatan_wali_kelas' => 'Menunjukkan perkembangan yang baik dalam pembelajaran dan karakter.', 'ekskul' => ['Pramuka' => 'Baik'], 'prestasi' => [], 'karakter' => ['Disiplin' => 'Baik', 'Gotong royong' => 'Baik'], 'keputusan' => 'Melanjutkan', 'is_locked' => false]);
            $date = Carbon::parse('2026-10-'.str_pad((string) (5 + ($index % 20)), 2, '0', STR_PAD_LEFT));
            Absensi::updateOrCreate(['siswa_id' => $student->id, 'tanggal' => $date->toDateString()], ['jadwal_id' => $schedules[$index % count($schedules)]->id, 'status' => $index % 11 === 0 ? 'izin' : 'hadir']);
        }
    }

    private function seedFees(array $years, array $jurusans): array
    {
        $active = $years['active']; $draft = $years['draft']; $definitions = [['SMP', null, 'SPP Bulanan', 350000, 'Biaya pendidikan bulanan SMP tahun ajaran 2026/2027.', $active], ['SMA', null, 'SPP Bulanan', 500000, 'Biaya pendidikan bulanan SMA tahun ajaran 2026/2027.', $active], ['SMK', $jurusans['RPL']->id, 'SPP Bulanan', 575000, 'Biaya pendidikan bulanan program RPL.', $active], ['SMK', $jurusans['TKJ']->id, 'SPP Bulanan', 600000, 'Biaya pendidikan bulanan program TKJ.', $active], ['SMK', $jurusans['AKL']->id, 'SPP Bulanan', 550000, 'Biaya pendidikan bulanan program AKL.', $active], ['Semua', null, 'Uang Bangunan', 1750000, 'Dibayarkan satu kali pada saat daftar ulang.', $active], ['SMK', $jurusans['RPL']->id, 'SPP Bulanan', 600000, 'Rencana tarif RPL semester genap.', $draft], ['SMK', $jurusans['TKJ']->id, 'SPP Bulanan', 625000, 'Rencana tarif TKJ semester genap.', $draft], ['SMK', $jurusans['AKL']->id, 'SPP Bulanan', 575000, 'Rencana tarif AKL semester genap.', $draft]]; $fees = [];
        foreach ($definitions as [$level, $major, $category, $amount, $note, $year]) { $fee = Spp::updateOrCreate(['tahun_ajaran_id' => $year->id, 'jenjang' => $level, 'jurusan_id' => $major, 'kategori' => $category], ['nominal' => $amount, 'keterangan' => $note, 'is_active' => true]); if ($year->is($active)) $fees[$level.':'.($major ?? 'all').':'.$category] = $fee; }
        return $fees;
    }

    private function seedInvoicesAndPayments(array $students, array $fees, User $admin): void
    {
        foreach (array_slice($students, 0, 18) as $index => $student) {
            $key = $student->jenjang === 'SMK' ? 'SMK:'.$student->jurusan_id.':SPP Bulanan' : $student->jenjang.':all:SPP Bulanan'; $fee = $fees[$key] ?? null; if (! $fee) continue;
            $invoice = Tagihan::generateDariSpp($student, $fee, 2026, 10, '2026-10-10'); $invoice->update(['keterangan' => 'Tagihan SPP Oktober 2026']);
            if ($index % 3 === 0 && ! Pembayaran::where('tagihan_id', $invoice->id)->exists()) $invoice->bayar($index % 2 === 0 ? $fee->nominal : $fee->nominal / 2, 'Transfer', $admin->id, 'Pembayaran melalui bank', Carbon::parse('2026-10-08'));
        }
    }

    private function seedPpdb(User $admin, array $jurusans): void
    {
        $one = PpdbGelombang::updateOrCreate(['tahun_pendaftaran' => 2027, 'nama_gelombang' => 'Gelombang 1'], ['tanggal_mulai' => '2026-10-15', 'tanggal_selesai' => '2027-01-15', 'status' => 'Dibuka', 'deskripsi' => 'Pendaftaran jalur reguler, prestasi, dan afirmasi untuk tahun ajaran 2027/2028.', 'dibuka_pada' => '2026-10-15 08:00:00', 'ditutup_pada' => null]);
        $two = PpdbGelombang::updateOrCreate(['tahun_pendaftaran' => 2027, 'nama_gelombang' => 'Gelombang 2'], ['tanggal_mulai' => '2027-01-20', 'tanggal_selesai' => '2027-03-20', 'status' => 'Draft', 'deskripsi' => 'Gelombang lanjutan apabila kuota masih tersedia.']);
        PpdbGelombangRiwayat::updateOrCreate(['ppdb_gelombang_id' => $one->id, 'aksi' => 'Gelombang dibuka'], ['user_id' => $admin->id, 'status_sebelum' => 'Draft', 'status_sesudah' => 'Dibuka', 'catatan' => 'Jadwal PPDB 2027 dipublikasikan.', 'terjadi_pada' => '2026-10-15 08:00:00']);
        PpdbGelombangRiwayat::updateOrCreate(['ppdb_gelombang_id' => $two->id, 'aksi' => 'Gelombang dibuat'], ['user_id' => $admin->id, 'status_sebelum' => null, 'status_sesudah' => 'Draft', 'catatan' => 'Menunggu evaluasi kuota gelombang pertama.', 'terjadi_pada' => '2026-10-15 08:05:00']);
        foreach ([['Nadia Putri', '0098765432', 'Diperiksa', 'SMA', null], ['Rama Prabowo', '0098765433', 'Lengkap', 'SMK', $jurusans['RPL']->id], ['Sinta Aulia', '0098765434', 'Diterima', 'SMK', $jurusans['TKJ']->id]] as $index => [$name, $nisn, $status, $level, $major]) {
            PendaftaranMuridBaru::updateOrCreate(['nisn' => $nisn], ['ppdb_gelombang_id' => $one->id, 'nama_lengkap' => $name, 'nik' => '3273'.str_pad((string) (90000000 + $index), 12, '0', STR_PAD_LEFT), 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '2011-0'.($index + 2).'-12', 'jenis_kelamin' => $index === 1 ? 'Laki-Laki' : 'Perempuan', 'agama' => 'Islam', 'no_hp' => '0812345678'.$index, 'email' => 'calon'.($index + 1).'@mail.test', 'alamat' => 'Kota Bandung, Jawa Barat', 'kelurahan' => 'Sukamaju', 'kecamatan' => 'Cicendo', 'kota_kabupaten' => 'Bandung', 'provinsi' => 'Jawa Barat', 'sekolah_asal' => 'SMP Negeri '.($index + 1).' Bandung', 'tahun_lulus' => 2027, 'nilai_rapor' => ['matematika' => 88, 'bahasa_indonesia' => 86], 'nama_ayah' => 'Bapak '.$name, 'pekerjaan_ayah' => 'Wiraswasta', 'nama_ibu' => 'Ibu '.$name, 'pekerjaan_ibu' => 'Ibu Rumah Tangga', 'no_telp_ortu' => '0812987654'.$index, 'sekolah_pilihan_1' => Setting::where('key', 'school_name')->value('value'), 'jurusan_pilihan_1' => $major ? ($major === $jurusans['RPL']->id ? $jurusans['RPL']->nama : $jurusans['TKJ']->nama) : 'IPA', 'jenjang_pilihan' => $level, 'jurusan_id' => $major, 'document_upload_mode' => 'terpisah', 'status' => $status, 'catatan' => 'Data contoh untuk pemantauan proses PPDB.']);
        }
    }

    private function seedWebsite(User $admin): void
    {
        foreach ([['Selamat Datang', 'Sekolah yang menumbuhkan prestasi akademik, karakter, dan kepedulian sosial.', 'hero-campus.jpg', 1], ['Eksplorasi Sains dan Teknologi', 'Ruang belajar kolaboratif untuk karya, riset, dan inovasi peserta didik.', 'hero-lab.jpg', 2], ['PPDB Tahun Ajaran 2027/2028 Dibuka', 'Cek jadwal, biaya, dan formulir pendaftaran awal secara online.', 'achievement-olympiad.jpg', 3]] as [$title, $description, $photo, $order]) Slider::updateOrCreate(['judul' => $title], ['deskripsi' => $description, 'foto' => 'seed-images/'.$photo, 'urutan' => $order, 'is_active' => true]);
        foreach ([['Laboratorium Komputer', 'Laboratorium', 'Dua ruang laboratorium dengan perangkat untuk pembelajaran informatika dan pemrograman.', 72, 'Baik', 'Gedung Teknologi', 'facility-computer.jpg'], ['Perpustakaan dan Ruang Baca', 'Literasi', 'Ruang baca, koleksi cetak, dan area diskusi kelompok bagi warga sekolah.', 1, 'Baik', 'Gedung Utama', 'facility-library.jpg'], ['Gelanggang Olahraga', 'Olahraga', 'Lapangan multifungsi untuk pendidikan jasmani dan kegiatan ekstrakurikuler.', 1, 'Baik', 'Area Timur', 'facility-sports.jpg'], ['Laboratorium IPA', 'Laboratorium', 'Peralatan praktikum fisika, kimia, dan biologi yang mendukung pembelajaran berbasis eksperimen.', 1, 'Baik', 'Gedung Sains', 'hero-lab.jpg']] as [$name, $category, $description, $total, $condition, $location, $photo]) Fasilitas::updateOrCreate(['nama_fasilitas' => $name], ['kategori' => $category, 'deskripsi' => $description, 'jumlah' => $total, 'kondisi' => $condition, 'lokasi' => $location, 'foto' => 'seed-images/'.$photo]);
        foreach ([['Pembelajaran Kolaboratif di Kampus', 'Siswa berdiskusi dan beraktivitas di lingkungan sekolah.', 'hero-campus.jpg'], ['Praktikum Riset Sains', 'Eksperimen dan pemecahan masalah menjadi bagian pembelajaran.', 'hero-lab.jpg'], ['Ruang Baca dan Literasi', 'Koleksi dan ruang nyaman untuk membaca serta berdiskusi.', 'facility-library.jpg'], ['Kelas Pemrograman', 'Pembelajaran teknologi dan kreativitas digital.', 'facility-computer.jpg'], ['Pembinaan Olahraga', 'Kegiatan kebugaran dan kerja sama tim.', 'facility-sports.jpg'], ['Apresiasi Seni Siswa', 'Panggung ekspresi seni dan budaya sekolah.', 'activity-arts.jpg']] as [$title, $description, $photo]) Gallery::updateOrCreate(['judul' => $title], ['deskripsi' => $description, 'foto' => 'seed-images/'.$photo]);
        $categories = collect([['Pengumuman', 'pengumuman'], ['Prestasi', 'prestasi'], ['Akademik', 'akademik'], ['Kesiswaan', 'kesiswaan'], ['Literasi', 'literasi']])->mapWithKeys(fn ($item) => [$item[1] => Kategori::updateOrCreate(['slug' => $item[1]], ['nama_kategori' => $item[0]])]);
        $tags = collect(['PPDB', 'Prestasi', 'Sains', 'Literasi', 'Kesiswaan'])->mapWithKeys(fn ($name) => [strtolower($name) => Tag::firstOrCreate(['slug' => strtolower($name)], ['nama_tag' => $name])]);
        foreach ([['PPDB Tahun Ajaran 2027/2028 Resmi Dibuka', 'pengumuman', 'achievement-olympiad.jpg', 'Panitia PPDB membuka pendaftaran gelombang pertama. Calon peserta didik dapat memeriksa jadwal, persyaratan, biaya, dan mengisi formulir pendaftaran awal melalui halaman PPDB.', ['PPDB']], ['Tim Olimpiade Sains Raih Prestasi Tingkat Kota', 'prestasi', 'achievement-olympiad.jpg', 'Peserta didik SMA Nusantara meraih penghargaan pada kompetisi sains tingkat kota. Sekolah mengapresiasi ketekunan siswa dan guru pembimbing selama proses persiapan.', ['Prestasi', 'Sains']], ['Pekan Eksplorasi Sains dan Teknologi 2026', 'akademik', 'hero-lab.jpg', 'Pekan eksplorasi menghadirkan pameran proyek, praktikum terbuka, dan presentasi solusi teknologi sederhana dari peserta didik.', ['Sains']], ['Perpustakaan Menjadi Ruang Kolaborasi Siswa', 'literasi', 'facility-library.jpg', 'Program literasi pagi dan ruang diskusi mendorong siswa untuk membaca, bertukar gagasan, serta menyelesaikan proyek secara kolaboratif.', ['Literasi']], ['Gelar Karya Seni dan Budaya Siswa', 'kesiswaan', 'activity-arts.jpg', 'Kegiatan gelar karya mempertemukan musik, tari, seni rupa, dan kepanitiaan siswa dalam satu perayaan pembelajaran.', ['Kesiswaan']]] as [$title, $category, $photo, $content, $tagNames]) { $post = Post::updateOrCreate(['slug' => str($title)->slug()], ['user_id' => $admin->id, 'kategori_id' => $categories[$category]->id, 'judul' => $title, 'konten' => '<p>'.$content.'</p>', 'thumbnail' => 'seed-images/'.$photo, 'status' => 'Published', 'views' => 25, 'meta_title' => $title, 'meta_description' => $content]); $post->tags()->sync(collect($tagNames)->map(fn ($name) => $tags[strtolower($name)]->id)); }
        $post = Post::where('slug', str('PPDB Tahun Ajaran 2027/2028 Resmi Dibuka')->slug())->first(); if ($post) PostComment::updateOrCreate(['post_id' => $post->id, 'email' => 'orangtua@example.test'], ['nama' => 'Orang Tua Calon Siswa', 'komentar' => 'Informasi jadwal PPDB sangat membantu. Terima kasih.', 'status' => 'Approved']);
        WebsitePopup::updateOrCreate(['judul' => 'PPDB Tahun Ajaran 2027/2028'], ['gambar' => 'seed-images/achievement-olympiad.jpg', 'link' => '/ppdb/daftar', 'display_interval_hours' => 24, 'is_active' => true]);
    }

    private function password(): string
    {
        return $this->defaultPassword ??= Hash::make('password');
    }
}
