# Modal pengunjung — 2026-11-02

## Penggunaan admin

Buka **Pengaturan Website → Modal Pengunjung** (`/website/modal-pengunjung`). Admin dapat menambah, mengubah, mengaktifkan/nonaktifkan, dan menghapus modal.

- Judul dipakai sebagai label serta teks alternatif gambar.
- Gambar wajib JPG, PNG, atau WebP maksimal 4 MB. Disimpan pada disk publik di `website/popups`.
- Link wajib alamat lengkap HTTP/HTTPS; gambar dan tombol informasi menuju alamat tersebut.
- Tentukan **Tampilkan kembali setelah** dalam jam (1â€“8.760 jam). Setiap modal dapat memiliki durasi sendiri.
- Daftar menampilkan jumlah pengunjung yang telah mendapat izin penayangan.
- Riwayat penayangan berlaku sesuai durasi pada modal. Sesudah itu sistem menghapusnya otomatis sehingga modal dapat tampil lagi untuk pengunjung yang sama.

## Data dan alur penayangan

`website_popups`: `judul`, `gambar`, `link`, `display_interval_hours`, `is_active`, timestamps.

`website_popup_impressions`: `website_popup_id`, `visitor_key`, `ip_hash`, `device_hash`, `displayed_at`. Unique constraint `(website_popup_id, visitor_key)` mencegah pencatatan ganda, termasuk permintaan dari beberapa tab. Saat halaman landing atau endpoint penayangan diakses, riwayat yang telah melewati `display_interval_hours` modalnya otomatis dihapus; scheduler juga menjalankan pembersihan tiap jam. Pengunjung tersebut kemudian kembali berhak melihat modal. Penghapusan modal juga menghapus riwayatnya melalui foreign key cascade.

Pada layout landing, `VisitorPopupService` memilih satu modal aktif terbaru yang belum dilihat. Membuka halaman saja tidak mencatat penayangan. JavaScript menunggu gambar selesai dimuat dan tab terlihat, lalu POST ke `/informasi-modal/{popup}/tampil` dengan CSRF. Hanya permintaan yang pertama membuat riwayat mendapat `show: true`. Dialog dapat ditutup dengan tombol, Escape, atau klik di luar. Modal tidak dipasang pada halaman login maupun dashboard.

Jika ada beberapa modal aktif, satu modal baru dapat muncul pada kunjungan halaman berikutnya. Tidak ada rentetan modal saat pengguna menutup dialog. Gambar gagal dimuat tidak dicatat sebagai tayang. Endpoint dilindungi CSRF dan pembatasan 30 permintaan/menit; status nonaktif ditolak di server.

## Identitas pengunjung

Penanda adalah kombinasi IP dari Laravel Request dan User-Agent (identitas browser/sistem operasi), bukan identitas akun atau nomor perangkat keras. Keduanya di-hash dengan HMAC SHA-256 dan APP_KEY; data mentah tidak ditambahkan ke tabel penayangan. Tidak bergantung pada session/localStorage, sehingga refresh atau session baru dengan IP dan User-Agent sama tetap tidak mengulang modal selama jendela waktu yang dipilih admin.

Perubahan IP atau User-Agent dapat dianggap pengunjung baru. Dua orang dengan IP dan User-Agent persis sama akan berbagi penanda. Rotasi APP_KEY juga mengubah hash pengunjung. Ini batas identifikasi berbasis IP/browser, bukan identifikasi fisik perangkat.

## Implementasi dan pengujian

- Migrasi: `database/migrations/2026_11_02_000001_create_website_popups_tables.php` (MySQL/MariaDB dan SQLite).
- Admin: `WebsitePopupIndex`, route dibatasi role admin; metode mutasi juga memeriksa role.
- Backend: `tests/Feature/WebsitePopupTest.php`.
- Interaksi browser: `node --test tests/Frontend/visitor-popup.test.mjs` (izin server, gambar gagal, tab tersembunyi, kegagalan jaringan, dan pemulihan history).

Hasil verifikasi: 23 pengujian backend terkait modal dan landing lulus (247 assertion), 5 pengujian JavaScript lulus, dan `npm run build` berhasil. Pratinjau browser memakai database SQLite terpisah: modal tampil dengan gambar/tautan, ditutup lewat Escape, lalu tidak muncul lagi setelah refresh; tabel riwayat tetap satu baris. Migrasi fitur sudah dijalankan pada database lokal. Pratinjau sementara sudah dihentikan dan file ujinya dibersihkan.
