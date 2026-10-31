# AGENTS.md — Panduan Agent AI (Sistem Informasi Sekolah)

Dokumen ini menjadi acuan otomatis (*project rules & context*) bagi Agent AI yang bekerja pada repositori **Sistem Informasi Sekolah** (Laravel 13, Livewire 4, Tailwind CSS, Spatie Permission, Maatwebsite Excel, DomPDF).

---

## 1. Referensi Dokumentasi Proyek (`docs/`)

Sebelum membaca banyak file dari awal, **baca dokumentasi di folder `docs/` terlebih dahulu**:
- [`docs/data-engineering-guide.md`](docs/data-engineering-guide.md) — **Arsitektur Data Engineering, Skema Database, Aturan Query, Pipeline ETL/AI, dan Daftar Backlog Optimasi.**
- [`docs/dashboard-bug-relasi2-dan-relasi3.md`](docs/dashboard-bug-relasi2-dan-relasi3.md) — Konteks perubahan skema periodik (`Rombel`, `AnggotaRombel`, `Tagihan`, `Pembayaran`, kenaikan kelas, dan PPDB).
- [`docs/dashboard-bug-relasi.md`](docs/dashboard-bug-relasi.md) — Sejarah migrasi awal relasi kelas & tahun ajaran.

---

## 2. Aturan Emas Pemodelan & Query Data (WAJIB DIIKUTI)

### A. Keanggotaan Kelas & Periode Akademik (`Rombel` vs `siswas.kelas_id`)
- Sumber kebenaran (*Single Source of Truth*) keanggotaan kelas per periode adalah `anggota_rombels -> rombels`, **BUKAN** `siswas.kelas_id`.
- Kolom `siswas.kelas_id` dan `kelas.wali_kelas_id` hanya dipertahankan sebagai *cache/pointer* kondisi aktif saat ini.
- **Gunakan Scope pada Model `App\Models\Siswa`**:
  - Roster aktif pada periode tertentu (Manajemen Nilai, e-Rapor):
    ```php
    Siswa::aktifDiKelasPadaTahunAjaran($kelasId, $tahunAjaranId)
    ```
  - Histori siswa yang pernah berada di kelas pada periode tertentu:
    ```php
    Siswa::inKelasPadaTahunAjaran($kelasId, $tahunAjaranId)
    ```
  - Keanggotaan kelas pada satu tanggal spesifik (Absensi harian):
    ```php
    Siswa::inKelasPadaTanggal($kelasId, $tanggal)
    ```
  - Keanggotaan kelas yang *overlap* pada rentang tanggal (Rekap absensi bulanan):
    ```php
    Siswa::inKelasPadaRentangTanggal($kelasId, $mulai, $selesai)
    ```
- **Jangan menghapus (`DELETE`) record `AnggotaRombel`** saat siswa pindah kelas, dikeluarkan, atau kelas dihapus. Gunakan update `status = 'Pindah'` (atau `'Lulus'`/`'Dikeluarkan'`) dan isi `tanggal_keluar = now()->toDateString()`.

### B. Keuangan (`Tagihan` & `Pembayaran`)
- Sistem keuangan menggunakan arsitektur **Invoice (`tagihans`) + Ledger (`pembayarans`)**. Jangan menambah fitur baru ke tabel legacy `pembayaran_spps`.
- Gunakan `Tagihan::generateDariSpp($siswa, $spp, $tahun, $bulan, $jatuhTempo)` untuk membuat tagihan dan `$tagihan->bayar(...)` untuk mencatat transaksi pembayaran (sudah memvalidasi anti-overpayment dan `refreshStatus()`).
- **Pencegahan N+1**: Accessor `$tagihan->total_terbayar` dan `$tagihan->sisa_tagihan` menjalankan query `SUM(nominal)` jika tidak di-*eager load*. Saat memuat banyak `Tagihan`, gunakan `withSum('pembayarans', 'nominal')`.

### C. KBM (Nilai, Absensi, e-Rapor)
- **Kunci Rapor (`rapors.is_locked`)**: Setiap mutasi nilai (`Nilai`) baik dari UI maupun **Impor Excel (`FormatNilaiImport`)** wajib memeriksa apakah `Rapor` siswa pada `tahun_ajaran_id` tersebut berstatus `is_locked = true`.
- **Hari Libur Absensi**: Setiap mutasi absensi (`Absensi`) baik manual maupun via AI Scanner wajib memvalidasi `KalenderAkademik::isHariLibur($tanggal)` dan keanggotaan tanggal siswa.
- **Eager Loading Nilai**: Saat menghitung `$nilai->nilai_akhir` atau `$rapor->rata_rata_nilai` untuk daftar siswa, selalu *eager load* `nilais.mapel` karena perhitungan bobot dan KKM membaca relasi `mapel`.

### D. Aktivasi Tahun Ajaran & Migrasi Database
- Status `TahunAjaran` (`Draft`, `Aktif`, `Ditutup`, `Diarsipkan`) hanya boleh diaktifkan melalui mekanisme satu pintu di `TahunAjaranIndex` (`toggleStatus` untuk tahun sama / `executeKenaikanKelas` untuk tahun baru).
- Foreign key `tahun_ajaran_id` pada tabel akademik menggunakan `restrictOnDelete()`.
- Tulis query migrasi yang *portable* (kompatibel dengan MySQL/MariaDB dan SQLite untuk keperluan testing).

---

## 3. Protokol Sinkronisasi Backlog (`docs/data-engineering-guide.md`) — WAJIB

Setiap kali Agent mengerjakan item dari **Daftar Backlog Optimasi & Perbaikan Data** di [`docs/data-engineering-guide.md`](docs/data-engineering-guide.md):
1. **Sebelum mulai**: Periksa tabel ringkasan status dan checklist `[ ]` di Bagian 3 [`docs/data-engineering-guide.md`](docs/data-engineering-guide.md) agar tidak mengerjakan ulang item yang sudah berstatus `✅ Selesai`.
2. **Saat selesai mengerjakan item**:
   - Ubah checkbox `- [ ]` menjadi `- [x]` pada sub-tugas yang telah diselesaikan.
   - Perbarui kolom **Status** pada tabel ringkasan backlog dari `⏳ Belum Dikerjakan` menjadi `🔄 Sebagian` atau `✅ Selesai (YYYY-MM-DD)` beserta ringkasan singkat file yang diubah.
   - Jangan menghapus riwayat masalah/solusi di dokumen tersebut agar tetap menjadi referensi arsitektur bagi sesi berikutnya.

