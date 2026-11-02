# Audit menu landing — 2026-11-02

## Hasil perbaikan

- Navbar dan menu seluler memakai daftar tujuan yang sama. Tombol Info PPDB dan Login Portal tidak membungkus teks; menu desktop beralih ke drawer pada lebar di bawah 1440 px.
- Info PPDB menuju `/ppdb`, tombol daftar menuju `/ppdb/daftar#daftar`, dan persyaratan menuju `/ppdb/syarat#syarat`.
- Banner PPDB membaca gelombang berstatus `Dibuka`, dengan urutan pemilihan yang sama seperti formulir PPDB. Tidak lagi mengklaim Gelombang I dibuka ketika belum ada gelombang aktif.
- Identitas navbar, kontak footer, dan judul situs memakai pengaturan sekolah.
- Tautan sambutan beranda mempunyai target `#sambutan` pada halaman tentang.

## Halaman publik tambahan

| URL | Sumber konten |
| --- | --- |
| `/pengumuman` | Artikel `Published` dalam kategori pengumuman |
| `/prestasi` | Artikel `Published` dalam kategori prestasi |
| `/kalender-akademik` | Kalender akademik mulai bulan berjalan, termasuk kegiatan dan hari libur |
| `/galeri` | Data galeri, dengan pagination dan pratinjau foto |
| `/alumni` | Jejak alumni `Verified` dan `is_public = true` |
| `/kebijakan-privasi` | Penjelasan penggunaan data layanan website |
| `/standar-pelayanan` | Panduan layanan informasi, PPDB, dan administrasi |
| `/peta-situs` | Direktori halaman publik, program akademik, serta bagian PPDB |

Pengumuman dan prestasi memanfaatkan modul Blog & Artikel yang sudah tersedia. Gunakan slug kategori `pengumuman` / `prestasi`, turunannya seperti `prestasi-akademik`, atau nama kategori yang memuat kata tersebut. Draf tidak ditampilkan. Halaman tanpa konten menampilkan keterangan kosong, bukan data contoh tambahan.

## Verifikasi

`tests/Feature/LandingNavigationTest.php` memeriksa tujuan menu desktop/seluler/footer, tujuan dan fragmen tautan beranda, banner PPDB, publikasi artikel, kalender, halaman informasi, serta pembatasan publikasi alumni. Pengujian PPDB yang sudah ada tetap berada di `tests/Feature/LandingPagesTest.php`.
