# Frontend Website Sistem Sekolah (`school-frontend`)

Antarmuka website institusi pendidikan modern (**Modern Educational Institution — Premium, Clean & Professional**) yang dibangun menggunakan **HTML5 Semantic**, **Tailwind CSS v4**, dan **Vanilla JavaScript (ES6+)**. Dirancang khusus agar dapat dibuka langsung sebagai pratinjau HTML statis sekaligus siap diintegrasikan ke dalam arsitektur **Laravel 13 & Livewire 4** (referensi: `sawal123/sistemsekolah`).

## 1. Cara Menjalankan Preview Statis

Seluruh halaman dirancang mandiri (*standalone static HTML*) tanpa membutuhkan proses kompilasi atau backend server untuk pratinjau awal:

1. Ekstrak folder `school-frontend/`.
2. Buka file `index.html` langsung di browser modern (Chrome, Edge, Firefox, Safari), atau jalankan server statis lokal:
   ```bash
   cd school-frontend
   python3 -m http.server 8080
   ```
3. Akses `http://localhost:8080` pada browser Anda.

## 2. Daftar Halaman (15 Halaman Lengkap)

| No | File HTML | Nama Halaman | Fitur Interaktif Utama |
| :--- | :--- | :--- | :--- |
| 01 | `index.html` | **Beranda** | Hero slider otomatis/manual, kartu statistik, sambutan kepsek, program akademik, prestasi, fasilitas, berita, galeri lightbox, CTA PPDB |
| 02 | `tentang.html` | **Profil Sekolah** | Sejarah & fase tumbuh, tabel identitas sekolah, sambutan lengkap, visi/misi/tujuan, nilai budaya, pratinjau struktur organisasi |
| 03 | `akademik.html` | **Akademik** | Navigasi tab interaktif (Kurikulum, Program IPA, Program IPS, Program Bahasa), tabel kalender akademik, CTA konsultasi |
| 04 | `kepegawaian.html` | **Guru & Kepegawaian** | 3 Tab (Direktori Guru, Tata Usaha, Struktur Organisasi), live search nama guru & filter mata pelajaran |
| 05 | `fasilitas.html` | **Fasilitas Sekolah** | Filter kategori fasilitas (8 fasilitas utama: Kelas, Lab Sains, Perpustakaan, Komputer, Olahraga, UKS, Kantin, Aula), lightbox preview |
| 06a | `blog.html` | **Berita & Artikel** | Featured article, filter kategori, pencarian artikel real-time, kartu responsif, paginasi, sidebar artikel populer |
| 06b | `blog-detail.html` | **Detail Artikel** | Breadcrumb, metadata penulis, tipografi editorial nyaman dibaca, tombol salin tautan/share dengan toast, artikel terkait |
| 07 | `ppdb.html` | **PPDB Online** | Info gelombang, jadwal, syarat dokumen, jalur masuk, tabel simulasi biaya, alur 4 langkah, **Form PPDB Demo** (validasi visual, auto-fill demo, loading & success state), FAQ accordion |
| 08 | `galeri.html` | **Galeri Sekolah** | Grid dokumentasi, filter kategori (Kegiatan, Fasilitas, Prestasi), pencarian judul, modal lightbox interaktif |
| 09 | `prestasi.html` | **Prestasi Sekolah** | Sorotan prestasi nasional, filter kombinasi Tahun (2024–2026) & Kategori, pencarian lomba |
| 10 | `pengumuman.html` | **Pengumuman** | Pinned announcement, status badge, filter kategori & pencarian, modal pratinjau lampiran PDF |
| 11 | `agenda.html` | **Agenda Sekolah** | Filter bulan kegiatan, pencarian agenda, kartu agenda dengan tanggal, waktu, lokasi, dan status |
| 12 | `alumni.html` | **Alumni** | Statistik jejaring alumni, kisah inspiratif lintas rumpun, program kolaborasi IKA & dokumentasi reuni |
| 13 | `kontak.html` | **Kontak** | Kartu alamat/telepon/email/jam kerja, form kontak dengan validasi demo, placeholder peta lokasi, FAQ layanan tamu |
| 14 | `login.html` | **Login Portal** | Split layout desktop modern, show/hide password, tombol isi akun demo, loading & success state |

## 3. Struktur File & Direktori

```text
school-frontend/
├── index.html
├── tentang.html
├── akademik.html
├── kepegawaian.html
├── fasilitas.html
├── blog.html
├── blog-detail.html
├── ppdb.html
├── galeri.html
├── prestasi.html
├── pengumuman.html
├── agenda.html
├── alumni.html
├── kontak.html
├── login.html
├── assets/
│   ├── css/
│   │   └── custom.css
│   ├── js/
│   │   └── main.js
│   └── images/
│       ├── logo-sekolah.svg
│       ├── hero-campus.jpg
│       ├── hero-lab.jpg
│       ├── facility-library.jpg
│       ├── facility-computer.jpg
│       ├── facility-sports.jpg
│       ├── achievement-olympiad.jpg
│       ├── activity-arts.jpg
│       ├── principal.jpg
│       └── avatar-1.svg ... avatar-8.svg
└── README.md
```

## 4. Dependensi Frontend

- **HTML5 Semantic** (tanpa ketergantungan framework SPA).
- **Tailwind CSS v4** (`@tailwindcss/browser@4` untuk pratinjau statis; siap dikompilasi via `@tailwindcss/vite` pada Laravel).
- **Google Fonts**: `Plus Jakarta Sans` (Heading) & `Inter` (Body).
- **Ikon**: Inline SVG modular (setara Lucide Icons, bebas beban bundel eksternal).
- **JavaScript**: `assets/js/main.js` murni Vanilla ES6+ tanpa jQuery.

## 5. Panduan Integrasi ke Laravel 13 & Livewire 4 Blade

Seluruh bagian halaman telah diberi penanda komentar `<!-- COMPONENT: NamaKomponen -->` agar mudah dipisahkan menjadi komponen Blade dan halaman Livewire:

### Pemetaan Komponen (`resources/views/components/landing/`)
- `<!-- COMPONENT: TopInfoBar -->` & `<!-- COMPONENT: Navbar -->` → `resources/views/components/landing/navbar.blade.php`
- `<!-- COMPONENT: PageHero -->` → `resources/views/components/landing/page-hero.blade.php`
- `<!-- COMPONENT: SectionHeading -->` → `resources/views/components/landing/section-heading.blade.php`
- `<!-- COMPONENT: NewsCard -->` → `resources/views/components/landing/news-card.blade.php`
- `<!-- COMPONENT: FacilityCard -->` → `resources/views/components/landing/facility-card.blade.php`
- `<!-- COMPONENT: LightboxModal -->` → `resources/views/components/landing/lightbox-modal.blade.php`
- `<!-- COMPONENT: Footer -->` → `resources/views/components/landing/footer.blade.php`

### Pemetaan Halaman Livewire (`resources/views/livewire/landing/`)
1. Buat layout utama `resources/views/layouts/landing.blade.php` yang memuat `<head>`, `<x-landing.navbar />`, `{{ $slot }}`, dan `<x-landing.footer />`.
2. Pindahkan isi `<main>...</main>` dari setiap file `.html` ke komponen Livewire terkait:
   - `index.html` → `livewire/landing/home-page.blade.php`
   - `tentang.html` → `livewire/landing/about-page.blade.php`
   - `akademik.html` → `livewire/landing/academic-page.blade.php`
   - `kepegawaian.html` → `livewire/landing/staffing-page.blade.php`
   - `fasilitas.html` → `livewire/landing/facilities-page.blade.php`
   - `blog.html` → `livewire/landing/blog-page.blade.php`
   - `blog-detail.html` → `livewire/landing/blog-detail-page.blade.php`
   - `ppdb.html` → `livewire/landing/ppdb-page.blade.php` (ganti atribut `name` pada input dengan `wire:model.blur`)
   - `kontak.html` → `livewire/landing/contact-page.blade.php`
3. Salin `assets/css/custom.css` ke `resources/css/app.css` (di bawah `@import "tailwindcss";`) dan `assets/js/main.js` ke `resources/js/app.js`.
