# Panduan Otorisasi & Batasan Role Guru (KBM & Perwalian)

Dokumen ini menjelaskan arsitektur keamanan, pemodelan hak akses, serta batasan fungsional bagi pengguna dengan role **`guru`** pada aplikasi **Sistem Informasi Sekolah**.

---

## 1. Perbedaan Lingkup Akses: Admin vs Guru

| Fitur / Halaman | Admin / Operator | Guru (Wali Kelas) | Guru (Bukan Wali Kelas) |
| :--- | :--- | :--- | :--- |
| **Dashboard** | Statistik global siswa/guru, trafik kunjungan, keamanan web, ringkasan PPDB. | Ringkasan kelas perwalian, rekap absensi hari ini, progres e-Rapor, jadwal mengajar hari ini. | Jadwal mengajar hari ini, total jam mengajar, daftar mapel diampu, agenda akademik terdekat. |
| **Sidebar Navigation** | Seluruh menu (PPDB, Civitas, Master, Keuangan, Sarpras, Alumni, Pengaturan Website). | Dashboard, KBM & Laporan, Blog/Artikel. | Dashboard, KBM & Laporan, Blog/Artikel. |
| **Rekap Absensi Harian** | Semua kelas (Read & Write, AI Scanner). | **Hanya kelas perwaliannya** pada tahun ajaran aktif. | ❌ Tidak dapat melihat/mengubah absensi. |
| **Manajemen Nilai** | Semua kelas dan semua mata pelajaran. | **Hanya kelas & mapel yang diajar** sesuai tabel `jadwals`. | **Hanya kelas & mapel yang diajar** sesuai tabel `jadwals`. |
| **e-Rapor** | Semua kelas, kelengkapan catatan, kunci rapor. | **Hanya kelas perwaliannya** pada tahun ajaran aktif. | ❌ Tidak dapat mengakses perwalian e-rapor. |
| **Cetak PDF Rapor & Absen** | Semua siswa dan kelas. | **Hanya siswa di kelas perwaliannya**. | ❌ Ditolak (HTTP 403 Forbidden). |
| **Jadwal Pelajaran** | CRUD Jadwal, pendeteksi bentrok. | **Read-Only**: Otomatis terkunci pada jadwal guru tersebut. | **Read-Only**: Terkunci pada jadwal guru tersebut. |
| **Kalender Akademik** | CRUD Hari Libur, Sinkronisasi API, Atur Masuk. | **Read-Only**: Melihat agenda & mencari hari libur. | **Read-Only**: Melihat agenda & mencari hari libur. |
| **Artikel & Blog** | CRUD Artikel semua penulis, kelola Tag & Kategori. | Menulis artikel & hanya dapat menyunting/menghapus tulisan miliknya sendiri. | Menulis artikel & hanya dapat menyunting/menghapus tulisan miliknya sendiri. |

---

## 2. Mekanisme Keamanan di Tingkat Backend (Anti-Bypass)

Semua validasi hak akses telah ditanamkan langsung pada method PHP backend di Livewire Component dan Controller, sehingga **manipulasi parameter URL (seperti `?filterKelas=ID_LAIN`) tidak dapat membobol data**:

1. **Rekap Absensi (`App\Livewire\Admin\Kbm\RekapAbsensiIndex`)**:
   - `validateWaliKelasAccess()`: Memastikan `Rombel::where('wali_kelas_id', $guru->id)->where('kelas_id', $this->filterKelas)` ada di database untuk periode akademik yang dipilih.
   - Dipasang pada `loadData()`, `updateAbsensi()`, dan `scanAbsensi()`.
2. **e-Rapor (`App\Livewire\Admin\Kbm\ERaporIndex`)**:
   - Dipasang pada `openEditModal()`, `simpanRapor()`, `toggleLock()`, dan `render()`.
3. **Cetak PDF (`App\Http\Controllers\PdfCetakController`)**:
   - Pada `cetak()`, sistem memvalidasi keanggotaan siswa dalam `Rombel` perwalian guru sebelum merender PDF rapor.
   - Pada `cetakTemplateAbsen()`, sistem memvalidasi perwalian kelas sebelum menghasilkan template cetak.
4. **Manajemen Nilai (`App\Livewire\Admin\Kbm\ManajemenNilaiIndex`)**:
   - `validateGuruTeachingAccess()`: Memeriksa apakah terdapat jadwal KBM aktif di tabel `jadwals` untuk kombinasi `(guru_id, kelas_id, mapel_id, tahun_ajaran_id)`.
   - Dipasang pada `loadData()`, `saveScore()`, `simpanPengaturan()`, `downloadExcel()`, dan `importExcel()`.
5. **Jadwal Pelajaran & Kalender Akademik**:
   - Action mutasi data (`simpanJadwal`, `executeHapus`, `save`, `delete`, `syncNationalHolidays`, `generateBulkHolidays`) dilindungi dengan `abort_unless(auth()->user()->hasRole('admin') || auth()->user()->hasRole('operator'), 403)`.
6. **Blog & Artikel**:
   - Guru otomatis dibatasi melihat dan menghapus hanya artikel yang memiliki `user_id == auth()->id()`.

---

## 3. Daftar File yang Diubah & Ditambahkan

1. [`app/Livewire/Dashboard.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/app/Livewire/Dashboard.php)
2. [`resources/views/livewire/dashboard.blade.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/resources/views/livewire/dashboard.blade.php)
3. [`resources/views/components/admin/sidebar.blade.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/resources/views/components/admin/sidebar.blade.php)
4. [`app/Livewire/Admin/Kbm/RekapAbsensiIndex.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/app/Livewire/Admin/Kbm/RekapAbsensiIndex.php)
5. [`resources/views/livewire/admin/kbm/rekap-absensi-index.blade.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/resources/views/livewire/admin/kbm/rekap-absensi-index.blade.php)
6. [`app/Livewire/Admin/Kbm/ERaporIndex.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/app/Livewire/Admin/Kbm/ERaporIndex.php)
7. [`resources/views/livewire/admin/kbm/e-rapor-index.blade.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/resources/views/livewire/admin/kbm/e-rapor-index.blade.php)
8. [`app/Http/Controllers/PdfCetakController.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/app/Http/Controllers/PdfCetakController.php)
9. [`app/Livewire/Admin/Kbm/ManajemenNilaiIndex.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/app/Livewire/Admin/Kbm/ManajemenNilaiIndex.php)
10. [`app/Livewire/Admin/Kbm/JadwalPelajaranIndex.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/app/Livewire/Admin/Kbm/JadwalPelajaranIndex.php)
11. [`app/Livewire/Admin/Akademik/KalenderAkademikIndex.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/app/Livewire/Admin/Akademik/KalenderAkademikIndex.php)
12. [`resources/views/livewire/admin/akademik/kalender-akademik-index.blade.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/resources/views/livewire/admin/akademik/kalender-akademik-index.blade.php)
13. [`app/Livewire/Admin/Website/BlogArtikelIndex.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/app/Livewire/Admin/Website/BlogArtikelIndex.php)
14. [`resources/views/livewire/admin/website/blog-artikel-index.blade.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/resources/views/livewire/admin/website/blog-artikel-index.blade.php)
15. [`app/Livewire/Admin/Website/BlogArtikelForm.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/app/Livewire/Admin/Website/BlogArtikelForm.php)
16. [`tests/Feature/RoleGuruAuthorizationTest.php`](file:///d:/PROJECT%20WEB/sistem%20sekolah/sistemsekolah/tests/Feature/RoleGuruAuthorizationTest.php) *(6 skenario pengujian otorisasi lolos 100%)*

