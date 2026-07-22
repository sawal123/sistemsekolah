<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiAiScanner
{
    /**
     * Memproses gambar absensi dan mengabstraksi data matriks absensinya menggunakan API Gemini.
     * 
     * @param string $base64Image String base64 dari gambar yang diupload
     * @param string $mimeType Mime type misal "image/jpeg"
     * @param array $daftarSiswa Daftar referensi "no" => "nama"
     * @param int $tahun Tahun aktif misal 2026
     * @param int|string $bulan Bulan aktif misal 04
     * @param array $tanggalLibur Daftar tanggal hari libur yang harus ditinggalkan AI
     * @return array Matrix status absen
     */
    public static function scanAbsenMatriks($base64Image, $mimeType, $daftarSiswa, $tahun, $bulan, $tanggalLibur)
    {
        // Berikan waktu lebih lama untuk memproses AI (120 detik) agar tidak terkena timeout PHP
        set_time_limit(120);

        $apiKey = trim(config('services.gemini.api_key'), "\"' ");
        
        if (empty($apiKey)) {
            throw new Exception("Sistem Kesalahan: Belum ada kunci API Gemini yang didaftarkan (GEMINI_API_KEY).");
        }

        // Tembak menggunakan Gemini Flash Latest (Model stabil yang tersedia untuk key ini)
        $model = "gemini-flash-latest";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        // Racik instruksi sistem super ketat karena Gemini suka ngoceh markdown JSON
        $prompt = "Anda adalah asisten super akurat bertugas mendigitalisasi tabel matriks gambar rekap absensi guru kertas menjadi JSON murni yang solid.\n\n";
        
        $prompt .= "1. TARGET SISWA (Berdasarkan baris Nomor): \n" . json_encode($daftarSiswa, JSON_PRETTY_PRINT). "\n\n";
        $prompt .= "2. PERINGATAN TANGGAL: Abaikan seluruh contreng absensi/Tanda pada tanggal libur atau hari minggu berikut ini (Jangan masukan tanggal ini di datasetmu): \n" . json_encode($tanggalLibur, JSON_PRETTY_PRINT) . "\n\n";
        
        $prompt .= "3. ATURAN HASIL (OUTPUT RULES):\n";
        $prompt .= "- **VERIFIKASI IDENTITAS (WAJIB)**: Sebelum memproses baris, bandingkan Nama yang tertulis di kertas dengan data `nama_lengkap` yang saya berikan. Jika nama di kertas berbeda jauh (bukan sekadar typo kecil), maka ABAIKAN baris tersebut. Jangan masukkan ke JSON.\n";
        $prompt .= "- **STRICT ROW LOCKING (PENTING)**: Kunci data absensi berdasarkan Nomor Urut yang tertera di kolom paling kiri kertas. Pastikan data tidak loncat antar baris. Misalnya, tanda di samping nomor 29 harus tetap menjadi milik nomor 29, dilarang dipindahkan ke nomor 32 meskipun ada kemiripan nama.\n";
        $prompt .= "- **DETEKSI TANDA KETAT**: Hanya catat tanda jika Anda yakin 100% itu adalah goresan pulpen sengaja. Jangan terkecoh oleh perpotongan garis grid tabel, noda kertas, atau bayangan lipatan.\n";
        $prompt .= "- **INTERPRETASI TANDA**:\n";
        $prompt .= "  - Tanda centang (v atau ✓), garis miring (/), atau titik (.) = 'hadir'.\n";
        $prompt .= "  - Huruf 'S' = 'sakit', 'I' = 'izin', 'A' = 'alpa'.\n";
        $prompt .= "- Kamu harus membalas HANYA DALAM FORMAT JSON murni, tanpa backtick awalan (```json) atau pun penutup.\n";
        $prompt .= "- Struktur: Objek dengan Key \"siswa_id\" (dari `database_siswa_id` yang saya berikan), dan Value adalah Array list absensinya.\n";
        $prompt .= "- Gunakan format array obyek berisi 'tanggal' (YYYY-MM-DD) dan 'status' ('hadir', 'sakit', 'izin', atau 'alpa').\n";
        $prompt .= "- Abaikan kolom tanggal yang kosong. Hanya catat yang benar-benar ada tandanya.\n\n";
        $prompt .= "CONTOH EXPETASI JSON SEMPURNA:\n";
        $prompt .= "{\n  \"12\": [\n    {\"tanggal\": \"$tahun-$bulan-01\", \"status\": \"sakit\"},\n    {\"tanggal\": \"$tahun-$bulan-15\", \"status\": \"alpa\"}\n  ]\n}";

        $payload = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt],
                        [
                            "inline_data" => [
                                "mime_type" => $mimeType,
                                "data" => $base64Image
                            ]
                        ]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.0,
                "response_mime_type" => "application/json"
            ]
        ];

        try {
            // Tambahkan logika retry: Coba lagi hingga 3x dengan jeda 2 detik jika terkena Rate Limit (429)
            $response = Http::timeout(60)
                ->retry(3, 2000, function ($exception, $request) {
                    return $exception instanceof \Illuminate\Http\Client\ConnectionException ||
                           (isset($exception->response) && $exception->response->status() == 429);
                })
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($url, $payload);
            
            if ($response->successful()) {
                $responseBody = $response->json();
                
                if (isset($responseBody['candidates'][0]['content']['parts'][0]['text'])) {
                    $jsonText = $responseBody['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Terkadang AI masih bandel menyisipkan teks luar JSON, ini lapisan parser darurat
                    $jsonText = trim(str_replace(['```json', '```'], '', $jsonText));

                    $dataArray = json_decode($jsonText, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $dataArray;
                    } else {
                        Log::error('AI gagal membentuk JSON valid: ' . json_last_error_msg());
                        throw new Exception("AI berhasil menerawang matriks absen, tetapi struktur laporannya rusak. Coba foto ulang dengan resolusi yang lebih tajam.");
                    }
                } else {
                    Log::error('Response struktur Gemini berubah: ' . json_encode($responseBody));
                    throw new Exception("Gagal melacak pola respons server AI.");
                }

            } else {
                $errorBody = $response->body();
                Log::error('Gemini API Error Detail: ' . $errorBody);
                $status = $response->status();

                if ($status == 429) {
                    throw new Exception("Lalu lintas AI sedang padat (Rate Limit). Google membatasi kecepatan untuk akun gratis. Silakan tunggu 10 detik lalu klik 'Pindai' kembali.");
                }

                if ($status == 404) {
                    throw new Exception("Mata AI tidak menemukan Model. Pastikan API Key valid dan model '{$model}' aktif di region Anda.");
                }

                throw new Exception("Koneksi internet sekolah lengah atau server AI sibuk (Kode Error: " . $status . ")");
            }

        } catch (Exception $e) {
            throw new Exception("Kegagalan Mata Buatan (AI Scanner): " . $e->getMessage());
        }
    }
}
