# Dokumentasi Pengerjaan — `dashboard-bug-relasi` Series

> **Tujuan:** Dokumen ini menyimpan konteks lengkap pengerjaan branch `dashboard-bug-relasi`, `dashboard-bug-relasi2`, dan `dashboard-bug-relasi3` agar Agent AI berikutnya tidak perlu membaca ulang seluruh file dari awal.

**Tanggal pengerjaan:** 2026-08-09
**Branch aktif:** `dashboard-bug-relasi3`

---

## 📌 Latar Belakang

Sistem sekolah awalnya memiliki desain database yang hanya mendukung **satu periode aktif**. Relasi antar entitas (siswa, kelas, jadwal, nilai, absensi, rapor) tidak bersifat periodik, sehingga saat sistem masuk tahun ajaran/semester baru:

- Data historis rusak
- Roster siswa tidak mencerminkan periode yang benar
- Jadwal bentrok antar tahun ajaran
- Keuangan tidak mendukung cicilan
- PPDB tidak terintegrasi dengan data siswa
- Penghapusan kelas menghapus siswa

---

## 🌿 Branch History

| Branch | Fokus | Status |
|---|---|---|
| `dashboard-bug-relasi` | Rombel + AnggotaRombel, Jadwal periodik, SoftDeletes | ✅ Merged |
| `dashboard-bug-relasi2` | Keuangan (Tagihan + Pembayaran), Salin Rombel, Kenaikan Kelas | ✅ Merged ke relasi3 |
| `dashboard-bug-relasi3` | Fix blocker: migration safety, activation workflow, roster scope, PPDB identity, kuitansi partial | 🔄 Aktif |

---

## 🗄️ Semua Migration Baru (urut)

| # | File | Isi |
|---|---|---|
| `2026_08_08_000001` | `create_rombels_and_anggota_rombels_table.php` | Tabel `rombels` (kelas_id, tahun_ajaran_id, wali_kelas_id, kapasitas, status) + `anggota_rombels` (rombel_id, siswa_id, status, tanggal_masuk, tanggal_keluar). Unique: `[kelas_id, tahun_ajaran_id]`, `[tahun_ajaran_id, wali_kelas_id]`, `[rombel_id, siswa_id]` |
| `2026_08_08_000002` | `add_tahun_ajaran_id_to_jadwals_table.php` | Tambah `tahun_ajaran_id` FK (nullable, nullOnDelete) ke `jadwals` + 3 index. Backfill dari tahun ajaran aktif. |
| `2026_08_08_000003` | `drop_unique_wali_kelas_from_kelas_table.php` | Hapus unique constraint lama `kelas.wali_kelas_id` — wali kelas sekarang periodik di `rombels` |
| `2026_08_09_000001` | `add_tanggal_and_unique_to_tahun_ajarans_table.php` | Tambah `tanggal_mulai`, `tanggal_selesai`, `UNIQUE(tahun, semester)` di `tahun_ajarans`. **Preflight:** deteksi duplikat → abort dengan pesan perbaikan manual (tidak auto-delete untuk lindungi data akademik). |
| `2026_08_09_000002` | `add_status_to_tahun_ajarans_and_restrict_fk.php` | Tambah `status` (Draft/Aktif/Ditutup/Diarsipkan) ke `tahun_ajarans`. Ubah FK `nilais`, `rapors`, `spps`, `rombels` dari `cascadeOnDelete` → `restrictOnDelete`. Backfill status: aktif→'Aktif', nonaktif→'Ditutup'. |
| `2026_08_09_000003` | `add_unique_constraint_to_nilais_table.php` | `UNIQUE(siswa_id, mapel_id, tahun_ajaran_id)` di `nilais`. Dedup sebelum constraint (query portable). |
| `2026_08_09_000004` | `add_unique_siswa_tanggal_to_absensis_table.php` | `UNIQUE(siswa_id, tanggal)` di `absensis`. Dedup pakai query builder portable (bukan MySQL JOIN DELETE — kompatibel SQLite). |
| `2026_08_09_000005` | `create_tagihans_and_pembayarans_tables.php` | Tabel `tagihans` (siswa_id, spp_id, jenis_biaya, tahun, bulan, nominal, potongan, jatuh_tempo, status) + `pembayarans` (tagihan_id, tanggal_bayar, nominal, metode, petugas_id) |
| `2026_08_09_000006` | `backfill_pembayaran_spps_to_tagihans.php` | Migrasi data dari `pembayaran_spps` lama → `tagihans` + `pembayarans`. Menangani potongan dengan benar: tagihan.nominal=full, tagihan.potongan=diskon, pembayaran=netto. |
| `2026_08_09_000007` | `add_unique_constraint_to_rapors_table.php` | `UNIQUE(siswa_id, tahun_ajaran_id)` di `rapors`. Dedup sebelum constraint. |
| `2026_08_09_000008` | `make_nisn_nullable_in_siswas_table.php` | Ubah `siswas.nisn` jadi nullable (dukung PPDB tanpa NISN) |
| `2026_08_09_000009` | `add_potongan_to_tagihans_table.php` | Tambah kolom `potongan` (decimal 12,2) ke `tagihans` |

---

## 🧩 Model Changes

### Model Baru

| Model | Relasi Kunci |
|---|---|
| `Rombel` | `kelas()`, `tahunAjaran()`, `waliKelas()`, `anggotaRombels()`, `siswas()` |
| `AnggotaRombel` | `rombel()`, `siswa()` — field: `status`, `tanggal_masuk`, `tanggal_keluar` |
| `Tagihan` | `siswa()`, `spp()`, `pembayarans()` — accessor: `total_terbayar`, `sisa_tagihan` (= nominal - total_terbayar - potongan), `persentase_terbayar`, `nama_bulan` |
| `Pembayaran` | `tagihan()`, `petugas()` — accessor: `nama_siswa`, `jenis_biaya` |

### Model yang Diupdate

| Model | Perubahan |
|---|---|
| `Siswa` | +`anggotaRombels()`, `rombels()`, `tagihans()`. Scope: `inKelasPadaTahunAjaran()` (histori), `aktifDiKelasPadaTahunAjaran()` (roster aktif, filter status=Aktif & tanggal_keluar=null), `inKelasPadaTanggal()` (absensi per tanggal). |
| `Kelas` | +`rombels()`. `SoftDeletes` sudah ada. |
| `Guru` | +`rombels()` (wali kelas periodik). |
| `TahunAjaran` | +`rombels()`, `$casts['tanggal_mulai','tanggal_selesai','is_active']`. Method: `forDate()` (prioritas by date range, fallback semester-based), `canBeDeleted()` (hanya Draft). |
| `Jadwal` | +`tahunAjaran()`, +`use SoftDeletes`. |
| `Spp` | +`tagihans()`. |
| `Tagihan` | Method: `bayar()` (validasi anti-overpayment), `refreshStatus()`, `generateDariSpp()` |

---

## 🎯 Arsitektur Final

```
TahunAjaran
    ├── Rombel ─── Kelas
    │     ├── wali_kelas (Guru)
    │     └── AnggotaRombel ─── Siswa
    │           ├── Absensi
    │           ├── Nilai
    │           ├── Rapor
    │           └── Tagihan ─── Pembayaran
    │
    ├── Jadwal ─── Mapel, Guru, Ruangan
    │     └── (kelas_id + tahun_ajaran_id = ekuivalen rombel_id)
    │
    └── SPP
```

**Catatan:** `Jadwal` menggunakan `kelas_id + tahun_ajaran_id` (bukan `rombel_id`) sebagai trade-off pragmatis — fungsional ekuivalen karena `[kelas_id, tahun_ajaran_id]` unique di `rombels`.

---

## 🔄 Workflow Kunci

### 1. Aktivasi Periode (SATU PINTU)

```
Periode Draft
     │
     ├─ Tahun SAMA → klik ✅ (toggleStatus)
     │     └─ Tawarkan "Salin Rombel" dari semester sebelumnya
     │
     └─ Tahun BEDA → klik → (Kenaikan Kelas)
           └─ Preview: VII→VIII, VIII→IX, IX→🎓Lulus, X→XI, XI→XII, XII→🎓Lulus
           └─ Execute: DB::transaction → buat rombel + pindahkan siswa + aktifkan TA
```

- **Form `save()` tidak bisa set `status = Aktif`** — hanya bisa via `toggleStatus()` atau `executeKenaikanKelas()`
- `toggleStatus()` menolak aktivasi tahun berbeda → arahkan ke Kenaikan Kelas

### 2. Kenaikan Kelas

Parser level eksplisit (bukan substring):
- Roman: VII→VIII, VIII→IX, IX→Lulus, X→XI, XI→XII, XII→Lulus
- Numeric: 7→8, 8→9, 9→Lulus, 10→11, 11→12, 12→Lulus
- Suffix dipertahankan (A, RPL 1, IPS 2)
- Filter: hanya siswa `status=Aktif`, `tanggal_keluar IS NULL`, `siswa.status=Aktif`

### 3. Salin Rombel (Ganjil↔Genap)

- Copy rombel dari semester sebelumnya dalam tahun yang sama
- Filter anggota: hanya `status=Aktif`, `tanggal_keluar=null`, `siswa.status=Aktif`
- Siswa yang sudah pindah (`status=Pindah`) tidak ikut disalin

### 4. PPDB → Siswa

```
PPDB Diterima → Klik ✅ Konversi
  ↓
1. Generate NIS (auto-increment, BUKAN dari NISN)
2. Cek email: jika sudah dipakai orang lain → generate email baru
3. Hanya pakai email existing jika milik siswa yang SAMA (cocok by NISN)
4. NISN hanya diisi jika ada dari PPDB (nullable)
5. Buat User + Siswa + status PPDB → Aktif
```

### 5. Keuangan

- **Transaksi:** `TransaksiPembayaranIndex` → `Tagihan` + `Pembayaran`
- **Kuitansi:** `KeuanganPdfController` → `Pembayaran` (dengan `tagihan.siswa`, `tagihan.spp`)
- **Laporan:** `LaporanKeuanganIndex` → `Pembayaran` + `Tagihan`
- **Overpayment dicegah:** `Tagihan::bayar()` validasi `nominal <= sisa_tagihan`
- **Tunggakan akurat:** `Lunas Sebagian` hanya dihitung sebesar `sisa_tagihan`, bukan nominal penuh
- **Backfill:** Data `pembayaran_spps` lama dimigrasi dengan benar (potongan diperhitungkan)

### 6. Penghapusan Kelas

- Kelas di-soft-delete
- Siswa **TIDAK** dihapus/dinonaktifkan — hanya dikeluarkan (`kelas_id=null`)
- AnggotaRombel di-update (`status=Pindah`, `tanggal_keluar=now()`) — **BUKAN** dihapus
- Rombel ditutup (`status=Ditutup`)

### 7. Pindah Kelas / Keluar dari Kelas

- `AnggotaRombel` lama di-update (`status=Pindah`, `tanggal_keluar`) — **BUKAN** dihapus
- `AnggotaRombel` baru dibuat (`status=Aktif`, `tanggal_masuk`)
- Histori keanggotaan tetap utuh untuk keperluan absensi/nilai historis

---

## 📁 Inventori File yang Diubah

### Livewire Components
| File | Perubahan Kunci |
|---|---|
| `Admin/DataMaster/DataKelasIndex.php` | Hapus kelas: siswa tidak dihapus. RemoveStudent: update instead of delete. |
| `Admin/DataMaster/TahunAjaranIndex.php` | Status sbg single source of truth. Aktivasi satu pintu. Salin Rombel. Kenaikan Kelas (parser level eksplisit). |
| `Admin/Civitas/DataSiswaIndex.php` | DB::transaction wrapper. Validasi jenjang sebelum User create. AnggotaRombel update instead of delete. |
| `Admin/Kbm/ManajemenNilaiIndex.php` | `inKelasPadaTahunAjaran` → `aktifDiKelasPadaTahunAjaran` |
| `Admin/Kbm/ERaporIndex.php` | `inKelasPadaTahunAjaran` → `aktifDiKelasPadaTahunAjaran` |
| `Admin/Kbm/RekapAbsensiIndex.php` | `inKelasPadaTahunAjaran` → `aktifDiKelasPadaTahunAjaran` |
| `Admin/Kbm/JadwalPelajaranIndex.php` | Filter & simpan dengan `tahun_ajaran_id`. Conflict check per TA. |
| `Admin/Ppdb/PendaftaranMuridBaruIndex.php` | Konversi PPDB→Siswa. NIS≠NISN. Email protection. Status baru: Daftar Ulang, Aktif. |
| `Admin/Keuangan/TransaksiPembayaranIndex.php` | Tagihan + Pembayaran. `sisa_tagihan` untuk bayar. |
| `Admin/Keuangan/LaporanKeuanganIndex.php` | Model baru. Tunggakan partial dihitung akurat. |
| `Admin/Keuangan/MasterSppIndex.php` | Tidak ada perubahan struktural. |

### Models
| File | Perubahan |
|---|---|
| `Rombel.php` | 🆕 Baru |
| `AnggotaRombel.php` | 🆕 Baru |
| `Tagihan.php` | 🆕 Baru |
| `Pembayaran.php` | 🆕 Baru |
| `Siswa.php` | +3 scope, +relasi |
| `Kelas.php` | +rombels() |
| `Guru.php` | +rombels() |
| `TahunAjaran.php` | +status, +tanggal, +forDate(), +canBeDeleted() |
| `Jadwal.php` | +SoftDeletes, +tahunAjaran() |
| `Spp.php` | +tagihans() |

### Views
| File | Perubahan |
|---|---|
| `admin/data-master/tahun-ajaran-index.blade.php` | Status badge, dropdown tanpa 'Aktif' (kecuali edit), modal Salin Rombel + Kenaikan Kelas |
| `admin/ppdb/pendaftaran-murid-baru-index.blade.php` | Tombol konversi (✅), status color baru |
| `admin/keuangan/transaksi-pembayaran-index.blade.php` | Status Lunas Sebagian (amber + %), riwayat dari Pembayaran |
| `admin/keuangan/laporan-keuangan-index.blade.php` | Kolom dari tagihan.siswa, tagihan.spp, petugas |
| `pdf/kuitansi-spp.blade.php` | Adaptasi model baru. Stempel LUNAS vs PEMBAYARAN SEBAGIAN. |

### Controllers
| File | Perubahan |
|---|---|
| `Http/Controllers/KeuanganPdfController.php` | `PembayaranSpp` → `Pembayaran` + `Tagihan` |
| `Http/Controllers/PdfCetakController.php` | Roster absensi dari rombel |

### Tests
| File | Perubahan |
|---|---|
| `Feature/DataKelasSoftDeleteTest.php` | Assert siswa TIDAK dihapus, akun tetap aktif |
| `Feature/LandingPagesTest.php` | Tambah field `status` di TahunAjaran::create |
| `Feature/RombelHistoryTest.php` | (tidak berubah — sudah benar) |

---

## ⚠️ Area yang Belum Diselesaikan

1. **Test coverage:** `RombelHistoryTest` hanya cover basic. Belum ada test untuk: kenaikan kelas, salin rombel setelah pindah, PPDB tanpa NISN, email conflict, backfill potongan, partial payment, aktivasi tanpa rollover.

2. **Jadwal → Rombel:** Masih menggunakan `kelas_id + tahun_ajaran_id`, bukan `rombel_id`. Fungsional ekuivalen tapi kurang eksplisit.

3. **Jurusan redundan:** `siswas.jurusan_id` masih ada (redundan dengan `kelas.jurusan_id`).

4. **PPDB pipeline penuh:** Konversi PPDB→Siswa sudah ada, tapi belum ada placement otomatis ke rombel tujuan. Admin masih harus menempatkan manual via Data Siswa.

5. **`pembayaran_spps` lama:** Tabel masih ada untuk backward compatibility. Belum di-drop.

6. **PHPUnit:** Belum bisa dijalankan di environment saat ini (`php` tidak di PATH).

---

## 🔑 Pola Query yang HARUS digunakan

```php
// ✅ Roster aktif (Manajemen Nilai, e-Rapor, Absensi)
Siswa::aktifDiKelasPadaTahunAjaran($kelasId, $tahunAjaranId)

// ✅ Histori (laporan lama)
Siswa::inKelasPadaTahunAjaran($kelasId, $tahunAjaranId)

// ✅ Absensi per tanggal
Siswa::inKelasPadaTanggal($kelasId, $tanggal)

// ✅ Cari tahun ajaran dari tanggal
TahunAjaran::forDate($tanggal)

// ❌ JANGAN untuk fitur periodik
Siswa::where('kelas_id', $kelasId)  // hanya untuk tampilan kelas aktif
```

---

## 📝 Cara Melanjutkan

1. Agent AI berikutnya cukup membaca dokumen ini — **tidak perlu membaca ulang semua file.**
2. Jika perlu melihat detail implementasi, baca file spesifik yang disebutkan di inventori.
3. Branch aktif: `dashboard-bug-relasi3`
4. Semua migration sudah dibuat dan siap dijalankan dengan `php artisan migrate`
