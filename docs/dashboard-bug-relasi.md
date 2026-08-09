# Dokumentasi Branch `dashboard-bug-relasi`

Dokumen ini merangkum pekerjaan di branch `dashboard-bug-relasi` agar engineer atau model AI lain bisa memahami konteks tanpa membaca ulang seluruh diff.

## Tujuan

Branch ini memperbaiki masalah relasi historis siswa, kelas, wali kelas, jadwal, nilai, absensi, dan e-Rapor saat sistem masuk tahun ajaran/semester berikutnya.

Masalah awal:

- `siswas.kelas_id` hanya menyimpan kelas aktif saat ini.
- Saat siswa naik kelas, data historis seperti roster nilai, absensi, dan rapor tahun ajaran lama bisa salah karena query masih membaca `siswas.kelas_id`.
- `kelas.wali_kelas_id` membuat wali kelas bersifat global, bukan periodik.
- `jadwals` belum terkait tahun ajaran, sehingga jadwal semester/tahun baru berpotensi bercampur dengan jadwal lama.

## Commit Utama

- `2eec81f Fix academic year class relationships`
- `7b0a777 Refactor class and relationship management by replacing KelasSiswa with AnggotaRombel and Rombel models...`

## Desain Relasi Baru

Struktur final yang dipakai:

```text
kelas
  id
  nama_kelas
  jenjang
  jurusan_id
  wali_kelas_id    # masih ada untuk kompatibilitas kelas aktif

rombels
  id
  kelas_id
  tahun_ajaran_id
  wali_kelas_id
  kapasitas
  status

anggota_rombels
  id
  rombel_id
  siswa_id
  status
  tanggal_masuk
  tanggal_keluar
```

Makna relasi:

- `kelas` tetap menjadi master nama kelas, misalnya `VII A`, `VIII A`, `X RPL 1`.
- `rombels` adalah instansi kelas pada periode tertentu, misalnya `VII A - 2026/2027 Ganjil`.
- `anggota_rombels` menyimpan keanggotaan siswa pada rombel periode tertentu.
- `siswas.kelas_id` tetap dipertahankan sebagai cache/kelas aktif untuk kompatibilitas fitur lama.
- Sumber kebenaran historis untuk roster siswa adalah `anggota_rombels -> rombels`, bukan `siswas.kelas_id`.

## Migration Baru

### `2026_08_08_000001_create_rombels_and_anggota_rombels_table.php`

Membuat:

- `rombels`
- `anggota_rombels`

Constraint penting:

- Satu `kelas_id` hanya boleh punya satu rombel untuk satu `tahun_ajaran_id`.
- Satu `wali_kelas_id` hanya boleh dipakai sekali dalam satu `tahun_ajaran_id`.
- Satu siswa hanya boleh sekali masuk ke rombel yang sama.

Backfill:

- Data `rapors` dipakai untuk membuat histori rombel lama.
- Data `siswas.kelas_id` dipakai untuk mengisi rombel pada tahun ajaran aktif.
- `wali_kelas_id` awal rombel diambil dari `kelas.wali_kelas_id`.

### `2026_08_08_000002_add_tahun_ajaran_id_to_jadwals_table.php`

Menambahkan `tahun_ajaran_id` ke `jadwals`.

Tujuannya:

- Jadwal tidak lagi global.
- Jadwal bisa berbeda antar semester/tahun ajaran.
- Deteksi bentrok guru, kelas, dan ruangan hanya dihitung dalam periode yang sama.

Backfill:

- Jadwal lama diberi `tahun_ajaran_id` dari tahun ajaran aktif.

### `2026_08_08_000003_drop_unique_wali_kelas_from_kelas_table.php`

Menghapus unique constraint lama dari `kelas.wali_kelas_id`.

Alasan:

- Wali kelas sekarang periodik di `rombels.wali_kelas_id`.
- Guru yang pernah menjadi wali kelas pada periode lama tetap boleh menjadi wali kelas lain pada periode berbeda.

## Model Baru

### `App\Models\Rombel`

Relasi:

- `kelas()`
- `tahunAjaran()`
- `waliKelas()`
- `anggotaRombels()`
- `siswas()`

### `App\Models\AnggotaRombel`

Relasi:

- `rombel()`
- `siswa()`

Field tanggal:

- `tanggal_masuk`
- `tanggal_keluar`

## Perubahan Model Existing

### `Siswa`

Ditambahkan:

- `anggotaRombels()`
- `rombels()`
- `scopeInKelasPadaTahunAjaran($query, $kelasId, $tahunAjaranId)`
- `kelasPadaTahunAjaran($tahunAjaranId)`
- `rombelPadaTahunAjaran($tahunAjaranId)`

Catatan penting:

- Gunakan `Siswa::inKelasPadaTahunAjaran($kelasId, $tahunAjaranId)` untuk fitur historis.
- Jangan gunakan `where('kelas_id', ...)` untuk nilai, absensi, e-Rapor, atau laporan yang bergantung periode.

### `Kelas`

Ditambahkan:

- `rombels()`

`siswas()` tetap ada untuk kelas aktif/kompatibilitas lama.

### `Guru`

Ditambahkan:

- `rombels()`

Ini dipakai untuk mengetahui wali kelas periodik.

### `TahunAjaran`

Ditambahkan:

- `rombels()`
- `forDate($date)`

`forDate()` dipakai oleh absensi untuk menentukan semester/tahun ajaran dari tanggal bulan absensi.

## Perubahan Alur Fitur

### Data Siswa

File:

- `app/Livewire/Admin/Civitas/DataSiswaIndex.php`

Saat siswa disimpan:

1. Data siswa tetap disimpan ke `siswas`.
2. `siswas.kelas_id` tetap diisi sebagai kelas aktif.
3. Sistem mencari/membuat `rombel` untuk `kelas_id + tahun_ajaran_aktif`.
4. Sistem membuat/memperbarui `anggota_rombels`.
5. Jika siswa pindah kelas di tahun ajaran aktif, anggota rombel lain pada tahun ajaran yang sama dihapus.

### Data Kelas

File:

- `app/Livewire/Admin/DataMaster/DataKelasIndex.php`

Saat kelas disimpan:

1. Master `kelas` dibuat/diperbarui.
2. Jika ada tahun ajaran aktif, sistem membuat/memperbarui `rombel`.
3. Wali kelas divalidasi per tahun ajaran aktif melalui `rombels`, bukan unique global di `kelas`.

Saat siswa dikeluarkan dari kelas:

- `siswas.kelas_id` diset `null`.
- Keanggotaan siswa pada rombel tahun ajaran aktif dihapus.

### Jadwal Pelajaran

File:

- `app/Livewire/Admin/Kbm/JadwalPelajaranIndex.php`
- `resources/views/livewire/admin/kbm/jadwal-pelajaran-index.blade.php`

Perubahan:

- Ada filter `filterTahunAjaran`.
- Jadwal disimpan dengan `tahun_ajaran_id`.
- Query jadwal difilter berdasarkan `tahun_ajaran_id`.
- Deteksi bentrok guru/kelas/ruangan memakai `tahun_ajaran_id`.

### Manajemen Nilai

File:

- `app/Livewire/Admin/Kbm/ManajemenNilaiIndex.php`
- `app/Exports/FormatNilaiExport.php`
- `app/Imports/FormatNilaiImport.php`

Perubahan:

- Roster siswa diambil dari `Siswa::inKelasPadaTahunAjaran(...)`.
- Export nilai memakai roster rombel periode terpilih.
- Import nilai memvalidasi bahwa siswa memang anggota kelas pada tahun ajaran tersebut.
- Guru hanya melihat kelas/mapel dari jadwal pada tahun ajaran yang dipilih.

### Rekap Absensi

File:

- `app/Livewire/Admin/Kbm/RekapAbsensiIndex.php`
- `app/Http/Controllers/PdfCetakController.php`

Perubahan:

- Tahun ajaran absensi ditentukan dari tanggal bulan/tahun filter menggunakan `TahunAjaran::forDate()`.
- Roster absensi diambil dari rombel periode tersebut.
- Template PDF absensi juga memakai roster rombel.
- Wali kelas di template PDF absensi diambil dari `rombels.wali_kelas_id` jika tersedia.

### e-Rapor

File:

- `app/Livewire/Admin/Kbm/ERaporIndex.php`
- `app/Http/Controllers/PdfCetakController.php`

Perubahan:

- Roster e-Rapor diambil dari rombel pada tahun ajaran yang dipilih.
- Untuk role guru/wali kelas, daftar kelas diambil dari `rombels` sesuai `wali_kelas_id + tahun_ajaran_id`.
- PDF rapor memakai kelas dan wali kelas historis dari rombel periode rapor.
- Ranking PDF diperbaiki dengan eager load nilai siswa untuk tahun ajaran terkait.

## Seeder

File:

- `database/seeders/DummyDataSeeder.php`

Perubahan:

- Seeder membuat `Rombel` untuk setiap kelas pada tahun ajaran aktif.
- Seeder memasukkan siswa ke `AnggotaRombel`.
- Jadwal dummy diberi `tahun_ajaran_id`.

## Test

File:

- `tests/Feature/RombelHistoryTest.php`

Yang dites:

- Siswa yang sekarang kelas baru tetap muncul di kelas lama jika filter tahun ajaran lama dipakai.
- `kelasPadaTahunAjaran()` dan `rombelPadaTahunAjaran()` mengembalikan data periode yang benar.
- `TahunAjaran::forDate()` bisa menentukan Ganjil/Genap dari tanggal.

Catatan:

- PHPUnit belum bisa dijalankan di lingkungan shell ini karena `php` tidak tersedia di PATH.
- `git diff --check` sudah bersih saat dokumentasi ini dibuat.

## Panduan Untuk AI/Developer Berikutnya

Gunakan pola ini:

```php
Siswa::inKelasPadaTahunAjaran($kelasId, $tahunAjaranId)
```

Untuk mengambil kelas historis siswa:

```php
$siswa->kelasPadaTahunAjaran($tahunAjaranId);
```

Untuk mengambil rombel historis siswa:

```php
$siswa->rombelPadaTahunAjaran($tahunAjaranId);
```

Untuk mencari tahun ajaran dari tanggal absensi:

```php
TahunAjaran::forDate($tanggal);
```

Hindari pola ini untuk fitur periodik:

```php
Siswa::where('kelas_id', $kelasId)
```

Pola tersebut hanya aman untuk tampilan kelas aktif atau fitur lama yang memang tidak membutuhkan histori.

## Area Yang Belum Diselesaikan

Beberapa area masih memakai `siswas.kelas_id` karena sifatnya masih kelas aktif/sederhana:

- Data master siswa.
- Statistik data kelas aktif.
- Beberapa laporan keuangan sederhana.

PPDB ke siswa aktif juga belum dibuat otomatis. Konversi PPDB sebaiknya memiliki pilihan kelas/rombel tujuan agar calon siswa yang diterima bisa langsung masuk ke `anggota_rombels` tahun ajaran yang tepat.
