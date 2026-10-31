# Panduan Data Engineering & Backlog Teknis (`sistemsekolah`)

> **Tujuan Dokumen:** Memudahkan Agent AI maupun Engineer memahami struktur database, aliran data (*data pipelines*), aturan integritas domain, serta daftar tugas optimasi (*actionable backlog*) lengkap dengan lokasi file dan baris kode.

---

## 1. Peta Entitas & Skema Database Inti

### 1.1 Domain Akademik & Periodisasi
| Tabel | Model | Unique Constraints & Indexes | Peran Data |
| :--- | :--- | :--- | :--- |
| `tahun_ajarans` | `App\Models\TahunAjaran` | `UNIQUE(tahun, semester)` | Dimensi waktu akademik (`tanggal_mulai`, `tanggal_selesai`, `is_active`, `status`: `Draft`/`Aktif`/`Ditutup`). Method penting: `TahunAjaran::forDate($date)`. |
| `jurusans` | `App\Models\Jurusan` | `UNIQUE(kode)` | Master jurusan (khusus jenjang SMK). |
| `kelas` | `App\Models\Kelas` | `SoftDeletes` | Master nama kelas (`nama_kelas`, `jenjang`, `jurusan_id`, `wali_kelas_id` aktif). |
| `rombels` | `App\Models\Rombel` | `UNIQUE(kelas_id, tahun_ajaran_id)`, `UNIQUE(tahun_ajaran_id, wali_kelas_id)` | Instansi kelas pada periode akademik tertentu (`tahun_ajaran_id` FK `restrictOnDelete`). |
| `anggota_rombels` | `App\Models\AnggotaRombel` | `UNIQUE(rombel_id, siswa_id)`, `INDEX(siswa_id)` | **Fact/Bridge Table SCD Type 2** keanggotaan siswa di rombel (`status`, `tanggal_masuk`, `tanggal_keluar`). |
| `siswas` | `App\Models\Siswa` | `UNIQUE(user_id)`, `UNIQUE(nisn)` (nullable), `UNIQUE(nis)`, `SoftDeletes` | Master profil siswa. `kelas_id` hanya penunjuk kelas aktif saat ini. |
| `gurus` | `App\Models\Guru` | `UNIQUE(user_id)`, `UNIQUE(nip)` | Master profil guru & wali kelas. |
| `mapels` | `App\Models\Mapel` | `UNIQUE(kode_mapel)` | Master mata pelajaran, menyimpan konfigurasi penilaian (`kkm`, `bobot_harian`, `bobot_pts`, `bobot_pas`). |
| `jadwals` | `App\Models\Jadwal` | `SoftDeletes`, `INDEX(tahun_ajaran_id, ...)` | Jadwal KBM per `kelas_id` + `tahun_ajaran_id` + `mapel_id` + `guru_id` + `ruangan_id`. |

### 1.2 Domain KBM (Transaksional Akademik)
| Tabel | Model | Unique Constraints | Struktur & Aturan |
| :--- | :--- | :--- | :--- |
| `absensis` | `App\Models\Absensi` | `UNIQUE(siswa_id, tanggal)` | Rekap kehadiran harian (`hadir`, `sakit`, `izin`, `alpa`). Diblokir pada hari Minggu/libur (`kalender_akademiks`). |
| `nilais` | `App\Models\Nilai` | `UNIQUE(siswa_id, mapel_id, tahun_ajaran_id)` | Menyimpan `nilai_harian` (JSON: `{t1, t2, t3}`), `pts`, `pas`, `skor_remedial`. Accessor: `rata_harian`, `nilai_akhir`, `predikat`, `deskripsi`. |
| `rapors` | `App\Models\Rapor` | `UNIQUE(siswa_id, tahun_ajaran_id)` | Agregasi nilai & kehadiran per semester (`total_sakit`, `total_izin`, `total_alpa`, JSON `ekskul`, `prestasi`, `karakter`, `is_locked`). |

### 1.3 Domain Keuangan (Invoice & Ledger)
| Tabel | Model | Indexes | Struktur & Aturan |
| :--- | :--- | :--- | :--- |
| `spps` | `App\Models\Spp` | FK `tahun_ajaran_id` (`restrictOnDelete`) | Master tarif biaya (`SPP Bulanan` atau biaya sekali bayar) per `jenjang` & `jurusan_id`. |
| `tagihans` | `App\Models\Tagihan` | `INDEX(siswa_id, tahun)`, `INDEX(status)`, `INDEX(jatuh_tempo)` | Invoice siswa (`nominal`, `potongan`, `bulan` 1-12 atau `null`, `status`: `Belum Lunas`, `Lunas Sebagian`, `Lunas`, `Dibatalkan`). |
| `pembayarans` | `App\Models\Pembayaran` | `INDEX(tagihan_id)`, `INDEX(tanggal_bayar)` | Riwayat transaksi pembayaran cicilan/lunas atas `tagihan_id`. |
| `pembayaran_spps` | `App\Models\PembayaranSpp` | *(Legacy)* | Tabel lama sebelum migrasi `2026_08_09_000006`. Jangan digunakan untuk fitur baru. |

### 1.4 Domain Akuisisi (PPDB), Alumni & Telemetri Web
| Tabel | Model | Peran Data |
| :--- | :--- | :--- |
| `ppdb_gelombangs` & `ppdb_gelombang_riwayats` | `PpdbGelombang`, `PpdbGelombangRiwayat` | Manajemen periode pendaftaran murid baru & audit log perubahan status gelombang. |
| `pendaftaran_murid_barus` | `PendaftaranMuridBaru` | Formulir lengkap calon siswa, path dokumen, koordinat rumah, JSON `nilai_rapor`. Konversi ke `User` + `Siswa` via `PendaftaranMuridBaruIndex::konversiKeSiswa()`. |
| `kegiatan_alumnis` | `KegiatanAlumni` | Tracer study alumni (kuliah, kerja, wirausaha) beserta status verifikasi. |
| `website_visits` | `WebsiteVisit` | Log trafik HTTP GET publik & deteksi ancaman (SQLi, XSS, Path Traversal, Scanner) via middleware `TrackWebsiteVisit`. |

---

## 2. Peta Pipeline ETL & Integrasi AI

1. **PPDB Document OCR (`app/Services/PpdbDocumentExtractionService.php`)**
   - Input: File gambar/PDF (`ijazah_skl`, `kartu_keluarga`, `dokumen_gabungan`).
   - Proses: Encode ke Base64 Data URL -> POST ke `https://api.openai.com/v1/responses` (`gpt-4o-mini`, format `json_object`) -> Normalisasi field di `PendaftaranMuridBaruIndex::applyExtractedData()`.
   - Aturan khusus KK: Nama calon siswa wajib ada terlebih dahulu (dari Ijazah/form) sebelum AI diizinkan mengekstrak data Ayah/Ibu dari tabel Kartu Keluarga.
2. **Geocoding Alamat KK (`app/Services/AddressGeocodingService.php`)**
   - Input: Array `[alamat, kelurahan, kecamatan, kota_kabupaten, provinsi]`.
   - Proses: GET ke `https://nominatim.openstreetmap.org/search` -> Mengembalikan string `"lat, lon"` ke kolom `koordinat_rumah`.
3. **Pemindaian Matriks Absensi Kertas (`app/Services/GeminiAiScanner.php`)**
   - Input: Foto lembar absensi bulanan (`RekapAbsensiIndex::scanAbsensi()`).
   - Proses: Resize gambar via GD (`maxDim = 1600px`, JPEG 80%) -> Kirim bersama JSON daftar urut siswa & tanggal libur ke `gemini-flash-latest` -> Validasi ulang di backend (lewati tanggal libur & cek `isDalamInterval` keanggotaan rombel) -> `Absensi::updateOrCreate()`.
4. **Impor/Ekspor Spreadsheet (`app/Imports/` & `app/Exports/`)**
   - `FormatNilaiExport` & `FormatNilaiImport`: Unduh/unggah nilai per kelas & mapel.
   - `MapelExport` & `MapelImport`: Ekspor/impor master mata pelajaran (`WithUpserts` berdasarkan `kode_mapel`).
   - `AlumniExport`: Ekspor data jejak alumni.

---

## 3. Daftar Backlog Optimasi & Perbaikan Data (Siap Dikerjakan Agent)

> **Instruksi untuk Agent AI:** Setiap kali selesai mengerjakan salah satu poin atau sub-tugas di bawah ini, **WAJIB** memperbarui tabel status ringkasan dan mengubah checkbox `- [ ]` menjadi `- [x]` pada dokumen ini.

### Tabel Ringkasan Status Backlog
| ID | Prioritas & Fokus Area | File Utama | Status | Tanggal Selesai / Catatan |
| :--- | :--- | :--- | :--- | :--- |
| **P1** | Optimasi Query N+1 & In-Memory Bottleneck pada Keuangan | `Tagihan.php`, `TransaksiPembayaranIndex.php`, `LaporanKeuanganIndex.php`, `2026_08_09_000010_add_unique_constraint_to_tagihans_table.php` | ✅ Selesai | 2026-10-30 (`withSum`, SQL `GROUP BY` target tagihan, batch load matriks SPP, unique constraint `tagihans`) |
| **P2** | Penegakan Kunci Rapor pada Impor Excel & Eager Loading KBM | `FormatNilaiImport.php`, `FormatNilaiExport.php`, `ERaporIndex.php` | ✅ Selesai | 2026-10-30 (validasi `is_locked` + `DB::transaction`, preload nilai di Export, eager load `nilais.mapel` di e-Rapor) |
| **P3** | Optimasi Query Time-Series Dashboard & Retensi `website_visits` | `Dashboard.php`, `WebsiteVisit.php`, `routes/console.php` | ✅ Selesai | 2026-10-30 (1 query agregasi 7 hari di Dashboard, `MassPrunable` 90/180 hari + jadwal harian `model:prune`) |
| **P4** | Eliminasi Loop Query Kalender Libur (31x), Slot Jadwal (8x) & PDF Eager Load | `KalenderAkademik.php`, `RekapAbsensiIndex.php`, `JadwalPelajaranIndex.php`, `PdfCetakController.php`, `KeuanganPdfController.php` | ✅ Selesai | 2026-10-30 (`getMapHariLiburBulan()` 1 query/bulan, in-memory slot check & eager load `guru.user`, `nilais.mapel` & `withSum` di PDF) |
| **P5** | Preload `TahunAjaran::forDate` & Batch Update `Siswa` saat Mutasi Rombel | `TahunAjaran.php`, `TransaksiPembayaranIndex.php`, `TahunAjaranIndex.php` | ✅ Selesai | 2026-10-30 (in-memory `$preloaded` pada `forDate()`, batch `whereIn('id', ...)->update()` saat salin rombel & kenaikan kelas) |
| **P6** | Single-Query Conditional Aggregation (`COUNT`/`CASE`) & Perbaikan Bug `orWhere` | `DataSiswaIndex.php`, `DataGuruIndex.php`, `PendaftaranMuridBaruIndex.php`, `WebsiteVisitIndex.php`, `JejakAlumniIndex.php` | ✅ Selesai | 2026-10-30 (gabung 3–4 query `count()` menjadi 1 `selectRaw`, bungkus `orWhere` pencarian dalam closure `where(fn)` agar tidak bocor melewati filter) |
| **P7** | Personalisasi Dashboard & Penguncian Otorisasi Role Guru | `Dashboard.php`, `sidebar.blade.php`, `RekapAbsensiIndex.php`, `ERaporIndex.php`, `PdfCetakController.php`, `ManajemenNilaiIndex.php`, `JadwalPelajaranIndex.php`, `KalenderAkademikIndex.php`, `BlogArtikelIndex.php`, `BlogArtikelForm.php` | ✅ Selesai | 2026-10-30 (Dashboard guru berbasis jadwal/perwalian hari ini, validasi wali kelas di absensi & e-rapor, batasi input nilai ke kelas yang diajar, read-only kalender, isolasi blog author) |
| **P8** | Penambahan Halaman Profil Saya & Pengaturan Akun | `ProfileIndex.php`, `SettingsIndex.php`, `header.blade.php`, `sidebar.blade.php`, `routes/web.php` | ✅ Selesai | 2026-10-30 (Halaman Profil terintegrasi biodata guru/staff, Halaman Pengaturan keamanan ubah password, preferensi tema, standar operasional KBM untuk admin) |
| **P9** | Audit Menyeluruh & Perbaikan Modul Civitas Akademik (Siswa, Guru, Pengguna) | `DataSiswaIndex.php`, `DataGuruIndex.php`, `DataGuruDetail.php`, `DataPenggunaIndex.php`, `routes/web.php` | ✅ Selesai | 2026-10-30 (Integritas Rombel saat nonaktif siswa, pencegahan cascade delete guru, penambahan reset password siswa & form tambah staf, eliminasi rute duplikat) |

---

### Prioritas 1 (P1): Perbaikan Query N+1 & In-Memory Bottleneck pada Keuangan
- **Lokasi**:
  - `app/Models/Tagihan.php` (baris 39–47)
  - `app/Livewire/Admin/Keuangan/TransaksiPembayaranIndex.php` (baris 137–302)
  - `app/Livewire/Admin/Keuangan/LaporanKeuanganIndex.php` (baris 155–271)
- **Masalah**:
  1. `Tagihan::getTotalTerbayarAttribute()` selalu memanggil `$this->pembayarans()->sum('nominal')`.
  2. `TransaksiPembayaranIndex::loadPaymentMatrix()` melakukan query `TahunAjaran::forDate()`, `Spp`, `Tagihan::generateDariSpp()`, `total_terbayar`, dan `pembayarans()->latest()->first()` di dalam loop 12 bulan (40+ query per klik siswa).
  3. `LaporanKeuanganIndex::hitungTargetTagihan()` memuat seluruh `Siswa::where('status', 'Aktif')->get()` ke memori hanya untuk menjumlahkan tarif SPP.
- **Checklist Eksekusi Agent**:
  - [x] **P1.1**: Di `Tagihan::getTotalTerbayarAttribute()`, gunakan nilai `pembayarans_sum_nominal` jika sudah di-*load* via `withSum('pembayarans', 'nominal')`, atau gunakan koleksi `pembayarans` jika `relationLoaded('pembayarans')`.
  - [x] **P1.2**: Di `LaporanKeuanganIndex::hitungTunggakan()`, tambahkan `->withSum('pembayarans', 'nominal')` saat mengambil `$allTagihans`.
  - [x] **P1.3**: Di `LaporanKeuanganIndex::hitungTargetTagihan()`, ganti `Siswa::where('status', 'Aktif')->get()` dengan `Siswa::where('status', 'Aktif')->selectRaw('jenjang, jurusan_id, COUNT(*) as total')->groupBy('jenjang', 'jurusan_id')->get()`.
  - [x] **P1.4**: Optimasi `TransaksiPembayaranIndex::loadPaymentMatrix()` agar memuat `withSum('pembayarans', 'nominal')` / relasi pembayaran tanpa N+1 di dalam loop 12 bulan, serta tambahkan pengamanan *unique constraint* `(siswa_id, spp_id, tahun, bulan)` pada `tagihans`.

### Prioritas 2 (P2): Penegakan Kunci Rapor pada Impor Excel & Eager Loading KBM
- **Lokasi**:
  - `app/Imports/FormatNilaiImport.php` (baris 24–54)
  - `app/Exports/FormatNilaiExport.php` (baris 32–47)
  - `app/Livewire/Admin/Kbm/ERaporIndex.php` (baris 221–246)
- **Masalah**:
  1. `FormatNilaiImport` belum memeriksa `Rapor::where('siswa_id', $siswaId)->where('tahun_ajaran_id', $this->tahun_ajaran_id)->where('is_locked', true)->exists()`, sehingga nilai rapor yang sudah dikunci wali kelas dapat tertimpa lewat unggahan Excel.
  2. `FormatNilaiExport::collection()` melakukan query `Nilai::where(...)` satu per satu di dalam `.map()`.
  3. `ERaporIndex::render()` melakukan eager load `nilais`, tetapi belum eager load `nilais.mapel`, memicu N+1 saat menghitung `$rp->rata_rata_nilai`.
- **Checklist Eksekusi Agent**:
  - [x] **P2.1**: Di `FormatNilaiImport::collection()`, preload daftar `siswa_id` yang rapornya terkunci (`is_locked = true`) pada `tahun_ajaran_id` tersebut di luar loop, lewati (`continue`) jika terkunci, dan bungkus proses simpan dalam `DB::transaction()`.
  - [x] **P2.2**: Di `FormatNilaiExport::collection()`, ambil seluruh `Nilai` sekaligus menggunakan `whereIn('siswa_id', ...)->get()->keyBy('siswa_id')` di luar `.map()`.
  - [x] **P2.3**: Di `ERaporIndex::render()`, ubah eager loading menjadi `'nilais' => fn($q) => $q->where('tahun_ajaran_id', $taId)->with('mapel')`.

### Prioritas 3 (P3): Optimasi Query Time-Series Dashboard & Retensi `website_visits`
- **Lokasi**:
  - `app/Livewire/Dashboard.php` (baris 21–43)
  - `app/Models/WebsiteVisit.php`
- **Masalah**:
  - Loop 7 hari di `Dashboard::render()` menjalankan 14 query `whereDate('visited_at', ...)` terpisah setiap kali halaman Dashboard dibuka.
- **Checklist Eksekusi Agent**:
  - [x] **P3.1**: Ganti loop 14 query di `Dashboard::render()` menjadi 1 query agregasi:
    ```php
    $startDate = now()->subDays(6)->startOfDay();
    $endDate = now()->endOfDay();
    $dailyStats = WebsiteVisit::whereBetween('visited_at', [$startDate, $endDate])
        ->selectRaw('DATE(visited_at) as visit_date, COUNT(*) as total, SUM(CASE WHEN is_suspicious = 1 THEN 1 ELSE 0 END) as danger')
        ->groupByRaw('DATE(visited_at)')
        ->get()
        ->keyBy('visit_date');
    ```
  - [x] **P3.2**: Tambahkan mekanisme pembersihan log lama (`MassPrunable` / jadwal retensi) pada `WebsiteVisit` agar tabel tidak membengkak tanpa batas.

### Prioritas 4 (P4): Eliminasi Loop Query Kalender Libur (31x), Slot Jadwal (8x) & PDF Eager Load
- **Lokasi**:
  - `app/Models/KalenderAkademik.php`
  - `app/Livewire/Admin/Kbm/RekapAbsensiIndex.php`
  - `app/Livewire/Admin/Kbm/JadwalPelajaranIndex.php`
  - `app/Http/Controllers/PdfCetakController.php`
  - `app/Http/Controllers/KeuanganPdfController.php`
- **Masalah**:
  1. `KalenderAkademik::isHariLibur()` dipanggil di dalam loop `1..$daysInMonth` (28–31 kali) pada `RekapAbsensiIndex::render()`, `RekapAbsensiIndex::scanAbsensi()`, dan `PdfCetakController::cetakTemplateAbsen()`, memicu ~26 query DB per render.
  2. `JadwalPelajaranIndex` belum eager-load `guru.user` (memicu N+1 untuk setiap kartu jadwal), memanggil 2 query `Setting` terpisah, dan menjalankan 8 query `Jadwal::where(...)->exists()` di dalam loop `for ($i = 1; $i <= 8; $i++)`.
  3. `PdfCetakController::cetak()` belum eager-load `siswa.nilais.mapel` saat menghitung ranking kelas, dan `KeuanganPdfController::cetakKuitansi()` belum memuat `withSum('pembayarans', 'nominal')` pada `tagihan`.
- **Checklist Eksekusi Agent**:
  - [x] **P4.1**: Tambahkan `KalenderAkademik::getMapHariLiburBulan($tahun, $bulan)` yang memuat hari libur sebulan penuh dalam 1 query `whereBetween`, dan gunakan di `RekapAbsensiIndex` serta `PdfCetakController`.
  - [x] **P4.2**: Di `JadwalPelajaranIndex`, tambahkan eager load `guru.user`, gabungkan query `Setting` menjadi `whereIn`, dan ganti 8 query slot waktu menjadi 1 preload query + pengecekan overlap di memori.
  - [x] **P4.3**: Tambahkan eager load `mapel` pada `siswa.nilais` di `PdfCetakController::cetak()` dan `withSum('pembayarans', 'nominal')` pada `tagihan` di `KeuanganPdfController::cetakKuitansi()`.

### Prioritas 5 (P5): Preload `TahunAjaran::forDate` & Batch Update `Siswa` saat Mutasi Rombel
- **Lokasi**:
  - `app/Models/TahunAjaran.php`
  - `app/Livewire/Admin/Keuangan/TransaksiPembayaranIndex.php`
  - `app/Livewire/Admin/DataMaster/TahunAjaranIndex.php`
- **Masalah**:
  1. `TransaksiPembayaranIndex::loadPaymentMatrix()` memanggil `TahunAjaran::forDate($tgl)` di dalam loop 12 bulan (12–24 query berulang).
  2. `TahunAjaranIndex::salinRombelDariSemesterSebelumnya()` dan `executeKenaikanKelas()` mengeksekusi query `Siswa::where('id', ...)->update(['kelas_id' => ...])` satu per satu di dalam loop ratusan siswa.
- **Checklist Eksekusi Agent**:
  - [x] **P5.1**: Tambahkan parameter opsional `?Collection $preloaded = null` pada `TahunAjaran::forDate()` dan preload `TahunAjaran::all()` sekali di luar loop 12 bulan pada `TransaksiPembayaranIndex`.
  - [x] **P5.2**: Ubah update `kelas_id` per siswa di `TahunAjaranIndex` menjadi batch `Siswa::whereIn('id', $siswaIdsToUpdate)->update(['kelas_id' => ...])` per rombel.

### Prioritas 6 (P6): Single-Query Conditional Aggregation (`COUNT`/`CASE`) & Perbaikan Bug `orWhere`
- **Lokasi**:
  - `app/Livewire/Admin/Civitas/DataSiswaIndex.php`
  - `app/Livewire/Admin/Civitas/DataGuruIndex.php`
  - `app/Livewire/Admin/Ppdb/PendaftaranMuridBaruIndex.php`
  - `app/Livewire/Admin/Website/WebsiteVisitIndex.php`
  - `app/Livewire/Admin/Alumni/JejakAlumniIndex.php`
- **Masalah**:
  1. Halaman daftar menjalankan 3–4 query `COUNT(*)` terpisah di setiap `render()` untuk kartu statistik.
  2. `WebsiteVisitIndex` menggunakan 3 `whereDate('visited_at', $today)` yang tidak ramah indeks B-Tree.
  3. Filter pencarian `orWhere` di `DataSiswaIndex`, `DataGuruIndex`, dan `JejakAlumniIndex` tidak dibungkus dalam closure `where(function ($sub) { ... })`, sehingga membocorkan hasil lintas filter (`filterJenjang`, `filterKelas`, `filterJabatan`, `filterStatus`) akibat operator precedence SQL `OR`.
- **Checklist Eksekusi Agent**:
  - [x] **P6.1**: Gabungkan 3–4 query `COUNT(*)` menjadi 1 query `selectRaw('COUNT(*) as ..., SUM(CASE WHEN ... END) as ...')` di `DataSiswaIndex`, `DataGuruIndex`, `PendaftaranMuridBaruIndex`, dan `WebsiteVisitIndex` (gunakan `whereBetween` untuk hari ini).
  - [x] **P6.2**: Bungkus kondisi pencarian `orWhere` ke dalam closure `where(function ($sub) { ... })` di `DataSiswaIndex`, `DataGuruIndex`, dan `JejakAlumniIndex`.

### Prioritas 7 (P7): Personalisasi Dashboard & Penguncian Otorisasi Role Guru
- **Lokasi**:
  - `app/Livewire/Dashboard.php` & `resources/views/livewire/dashboard.blade.php`
  - `resources/views/components/admin/sidebar.blade.php`
  - `app/Livewire/Admin/Kbm/RekapAbsensiIndex.php` & view
  - `app/Livewire/Admin/Kbm/ERaporIndex.php` & view
  - `app/Http/Controllers/PdfCetakController.php`
  - `app/Livewire/Admin/Kbm/ManajemenNilaiIndex.php`
  - `app/Livewire/Admin/Kbm/JadwalPelajaranIndex.php`
  - `app/Livewire/Admin/Akademik/KalenderAkademikIndex.php` & view
  - `app/Livewire/Admin/Website/BlogArtikelIndex.php` & `BlogArtikelForm.php`
- **Masalah**:
  1. Halaman `/dashboard` menampilkan data global web admin (kunjungan web, grafik lalu lintas, serangan keamanan, PPDB) alih-alih aktivitas akademik guru.
  2. Sidebar menampilkan menu dropdown kosong "Pengaturan Website" dan teks "Admin Panel" saat login sebagai Guru.
  3. Guru dapat memanipulasi parameter URL `filterKelas` untuk melihat/mengubah absensi dan mengedit/mengunci e-rapor kelas yang bukan perwaliannya.
  4. Guru dapat mencetak PDF rapor dan template absen kelas siswa lain.
  5. Guru dapat menginput nilai untuk kelas atau mapel yang tidak diajarnya.
  6. Guru memiliki akses menulis/menghapus jadwal pelajaran, kalender akademik, dan artikel guru lain.
- **Checklist Eksekusi Agent**:
  - [x] **P7.1**: Rombak tampilan Dashboard untuk Guru: menampilkan nama kelas perwalian & jumlah siswa (jika wali kelas), rekap absensi hari ini, progres e-Rapor terkunci, jadwal mengajar aktif hari ini, total jam mengajar mingguan, dan tautan cepat modul KBM.
  - [x] **P7.2**: Bersihkan sidebar navigasi: sembunyikan dropdown "Pengaturan Website" untuk Guru dan ganti sub-label menjadi "Panel Guru".
  - [x] **P7.3**: Pasang verifikasi Wali Kelas di backend `RekapAbsensiIndex` dan `ERaporIndex` (`loadData`, `updateAbsensi`, `scanAbsensi`, `openEditModal`, `simpanRapor`, `toggleLock`) sehingga tidak dapat ditembus manipulasi URL.
  - [x] **P7.4**: Pasang validasi otorisasi wali kelas di `PdfCetakController::cetak()` dan `cetakTemplateAbsen()`.
  - [x] **P7.5**: Kunci akses input nilai (`ManajemenNilaiIndex`) agar guru hanya dapat mengisi nilai pada kelas dan mata pelajaran yang tercantum dalam jadwal mengajarnya (`Jadwal`).
  - [x] **P7.6**: Kunci akses manajemen `JadwalPelajaranIndex` dan `KalenderAkademikIndex` menjadi *Read-Only* bagi Guru, serta batasi `BlogArtikelIndex` agar guru hanya dapat mengelola artikel tulisannya sendiri.

### Prioritas 8 (P8): Penambahan Halaman Profil Saya & Pengaturan Akun
- **Lokasi**:
  - `app/Livewire/Admin/Profile/ProfileIndex.php` & `resources/views/livewire/admin/profile/profile-index.blade.php`
  - `app/Livewire/Admin/Profile/SettingsIndex.php` & `resources/views/livewire/admin/profile/settings-index.blade.php`
  - `resources/views/components/admin/header.blade.php`
  - `resources/views/components/admin/sidebar.blade.php`
  - `routes/web.php`
  - `tests/Feature/ProfileAndSettingsTest.php`
- **Masalah**:
  1. Header dropdown tombol "Profil Saya" dan "Pengaturan" sebelumnya tidak memiliki link (`href="#"`).
  2. Guru dan Administrator belum memiliki antarmuka terpadu untuk memperbarui informasi kontak pribadi, no telepon, tempat/tanggal lahir, alamat, dan biodata guru.
  3. Pengguna belum memiliki fasilitas mandiri untuk mengubah kata sandi dengan validasi kata sandi saat ini (`current_password`), serta opsi preferensi tema.
  4. Pengaturan jam masuk dan durasi per jam KBM belum dapat diatur secara fleksibel oleh Administrator melalui antarmuka pengaturan.
- **Checklist Eksekusi Agent**:
  - [x] **P8.1**: Buat rute `/profil` (`admin.profile`) dan komponen Livewire `ProfileIndex` untuk mengelola akun (nama, email) serta sinkronisasi otomatis ke biodata guru (`gurus`).
  - [x] **P8.2**: Buat rute `/pengaturan` (`admin.settings`) dan komponen Livewire `SettingsIndex` untuk ganti kata sandi yang aman (validasi `current_password`), preferensi tema (Light/Dark via Alpine/LocalStorage), dan konfigurasi operasional KBM (khusus Admin).
  - [x] **P8.3**: Hubungkan dropdown menu header ("Profil Saya" dan "Pengaturan") serta sidebar user card ke rute yang baru, jadikan label tahun ajaran dan status role di header dinamis dari database.
  - [x] **P8.4**: Tulis automated feature test komprehensif di `ProfileAndSettingsTest.php` (6 skenario pengujian lulus 100%).

### Prioritas 9 (P9): Audit Menyeluruh & Perbaikan Modul Civitas Akademik
- **Lokasi**:
  - `app/Livewire/Admin/Civitas/DataSiswaIndex.php` & `data-siswa-index.blade.php`
  - `app/Livewire/Admin/Civitas/DataGuruIndex.php` & `data-guru-index.blade.php`
  - `app/Livewire/Admin/Civitas/DataGuruDetail.php` & `data-guru-detail.blade.php`
  - `app/Livewire/Admin/Civitas/DataPenggunaIndex.php` & `data-pengguna-index.blade.php`
  - `routes/web.php`
  - `tests/Feature/CivitasAkademikTest.php`
- **Masalah**:
  1. Penonaktifan siswa di `DataSiswaIndex::delete()` tidak memperbarui record `AnggotaRombel` (melanggar Aturan Emas `AGENTS.md`) dan tidak mematikan login user (`is_active = false`), sehingga siswa tetap terdaftar aktif di e-Rapor/absensi.
  2. Ketiadaan aksi reset password instan bagi siswa di `DataSiswaIndex`.
  3. Query `$formClasses` di `DataSiswaIndex` dieksekusi terus-menerus pada setiap render meski modal tertutup.
  4. Penghapusan guru di `DataGuruIndex::delete()` melakukan hard delete cascade yang menghapus seluruh jadwal mengajar historis dan artikel blog, serta dapat mengosongkan wali kelas aktif tanpa validasi.
  5. Detail jadwal guru memuat jadwal lampau tanpa filter tahun ajaran aktif, dan pencarian guru di Blade tidak ter-debounce.
  6. Data Pengguna tidak memiliki form tambah staf/pengguna baru dari UI web, role inti (`guru`, `siswa`, `alumni`) belum diproteksi di backend, dan rute civitas terduplikasi di `routes/web.php`.
- **Checklist Eksekusi Agent**:
  - [x] **P9.1**: Perbaiki `DataSiswaIndex::delete()` agar menonaktifkan `User` dan mengupdate `AnggotaRombel` aktif (`status = 'Pindah'`, `tanggal_keluar = now()`).
  - [x] **P9.2**: Tambahkan method `resetPassword($siswaId)` dan tombol aksi di antarmuka Data Siswa.
  - [x] **P9.3**: Optimasi query form kelas di `DataSiswaIndex` agar lazy-loaded saat modal terbuka saja dan bersihkan role alumni jika status siswa dibatalkan dari lulus.
  - [x] **P9.4**: Lindungi penghapusan guru di `DataGuruIndex` dengan validasi perwalian & jadwal aktif, serta sediakan opsi penonaktifan akun (suspend).
  - [x] **P9.5**: Filter jadwal mengajar aktif di `DataGuruDetail` berdasarkan tahun ajaran aktif dan pasang debounce pada input pencarian guru.
  - [x] **P9.6**: Tambahkan fitur form modal "Tambah Staf / Pengguna Baru" di `DataPenggunaIndex`, proteksi role sistem di backend, dan auto-inisialisasi data guru saat role `guru` di-assign.
  - [x] **P9.7**: Gabungkan deklarasi rute ganda Civitas Akademik di `routes/web.php` menjadi satu blok terpadu.
  - [x] **P9.8**: Tulis feature test komprehensif `CivitasAkademikTest.php` dan pastikan 100% lulus.




