# Panduan Audit & Perbaikan Civitas Akademik

Dokumen ini mencatat hasil audit komprehensif, analisis akar masalah (*root cause*), arsitektur relasi, serta rincian perbaikan teknis pada modul **Civitas Akademik** (`Data Siswa`, `Data Guru`, `Data Pengguna`) di sistem sekolah.

---

## 1. Ringkasan Temuan Audit

### 1.1 Integritas Data Siswa & Rombel (Data Siswa)
* **Akar Masalah**:
  Saat admin mengklik tombol nonaktifkan (`delete($id)`) pada `DataSiswaIndex`, sistem hanya melakukan `$siswa->update(['status' => 'Pindah'])`. 
  Tindakan ini melanggar **Aturan Emas Data Modeling** (`AGENTS.md` Bagian 2.A) karena tidak memperbarui tabel `anggota_rombels` dan tidak menonaktifkan akun `users`.
* **Dampak Sistem**:
  Karena `anggota_rombels.tanggal_keluar` tetap `NULL` dan status anggota tetap `'Aktif'`, query scope KBM (`Siswa::aktifDiKelasPadaTahunAjaran` dan `inKelasPadaTanggal`) tetap menganggap siswa tersebut aktif di kelas, sehingga siswa tetap muncul di form input nilai, e-Rapor, dan absensi harian guru. Siswa juga masih bisa login ke portal.
* **Fitur Hilang**:
  Data Siswa tidak memiliki tombol 1-klik untuk mereset kata sandi siswa ketika siswa lupa kata sandi.

### 1.2 Keamanan Penghapusan Guru & Eager Loading Jadwal (Data Guru)
* **Akar Masalah**:
  Model `User` dan `Guru` tidak menggunakan `SoftDeletes`. Menghapus guru via `$guru->user->delete()` langsung memicu `ON DELETE CASCADE` foreign key database.
* **Dampak Sistem**:
  1. Seluruh rekam jejak jadwal mengajar (`jadwals`) guru terhapus permanen dari database.
  2. Seluruh artikel blog (`posts`) yang ditulis guru terhapus permanen.
  3. Rombel kelas aktif yang dibimbing guru mendadak kehilangan wali kelas (`nullOnDelete`), menimbulkan inkonsistensi data pada panel guru.
  4. Halaman `DataGuruDetail` memuat seluruh riwayat jadwal mengajar dari semua tahun ajaran lampau tanpa filter semester aktif.
* **Performa Input**:
  Input pencarian guru di Blade tidak menggunakan Livewire debounce (`wire:model` biasa alih-alih `wire:model.live.debounce.300ms`).

### 1.3 Manajemen Staf & Proteksi Role (Data Pengguna)
* **Akar Masalah**:
  1. Halaman `DataPenggunaIndex` tidak memiliki antarmuka (tombol & modal) untuk menambahkan akun staf/pengguna baru secara langsung melalui web.
  2. Method `deleteRole()` di backend hanya melindungi role `['admin', 'operator']`, sehingga role penting `['guru', 'siswa', 'alumni']` berisiko terhapus melalui manipulasi wire action.
  3. Ketika role pengguna diubah menjadi `'guru'` via `assignRole()`, tabel relasi `gurus` tidak diinisialisasi sehingga `auth()->user()->guru` bernilai `null` dan dashboard guru mengalami anomali.
* **Duplikasi Routing**:
  Prefix `civitas` dideklarasikan 2 kali di `routes/web.php` dan rute `admin.civitas.data-guru` didaftarkan berulang.

---

## 2. Daftar Tugas & Checklist Implementasi

### Bagian A: Perbaikan Data Siswa (`DataSiswaIndex.php` & Blade)
- [x] **A.1**: Perbaiki method `delete($id)` pada `DataSiswaIndex`:
  - Update `siswas.status = 'Pindah'`.
  - Nonaktifkan akun login user: `$siswa->user?->update(['is_active' => false])`.
  - Sinkronkan `anggota_rombels` pada tahun ajaran aktif: `status = 'Pindah'`, `tanggal_keluar = now()->toDateString()`.
- [x] **A.2**: Tambahkan method `resetPassword($siswaId)` pada `DataSiswaIndex`:
  - Reset kata sandi user siswa ke format tanggal lahir `dmY` (jika ada) atau default `siswa123`.
  - Tambahkan tombol aksi "Reset Password" dengan konfirmasi di tabel [`data-siswa-index.blade.php`].
- [x] **A.3**: Optimasi query form kelas di `DataSiswaIndex::render()`:
  - Hanya muat `$formClasses` ketika `$isModalOpen === true`.
  - Batasi kolom query kelas: `select('id', 'nama_kelas', 'jenjang', 'jurusan_id')`.
- [x] **A.4**: Sinkronisasi status kelulusan:
  - Jika status siswa diubah kembali dari `Lulus` ke `Aktif`, bersihkan `tahun_lulus` dan cabut role `alumni`.

### Bagian B: Perbaikan Data Guru (`DataGuruIndex`, `DataGuruDetail`, `DataGuruForm` & Blade)
- [x] **B.1**: Amankan penghapusan guru pada `DataGuruIndex::delete()`:
  - Validasi apakah guru masih menjabat sebagai wali kelas aktif di `Rombel` atau memiliki jadwal KBM aktif di tahun ajaran aktif.
  - Jika aktif, cegah penghapusan dan tampilkan notifikasi peringatan.
  - Sediakan opsi alternatif penonaktifan akun (`toggleStatus` / suspend) agar jadwal dan histori blog tidak terhapus.
- [x] **B.2**: Filter jadwal mengajar pada `DataGuruDetail`:
  - Muat jadwal mengajar hanya untuk tahun ajaran aktif (`tahun_ajaran_id = $taAktif->id`).
  - Tampilkan label tahun ajaran aktif dan total jam mengajar mingguan pada profil detail guru.
- [x] **B.3**: Pasang `wire:model.live.debounce.300ms="search"` pada input pencarian di `data-guru-index.blade.php`.
- [x] **B.4**: Sinkronkan indikator wali kelas di `DataGuruIndex` dengan `rombels` aktif.

### Bagian C: Perbaikan Data Pengguna & Routing (`DataPenggunaIndex.php`, Blade, `routes/web.php`)
- [x] **C.1**: Tambahkan fitur "Tambah Staf / Pengguna Baru":
  - Buat properti form modal (`name`, `email`, `password`, `role`, `staff_position`).
  - Tambahkan method `createUser()` dengan validasi unik email, hash password, assignment role Spatie, dan pencatatan `staff_position`.
  - Tambahkan tombol dan modal form di `data-pengguna-index.blade.php`.
- [x] **C.2**: Perketat proteksi `deleteRole()` di backend untuk melindungi `['admin', 'operator', 'guru', 'siswa', 'alumni']`.
- [x] **C.3**: Pastikan `assignRole($userId, 'guru')` menginisialisasi profil guru jika belum ada.
- [x] **C.4**: Rapikan deklarasi rute Civitas Akademik di `routes/web.php` menjadi satu grup terpadu tanpa duplikasi.

### Bagian D: Verifikasi & Automated Testing
- [x] **D.1**: Buat test suite komprehensif `tests/Feature/CivitasAkademikTest.php` untuk memverifikasi seluruh skenario di atas.
- [x] **D.2**: Pastikan seluruh test suite proyek (62 tests) tetap lulus 100% tanpa regresi.
- [x] **D.3**: Sinkronisasi dokumen `docs/data-engineering-guide.md` (Prioritas 9).

