# Rencana Portal Siswa (Dashboard Mobile-First)

> **Status:** 🔄 Implementasi inti selesai sebagian (2026-11-03). Portal, pengajuan bukti, approval admin, profil, dan akademik dasar sudah tersedia; hardening lanjutan masih tersisa.  
> **Target:** Menyediakan portal khusus akun ber-role `siswa`, terpisah sepenuhnya dari dashboard dan layout admin/guru.

## 1. Tujuan dan ruang lingkup

Portal siswa adalah area baca mandiri untuk informasi personal siswa serta pengajuan pembayaran. Halaman ini **bukan** variasi dari dashboard admin: ia memakai route, Livewire component, navigasi, dan layout mobile-first tersendiri.

Fitur tahap awal:

1. Dashboard siswa: ringkasan tagihan, status pengajuan pembayaran, kelas aktif, jadwal hari ini, dan pengumuman/agenda terdekat.
2. Pembayaran SPP dan biaya sekolah: lihat invoice, unggah bukti transfer, pantau status, lalu admin menyetujui atau menolak.
3. Informasi siswa: profil, NIS/NISN, kontak, jurusan, dan data kelas aktif.
4. Informasi akademik: kelas/rombel aktif, wali kelas, mata pelajaran serta jadwal pada tahun ajaran aktif.
5. Tambahan yang diperlukan: riwayat absensi pribadi, kalender akademik, dan notifikasi status pengajuan pembayaran.

Di luar tahap awal: pembayaran gateway otomatis, chat antar pengguna, perubahan data akademik oleh siswa, dan pengeditan nilai/rapor.

## 2. Aturan produk dan otorisasi

### 2.1 Role dan route

- Role Spatie yang digunakan adalah `siswa`.
- Akses portal hanya untuk user yang memiliki role `siswa`, `is_active = true`, serta memiliki relasi `user->siswa` yang aktif.
- Jika role siswa tidak memiliki profil `Siswa`, sistem menampilkan halaman pemulihan yang aman (bukan error 500) dan meminta siswa menghubungi administrasi.
- Route siswa berada pada prefix `/siswa` dan middleware `auth`, `role:siswa`, serta middleware baru `ensure.student.profile`.
- Rute awal yang direncanakan:

  | URL | Nama route | Fungsi |
  | --- | --- | --- |
  | `/siswa/dashboard` | `siswa.dashboard` | Ringkasan personal siswa. |
  | `/siswa/pembayaran` | `siswa.payments` | Daftar tagihan dan pengajuan bukti bayar. |
  | `/siswa/akademik` | `siswa.academic` | Kelas, wali kelas, mapel, jadwal, absensi. |
  | `/siswa/profil` | `siswa.profile` | Biodata yang dapat dilihat dan data kontak terbatas yang dapat diperbarui. |

- Login diarahkan menurut role: admin/guru ke dashboard mereka, siswa ke `siswa.dashboard`.
- Siswa tidak boleh mengakses route atau layout admin. Sebaliknya, komponen siswa tidak boleh menerima `siswa_id` dari query string untuk menentukan data; semua data selalu diperoleh dari `auth()->user()->siswa`.

### 2.2 Sumber kebenaran akademik

- Kelas aktif siswa pada tahun ajaran aktif harus dibaca dari `anggota_rombels -> rombels`, dengan `status = Aktif` dan `tanggal_keluar IS NULL`.
- Jangan menjadikan `siswas.kelas_id` sebagai sumber kebenaran. Kolom tersebut hanya pointer/cache kondisi aktif.
- Gunakan scope model yang telah tersedia, khususnya `Siswa::aktifDiKelasPadaTahunAjaran()` dan `Siswa::inKelasPadaTanggal()`.
- Mapel dan jadwal diambil dari `jadwals` pada `kelas_id` rombel aktif dan `tahun_ajaran_id` aktif, dengan eager loading `mapel`, `guru.user`, serta `ruangan`.

### 2.3 Aturan keuangan dan bukti pembayaran

- Sumber keuangan tetap `tagihans` (invoice) dan `pembayarans` (ledger). Tabel legacy `pembayaran_spps` tidak digunakan untuk fitur baru.
- Siswa tidak boleh membuat record `Pembayaran` secara langsung. Unggahan siswa menjadi **pengajuan pembayaran**, bukan transaksi final.
- Hanya admin yang boleh menyetujui. Saat persetujuan, sistem menjalankan `Tagihan::bayar(...)` dalam transaksi database agar status invoice diperbarui lewat `refreshStatus()`.
- Nominal pengajuan wajib lebih dari 0 dan tidak boleh melebihi `sisa_tagihan` pada saat admin menyetujui. Nilai harus dihitung ulang ketika approval, bukan hanya saat siswa mengunggah.
- Bukti transfer tidak dapat menggantikan bukti yang sedang berstatus `Menunggu Verifikasi`. Siswa dapat membatalkan pengajuan pending atau mengirim ulang setelah `Ditolak`.
- Siswa hanya boleh melihat tagihan, pembayaran, dan bukti miliknya sendiri.

## 3. Desain data

Tambahkan tabel baru `payment_submissions` agar ledger keuangan hanya berisi transaksi yang telah terverifikasi.

| Kolom | Aturan |
| --- | --- |
| `id` | Primary key. |
| `tagihan_id` | FK ke `tagihans`, `restrictOnDelete()` agar bukti tidak kehilangan invoice. |
| `siswa_id` | FK ke `siswas`, harus sama dengan pemilik tagihan. |
| `submitted_by` | FK ke `users`; akun siswa yang mengirim. |
| `nominal` | Decimal(12,2), > 0. |
| `tanggal_transfer` | Tanggal yang diklaim siswa. |
| `metode` | Contoh: Transfer Bank, QRIS; tidak boleh `Tunai` untuk unggah bukti. |
| `bukti_path` | Path berkas private, bukan URL publik. |
| `status` | `Menunggu Verifikasi`, `Disetujui`, `Ditolak`, `Dibatalkan`. |
| `catatan_siswa` | Opsional, dibatasi panjang. |
| `catatan_admin` | Alasan penolakan/catatan verifikasi. |
| `reviewed_by`, `reviewed_at` | Audit admin yang mengambil keputusan. |
| `pembayaran_id` | Nullable + unique, diisi hanya setelah approval berhasil mencatat ledger. |
| timestamps | Audit waktu kirim/perubahan. |

Index yang diperlukan:

- `(siswa_id, status, created_at)` untuk daftar siswa;
- `(tagihan_id, status)` untuk review invoice;
- `reviewed_by` dan `pembayaran_id` untuk audit.

Tidak perlu menyimpan kolom boolean approval terpisah: `status = Disetujui` adalah kebenaran kanonis. Bila UI membutuhkan boolean, gunakan accessor `is_approved` yang bernilai `true` hanya ketika status tersebut.

## 4. Alur pembayaran

```text
Siswa melihat Tagihan
  -> memilih tagihan yang belum lunas
  -> mengisi nominal, tanggal transfer, metode, bukti, catatan
  -> PaymentSubmission: Menunggu Verifikasi
  -> Admin meninjau bukti
     -> Tolak: status Ditolak + catatan_admin, ledger tidak berubah
     -> Setujui (DB transaction + row lock):
        validasi kembali pemilik, status, dan sisa tagihan
        Tagihan::bayar(...)
        hubungkan pembayaran_id
        status Disetujui
        status invoice diperbarui otomatis
```

Pada approval gunakan `lockForUpdate()` pada submission dan tagihan agar dua admin tidak dapat menyetujui bukti yang sama atau menyebabkan overpayment. Approval harus idempotent: submission yang sudah memiliki `pembayaran_id` tidak boleh diproses ulang.

## 5. UI/UX dashboard siswa

### Prinsip desain

- Buat `layouts.siswa`, bukan `layouts.admin`; tidak ada sidebar admin, statistik kunjungan, PPDB admin, maupun menu manajemen.
- Mobile-first (lebar 360 px ke atas), area sentuh minimal 44 px, teks utama minimal 14 px, dan bottom navigation yang aman terhadap safe-area perangkat.
- Header ringkas: foto/inisial siswa, sapaan, tahun ajaran aktif, tombol notifikasi.
- Navigasi bawah maksimal lima item: Beranda, Pembayaran, Akademik, Profil, Bantuan.
- Desktop boleh menampilkan sidebar/ringkasan samping khusus siswa, tetapi struktur informasi dan action tetap sama dengan mobile.
- Gunakan label status yang jelas dan tidak hanya mengandalkan warna: `Menunggu Verifikasi`, `Disetujui`, `Ditolak`, `Lunas`, dan `Jatuh Tempo`.

### Isi halaman

| Halaman | Konten utama |
| --- | --- |
| Dashboard | Ringkasan total tagihan tersisa, tagihan terdekat, kartu status pengajuan terakhir, kelas/rombel dan wali kelas, jadwal hari ini, agenda akademik. |
| Pembayaran | Filter tagihan, detail nominal/potongan/terbayar/sisa/jatuh tempo, modal/form upload bukti, riwayat pengajuan dan riwayat pembayaran tervalidasi. |
| Akademik | Tahun ajaran aktif, kelas dan jurusan, wali kelas, daftar mapel dengan guru, jadwal mingguan, ringkasan absensi pribadi. |
| Profil | Biodata read-only (NIS/NISN, kelas, jurusan), kontak yang diizinkan diperbarui, tautan ubah kata sandi. |
| Bantuan | Kontak tata usaha dan FAQ pembayaran; AI bantuan yang sudah ada dapat ditampilkan hanya jika menjaga batas data pribadi. |

## 6. Implementasi bertahap

### Fase 1 — Fondasi role dan layout

- [x] Audit/konfirmasi role `siswa` pada seeder dan akun siswa.
- [x] Tambahkan middleware validasi profil siswa serta redirect login berbasis role.
- [x] Tambahkan `layouts.siswa`, header, dan bottom navigation mobile khusus portal siswa.
- [x] Buat route dan komponen `StudentDashboard` terpisah dari `App\Livewire\Dashboard`.
- [ ] Tambahkan policy/helper sentral untuk memastikan resource dimiliki siswa yang login.

### Fase 2 — Pembayaran dan verifikasi admin

- [x] Buat migrasi/model `PaymentSubmission`, relasi di `Tagihan`, `Siswa`, `User`, dan `Pembayaran`.
- [x] Buat penyimpanan bukti private dan controller pratinjau berotorisasi; validasi PDF/JPG/PNG/WebP maksimal 5 MB serta nama file acak.
- [x] Buat halaman siswa untuk melihat invoice dan mengirim/batal pengajuan.
- [x] Buat antrean review admin di modul Keuangan, termasuk pratinjau aman, setujui/tolak, catatan admin, dan audit reviewer.
- [x] Implementasikan approval atomik menggunakan `DB::transaction()`, `lockForUpdate()`, dan `Tagihan::bayar()`; jangan insert langsung ke `pembayarans`.
- [x] Tampilkan notifikasi in-app ketika pengajuan dikirim, disetujui, atau ditolak.

### Fase 3 — Informasi akademik dan profil

- [x] Buat query kelas aktif melalui `AnggotaRombel/Rombel` pada tahun ajaran aktif.
- [x] Tambahkan kartu kelas, wali kelas, mapel, jadwal, dan ringkasan absensi siswa dengan eager loading.
- [x] Buat profil siswa dan perubahan kontak terbatas, dengan validasi.
- [x] Tambahkan kalender akademik pada dashboard siswa.

### Fase 4 — Hardening dan kualitas

- [x] Pastikan query tagihan/pengajuan dan akses bukti memiliki ownership guard di backend, bukan hanya filter UI.
- [ ] Rate-limit unggah bukti dan aksi approval; log keputusan admin.
- [ ] Tambahkan empty state, error state, skeleton loading, serta aksesibilitas keyboard/screen reader.
- [ ] Uji responsivitas pada 360, 390, 768, dan desktop; uji dark/light mode bila tema tetap didukung.
- [ ] Tambahkan retensi berkas bukti dan kebijakan penghapusan yang tidak merusak audit transaksi.

## 7. Rencana pengujian wajib

1. Siswa hanya dapat membaca resource miliknya dan mendapat 403/404 aman untuk ID siswa/tagihan lain.
2. Akun tanpa profil siswa tidak dapat membuka portal normal.
3. Upload bukti valid membuat submission `Menunggu Verifikasi`, tetapi tidak menambah `Pembayaran` maupun mengubah `Tagihan`.
4. Penolakan tidak mengubah ledger/invoice dan menyimpan alasan admin.
5. Approval membuat satu `Pembayaran`, menghubungkan `pembayaran_id`, serta memperbarui status tagihan secara tepat.
6. Approval paralel/berulang tidak dapat menciptakan pembayaran ganda atau overpayment.
7. Data kelas aktif berasal dari `anggota_rombels`, bukan `siswas.kelas_id`.
8. Render daftar tagihan menggunakan `withSum('pembayarans', 'nominal')` untuk menghindari N+1.
9. Screenshot/regression test memastikan dashboard siswa pada mobile tidak memuat sidebar atau data admin.

## 8. Keputusan yang perlu dikonfirmasi sebelum implementasi

1. Metode pembayaran yang diterima (transfer bank saja, QRIS, atau keduanya) dan rekening tujuan yang ditampilkan.
2. Batas ukuran berkas serta format final bukti pembayaran (rekomendasi: JPG/PNG/WebP/PDF, maksimum 5 MB).
3. Apakah siswa boleh membayar sebagian atau setiap bukti harus melunasi satu tagihan. Rencana ini mendukung cicilan karena ledger saat ini mendukungnya.
4. Kanal notifikasi tahap pertama: in-app saja atau juga email/WhatsApp.
5. Apakah nilai/rapor read-only akan dimasukkan dalam fase berikutnya setelah dashboard akademik dasar stabil.
