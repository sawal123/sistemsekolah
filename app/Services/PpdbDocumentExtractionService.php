<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PpdbDocumentExtractionService
{
    public function extract(string $documentType, string $path, string $filename, string $mimeType, array $context = []): array
    {
        $apiKey = config('services.openai.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('OPENAI_API_KEY belum dikonfigurasi.');
        }

        if (! file_exists($path)) {
            throw new RuntimeException('Berkas tidak ditemukan.');
        }

        $content = [
            $this->buildFileContent($path, $filename, $mimeType),
            [
                'type' => 'input_text',
                'text' => $this->prompt($documentType, $context),
            ],
        ];

        $response = Http::withToken($apiKey)
            ->timeout(120)
            ->acceptJson()
            ->post('https://api.openai.com/v1/responses', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'input' => [
                    [
                        'role' => 'system',
                        'content' => [[
                            'type' => 'input_text',
                            'text' => 'Anda adalah petugas PPDB Indonesia yang mengekstrak data dari dokumen resmi. Balas hanya JSON valid. Jangan mengarang data. Jika tidak terbaca, isi string kosong.',
                        ]],
                    ],
                    [
                        'role' => 'user',
                        'content' => $content,
                    ],
                ],
                'text' => [
                    'format' => ['type' => 'json_object'],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException($response->json('error.message') ?: 'Gagal menghubungi OpenAI.');
        }

        $outputText = $response->json('output_text');

        if (! $outputText) {
            $outputText = collect($response->json('output', []))
                ->flatMap(fn ($item) => $item['content'] ?? [])
                ->firstWhere('type', 'output_text')['text'] ?? null;
        }

        $data = json_decode($outputText ?? '', true);

        if (! is_array($data)) {
            throw new RuntimeException('Respons AI tidak dapat dibaca sebagai JSON.');
        }

        return $this->normalize($data);
    }

    private function buildFileContent(string $path, string $filename, string $mimeType): array
    {
        $base64 = base64_encode(file_get_contents($path));
        $dataUrl = "data:{$mimeType};base64,{$base64}";

        if (str_starts_with($mimeType, 'image/')) {
            return [
                'type' => 'input_image',
                'image_url' => $dataUrl,
            ];
        }

        return [
            'type' => 'input_file',
            'filename' => $filename,
            'file_data' => $dataUrl,
        ];
    }

    private function prompt(string $documentType, array $context = []): string
    {
        $studentName = trim((string) ($context['nama_lengkap'] ?? ''));

        $context = match ($documentType) {
            'kartu_keluarga' => 'Dokumen ini adalah Kartu Keluarga. Fokus pada nomor KK, alamat, kepala keluarga, data orang tua/wali, dan data domisili. Jangan menentukan nama calon siswa dari KK karena satu KK bisa berisi lebih dari satu anak. Data orang tua hanya boleh diisi jika nama calon siswa dari Ijazah/SKL sudah diberikan di konteks. Cari baris calon siswa pada tabel anggota keluarga PALING ATAS berdasarkan nama konteks tersebut. Setelah baris calon siswa ditemukan, lihat baris yang sama pada tabel lanjutan bawah untuk membaca referensi Nama Orang Tua kolom Ayah dan Ibu. Nama dari kolom Ayah/Ibu di tabel bawah hanya boleh dipakai sebagai referensi pencocokan. Setelah mendapatkan referensi nama ayah/ibu calon siswa, cocokkan nama itu ke tabel anggota keluarga paling atas, lalu ambil nama, NIK, dan jenis pekerjaan dari tabel paling atas. Jangan mengambil data orang tua dari baris lain di tabel bawah, misalnya baris Kepala Keluarga.',
            'ijazah_skl' => 'Dokumen ini adalah Ijazah atau Surat Keterangan Lulus. Fokus hanya pada nama calon siswa, NISN, tempat/tanggal lahir, sekolah asal, NPSN bila ada, dan tahun lulus. Jangan ambil atau isi data orang tua dari Ijazah/SKL.',
            'dokumen_gabungan' => 'Dokumen ini berisi gabungan berkas PPDB seperti KK, Ijazah/SKL, Akta, KTP orang tua, rapor, dan pas foto. Ambil semua data yang relevan untuk formulir PPDB.',
            default => 'Dokumen ini adalah berkas PPDB calon siswa. Ambil data yang relevan untuk formulir pendaftaran.',
        };

        $studentContext = $studentName !== ''
            ? "Nama calon siswa dari Ijazah/SKL/form saat ini: {$studentName}."
            : 'Nama calon siswa dari Ijazah/SKL/form belum tersedia. Jika dokumen adalah KK, kosongkan seluruh field orang tua karena baris anak yang dimaksud belum bisa dipastikan.';

        return <<<PROMPT
{$context}

{$studentContext}

Kembalikan JSON valid dengan struktur datar berikut:
{
  "nama_lengkap": "",
  "nisn": "",
  "nik": "",
  "tempat_lahir": "",
  "tanggal_lahir": "",
  "jenis_kelamin": "",
  "agama": "",
  "alamat": "",
  "rt": "",
  "rw": "",
  "dusun": "",
  "kelurahan": "",
  "kecamatan": "",
  "kota_kabupaten": "",
  "provinsi": "",
  "no_kk": "",
  "sekolah_asal": "",
  "npsn_sekolah_asal": "",
  "tahun_lulus": "",
  "nama_ayah": "",
  "nik_ayah": "",
  "pekerjaan_ayah": "",
  "nama_ibu": "",
  "nik_ibu": "",
  "pekerjaan_ibu": "",
  "nama_wali": "",
  "nik_wali": "",
  "pekerjaan_wali": "",
  "catatan_ai": ""
}

Aturan:
- Gunakan format tanggal YYYY-MM-DD jika tanggal bisa dibaca.
- Untuk jenis_kelamin gunakan hanya "Laki-Laki" atau "Perempuan".
- Untuk agama gunakan salah satu: Islam, Kristen, Katolik, Hindu, Buddha, Konghucu, Lainnya.
- Jangan isi field dengan tebakan. Jika ragu, kosongkan dan jelaskan singkat di catatan_ai.
- Nama lengkap calon siswa harus diprioritaskan dari Ijazah/SKL/Akta. Jangan isi nama calon siswa dari Kartu Keluarga.
- Data orang tua (nama_ayah, nik_ayah, pekerjaan_ayah, nama_ibu, nik_ibu, pekerjaan_ibu, nama_wali, nik_wali, pekerjaan_wali) hanya boleh diisi dari Kartu Keluarga.
- Jika dokumen adalah Ijazah/SKL, semua field orang tua harus dikosongkan walaupun nama orang tua tercetak di dokumen.
- Khusus Kartu Keluarga: nama_ayah, nik_ayah, nama_ibu, nik_ibu, nama_wali, dan nik_wali harus berasal dari tabel anggota keluarga paling atas, kolom "Nama Lengkap" dan "NIK".
- Khusus Kartu Keluarga: identifikasi dulu baris calon siswa dengan mencocokkan nama calon siswa dari konteks ke tabel paling atas. Pada contoh umum, jika calon siswa berada di baris 3, baca kolom "Nama Orang Tua" baris 3 di tabel bawah untuk mendapatkan referensi ayah/ibu calon siswa.
- Khusus Kartu Keluarga: setelah referensi ayah/ibu dari baris calon siswa ditemukan, cari nama tersebut di tabel paling atas. Ambil nama, NIK, dan pekerjaan dari tabel paling atas. Contoh: jika baris calon siswa menulis ayah "SAWALINTO" dan ibu "ERA KOESUMA WAHYU NINGRUM", maka nama_ayah harus "SAWALINTO" dan nama_ibu harus "ERA KOESUMA WAHYU NINGRUM", bukan nama ayah/ibu dari baris kepala keluarga.
- Khusus Kartu Keluarga: pekerjaan ayah/ibu/wali boleh diambil dari kolom "Jenis Pekerjaan" pada tabel paling atas, bukan dari tabel bawah.
- Jika nama calon siswa dari konteks kosong atau tidak cocok dengan tabel atas, kosongkan seluruh field orang tua dan jelaskan di catatan_ai.
PROMPT;
    }

    private function normalize(array $data): array
    {
        $fields = [
            'nama_lengkap', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama',
            'alamat', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kota_kabupaten', 'provinsi',
            'no_kk', 'sekolah_asal', 'npsn_sekolah_asal', 'tahun_lulus', 'nama_ayah', 'nik_ayah',
            'pekerjaan_ayah', 'nama_ibu', 'nik_ibu', 'pekerjaan_ibu', 'nama_wali', 'nik_wali',
            'pekerjaan_wali', 'catatan_ai',
        ];

        $normalized = [];

        foreach ($fields as $field) {
            $value = $data[$field] ?? '';
            $normalized[$field] = is_scalar($value) ? trim((string) $value) : '';
        }

        return $normalized;
    }
}
