# Rencana Implementasi Bantuan Chat Bot AI

## Status implementasi

**Fase 1 / MVP read-only telah diimplementasikan pada 2026-11-02.** Tersedia halaman `/bantuan-ai` untuk admin dan guru, penyimpanan percakapan per pengguna, backend OpenAI Responses API, feature flag, pembatasan laju, penghapusan percakapan, dan retensi harian. Panggilan API hanya dilakukan oleh server.

Fase 2 (konten FAQ yang dikelola admin) dan Fase 3 (tool data personal read-only) belum diaktifkan karena perlu keputusan sumber konten dan cakupan data yang boleh diakses bot.

## 1. Tujuan dan batas awal

Membangun fitur **Bantuan Chat Bot AI** di portal sekolah untuk menjawab pertanyaan penggunaan sistem, proses akademik, PPDB, dan informasi sekolah. Fitur pertama direkomendasikan tersedia bagi pengguna yang sudah login (admin dan guru), bersifat **read-only**, dan tidak menjalankan perubahan data apa pun.

Kunci API sudah tersedia melalui `OPENAI_API_KEY`. Konfigurasi proyek juga sudah memiliki `services.openai.api_key` dan `services.openai.model` dengan nilai model dari `OPENAI_MODEL` (fallback saat ini: `gpt-4o-mini`). Kunci tidak boleh dikirim ke browser, dicetak di log, atau dimasukkan ke database.

OpenAI merekomendasikan [Responses API](https://developers.openai.com/api/docs/guides/text) untuk aplikasi generasi teks baru. Rencana ini menggunakan API tersebut melalui backend Laravel, memakai `instructions` untuk aturan bot dan mengambil teks hasil secara aman dari respons, bukan mengasumsikan indeks `output` tertentu.

## 2. Keputusan produk yang perlu disepakati sebelum implementasi

1. **Audiens awal:** rekomendasi: hanya pengguna portal yang login. Chatbot publik landing page dapat menjadi fase berikutnya karena membutuhkan aturan privasi dan anti-spam tambahan.
2. **Cakupan jawaban:** bantuan penggunaan menu, SOP sekolah, jadwal/akademik yang diizinkan, informasi PPDB, dan FAQ. Bot tidak menjadi pengganti keputusan administratif.
3. **Sumber pengetahuan:** konten bantuan yang dikelola admin, dokumentasi modul, dan FAQ sekolah. Data transaksi/akademik pribadi tidak dijadikan konteks bebas.
4. **Tindakan:** fase awal hanya menjawab dan memberikan tautan menu. Aksi seperti mengubah nilai, absensi, pembayaran, atau akun tidak boleh dilakukan oleh bot.
5. **Retensi percakapan:** rekomendasi awal 90 hari, dengan tombol hapus percakapan untuk pengguna dan job pembersihan terjadwal.

## 3. Arsitektur yang direkomendasikan

```text
Livewire chat widget/page
        |
        v
AiChatService (otorisasi, batas konteks, prompt, audit)
        |
        +--> Basis pengetahuan internal / FAQ yang diizinkan
        |
        v
Laravel HTTP client -> OpenAI Responses API
        |
        v
ai_chat_conversations + ai_chat_messages + log operasional
```

### Komponen yang akan ditambahkan

- `app/Services/AiChatService.php`: satu pintu pemanggilan OpenAI, perakitan konteks, timeout, penanganan error, dan redaksi data sensitif.
- `app/Livewire/Assistant/HelpChat.php` dan view terkait: panel chat yang dapat dibuka dari header/sidebar, daftar percakapan, input pesan, status memuat, dan tautan rujukan.
- `app/Models/AiChatConversation.php`, `AiChatMessage.php`, dan opsional `AiChatFeedback.php`.
- Migrasi database untuk percakapan, pesan, umpan balik, dan indeks retensi.
- `config/ai-chat.php`: model, batas pesan, jumlah riwayat konteks, timeout, dan status feature flag. Nilai API key tetap berasal dari `config/services.php`.
- Basis pengetahuan awal, misalnya `ai_knowledge_articles`, atau adaptor yang membaca FAQ/dokumen yang secara eksplisit disetujui admin.
- Route/halaman khusus bantuan di bawah middleware `auth`; tombol widget hanya dirender pada layout portal, bukan melalui JavaScript yang memegang API key.

## 4. Desain data

### `ai_chat_conversations`

| Kolom | Keterangan |
| --- | --- |
| `id` | Primary key. |
| `user_id` | Pemilik percakapan, `restrictOnDelete()` atau retensi terkelola sesuai kebijakan akun. |
| `title` | Ringkasan pendek percakapan. |
| `last_message_at` | Urutan dan daftar percakapan pengguna. |
| `deleted_at` | Soft delete untuk penghapusan oleh pengguna. |
| timestamps | Audit dasar. |

### `ai_chat_messages`

| Kolom | Keterangan |
| --- | --- |
| `id`, `conversation_id` | Relasi percakapan; indeks komposit `(conversation_id, created_at)`. |
| `role` | `user`, `assistant`, atau `system`. |
| `content` | Pesan yang telah dibatasi panjang dan disanitasi untuk tampilan. |
| `openai_response_id` | ID respons untuk penelusuran kesalahan; bukan API key. |
| `model`, `prompt_version` | Reproduksibilitas jawaban. |
| `metadata` | Latensi, penggunaan token bila tersedia, status error tanpa menyimpan rahasia. |
| timestamps | Audit dan retensi. |

### `ai_knowledge_articles` (fase FAQ)

Konten terkurasi oleh admin: `title`, `slug`, `content`, `audience_role`, `is_active`, timestamps. Hanya artikel yang sesuai role pengguna dapat dijadikan konteks.

## 5. Alur satu pesan

1. Livewire memvalidasi panjang pesan, memastikan pengguna telah login, dan menerapkan pembatasan laju per user/IP.
2. Sistem mengambil percakapan milik pengguna saja, lalu memuat sejumlah kecil pesan terbaru (misalnya 10) dan ringkasan konteks bila sudah tersedia.
3. `AiChatService` memilih artikel FAQ yang relevan dengan query sederhana terlebih dahulu. Fase awal tidak perlu mengirim seluruh database atau seluruh isi dokumen ke model.
4. Service menyusun `instructions`: bahasa Indonesia, batas peran, larangan membuat data, cara memberi tautan menu, dan respons aman saat data tidak tersedia.
5. Backend memanggil `POST /v1/responses` dengan model dari konfigurasi. Hasil disimpan sebagai pesan asisten hanya setelah respons valid diterima.
6. UI menampilkan jawaban, sumber/tautan internal yang relevan, dan aksi umpan balik “Membantu / Tidak membantu”.
7. Jika API timeout, rate limit, atau gagal, pengguna menerima pesan ramah tanpa detail rahasia; error teknis dicatat di log server.

## 6. Aturan konteks dan otorisasi

- Bot mengetahui role pengguna dari server; role tidak boleh diklaim dari teks pengguna.
- Jawaban untuk guru hanya boleh mengarahkan ke data kelas/mapel yang memang dapat diakses guru tersebut. Aturan otorisasi Civitas/KBM yang sudah ada tetap menjadi sumber kebenaran.
- Jangan mengirim kata sandi, API key, token sesi, NIK, dokumen PPDB, koordinat rumah, detail pembayaran, atau daftar siswa lengkap ke model.
- Untuk pertanyaan data pribadi, bot hanya mengarahkan pengguna ke menu/prosedur resmi atau meminta admin yang berwenang.
- Input pengguna diperlakukan sebagai data tidak tepercaya. Instruksi dalam pesan seperti “abaikan aturan” tidak boleh mengubah prompt kebijakan aplikasi.
- Batasi panjang pesan, jumlah riwayat, dan ukuran artikel konteks agar biaya serta risiko kebocoran data terkendali.

## 7. Prompt dan kualitas jawaban

Versi prompt disimpan di kode (`AiChatPromptBuilder`) dan diberi `prompt_version`; jangan menyimpan prompt produksi hanya di dashboard eksternal. Pendekatan ini selaras dengan panduan OpenAI untuk menggunakan `instructions`, menguji perubahan prompt, dan memantau perilaku model. [Panduan Text Generation OpenAI](https://developers.openai.com/api/docs/guides/text)

Isi minimum instruksi sistem:

- Anda adalah asisten bantuan Sistem Informasi Sekolah.
- Jawab singkat, jelas, dalam Bahasa Indonesia.
- Bedakan informasi pasti, panduan langkah, dan hal yang perlu dikonfirmasi ke admin.
- Jangan mengarang status sekolah, data siswa, jadwal, atau keputusan keuangan.
- Jangan melakukan aksi; berikan langkah navigasi/menu yang relevan.
- Bila tidak ada sumber yang memadai, nyatakan keterbatasan dan arahkan ke petugas/admin.

Fase lanjutan dapat memakai Structured Outputs untuk respons berbentuk `{answer, suggested_links, confidence, escalation}`. Referensi API mendukung format `json_schema` dan merekomendasikannya dibanding JSON mode lama. [Referensi Responses API](https://developers.openai.com/api/reference/resources/responses/methods/create)

## 8. Keamanan, reliabilitas, dan biaya

- Semua panggilan OpenAI dibuat server-side memakai `config('services.openai.api_key')`.
- Tambahkan `AI_CHAT_ENABLED=false` sebagai kill switch dan `AI_CHAT_MODEL`/`OPENAI_MODEL` sebagai konfigurasi model tanpa mengubah kode.
- Terapkan `RateLimiter`, misalnya 10 pesan per 10 menit per user dan batas tambahan per IP.
- Tetapkan timeout HTTP, tetapi hindari retry buta setelah request berpotensi diterima provider agar pesan/jawaban tidak tercatat ganda.
- Gunakan idempotency key internal pada pesan pengguna dan transaksi database saat menyimpan pasangan pesan user/asisten.
- Catat model, latency, response ID, token/usage bila tersedia, dan kategori error. Jangan catat header otorisasi atau API key.
- Jalankan command retensi harian untuk menghapus soft-deleted dan percakapan kedaluwarsa.
- Tampilkan pemberitahuan singkat bahwa jawaban AI dapat keliru dan tidak menggantikan kebijakan sekolah.

## 9. Tahap implementasi

### Fase 1 — Fondasi dan MVP read-only

1. Tambahkan konfigurasi `ai-chat.php`, feature flag, dan validasi bahwa `OPENAI_API_KEY` tersedia tanpa mengekspos nilainya.
2. Buat migrasi/model percakapan dan pesan beserta kebijakan kepemilikan data.
3. Buat `AiChatService` berbasis Laravel HTTP client serta unit test menggunakan `Http::fake()`.
4. Buat `HelpChat` Livewire dengan percakapan baru, kirim pesan, riwayat, loading state, error state, dan hapus percakapan.
5. Tambahkan tombol Bantuan AI pada portal pengguna yang telah login.
6. Tambahkan rate limiting, audit log aman, dan batas konteks.

**Kriteria selesai:** admin/guru dapat mengirim pertanyaan bantuan, melihat jawaban AI, riwayat hanya miliknya sendiri, dan API key tidak muncul di HTML/log UI.

### Fase 2 — Basis pengetahuan terkurasi

1. Tambahkan CRUD artikel bantuan untuk admin serta segmentasi `audience_role`.
2. Tambahkan pencarian artikel relevan dan sertakan kutipan/tautan internal pada jawaban.
3. Tambahkan feedback jawaban dan dashboard sederhana: jumlah pesan, kegagalan, rating, dan topik tanpa jawaban.

**Kriteria selesai:** jawaban merujuk SOP/FAQ sekolah yang dapat dikelola admin dan dapat dievaluasi kualitasnya.

### Fase 3 — Konteks data terbatas dan eskalasi

1. Tambahkan tool internal read-only dengan otorisasi server-side untuk pertanyaan terstruktur, misalnya “menu jadwal saya” atau “status PPDB saya”.
2. Setiap tool memeriksa policy yang sama dengan modul asal; model tidak pernah menerima akses database langsung.
3. Tambahkan eskalasi ke admin/petugas bila kepercayaan rendah atau pertanyaan menyangkut data sensitif.

**Kriteria selesai:** bot dapat membantu informasi personal yang diizinkan tanpa melampaui hak akses pengguna.

## 10. Pengujian dan penerimaan

- **Unit:** prompt builder, pemilihan artikel bantuan, redaksi data sensitif, parser respons, dan penanganan error API.
- **Feature:** autentikasi, kepemilikan percakapan, role visibility, rate limit, validasi input, penghapusan/retensi, dan feature flag.
- **Livewire/browser:** kirim pesan, loading, error, scroll riwayat, keyboard accessibility, tema terang/gelap, dan mobile.
- **Security:** uji prompt injection, akses silang antar-user, role escalation, payload terlalu besar, serta memastikan API key tidak muncul di respons/log frontend.
- **Eval set:** siapkan minimal 30 pertanyaan representatif (PPDB, akademik, keuangan, penggunaan menu, pertanyaan terlarang) dengan jawaban/rubrik yang disetujui sekolah.

## 11. Berkas yang diperkirakan berubah

```text
config/ai-chat.php
config/services.php                         # hanya bila perlu menambah alias konfigurasi
app/Services/AiChatService.php
app/Services/AiChatPromptBuilder.php
app/Livewire/Assistant/HelpChat.php
resources/views/livewire/assistant/help-chat.blade.php
app/Models/AiChatConversation.php
app/Models/AiChatMessage.php
app/Models/AiKnowledgeArticle.php           # fase 2
database/migrations/*_create_ai_chat_*.php
routes/web.php
resources/views/layouts/admin.blade.php atau header/sidebar terkait
routes/console.php                          # retensi percakapan
tests/Feature/AiChatTest.php
tests/Unit/AiChatServiceTest.php
```

## 12. Urutan pelaksanaan yang disarankan

Mulai dari Fase 1 dalam satu feature branch, lakukan uji dengan `Http::fake()` terlebih dahulu, lalu aktifkan `AI_CHAT_ENABLED` hanya pada lingkungan staging. Setelah prompt, limit, dan eval set stabil, baru aktifkan pada produksi. Fase 2 dan 3 tidak perlu dikerjakan sebelum MVP read-only aman dan terukur.
