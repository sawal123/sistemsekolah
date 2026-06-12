<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAISeoService
{
    public function generateForSchool(array $profile): array
    {
        $apiKey = config('services.openai.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('OPENAI_API_KEY belum dikonfigurasi.');
        }

        $schoolName = trim((string) ($profile['school_name'] ?? ''));

        if (mb_strlen($schoolName) < 3) {
            throw new RuntimeException('Nama sekolah wajib diisi sebelum generate SEO.');
        }

        $context = collect([
            'Nama sekolah' => $schoolName,
            'Nama website' => $profile['site_name'] ?? '',
            'Title website' => $profile['site_title'] ?? '',
            'Tagline' => $profile['site_tagline'] ?? '',
            'Alamat' => $profile['school_address'] ?? '',
            'Telepon' => $profile['school_phone'] ?? '',
            'Email' => $profile['school_email'] ?? '',
            'Website' => $profile['school_website'] ?? '',
        ])->map(fn ($value, $key) => "{$key}: {$value}")->implode("\n");

        $response = Http::withToken($apiKey)
            ->timeout(45)
            ->acceptJson()
            ->post('https://api.openai.com/v1/responses', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'input' => [
                    [
                        'role' => 'system',
                        'content' => [[
                            'type' => 'input_text',
                            'text' => 'Anda adalah spesialis SEO untuk website sekolah menengah atas di Indonesia. Balas hanya JSON valid dengan key: seo_title, seo_description, seo_keywords.',
                        ]],
                    ],
                    [
                        'role' => 'user',
                        'content' => [[
                            'type' => 'input_text',
                            'text' => "Buat SEO default untuk website sekolah menengah atas berdasarkan profil berikut.\n\n{$context}\n\nAturan:\n- seo_title maksimal 70 karakter, jelas menyebut SMA/sekolah menengah atas dan nama sekolah.\n- seo_description maksimal 180 karakter, natural untuk hasil Google, menonjolkan pendidikan SMA, prestasi, PPDB, akademik, dan informasi sekolah.\n- seo_keywords berisi 8-12 keyword dipisahkan koma, relevan untuk SMA Indonesia.\n- Jangan mengarang prestasi spesifik yang tidak ada di profil.\n- Bahasa Indonesia.",
                        ]],
                    ],
                ],
                'text' => [
                    'format' => ['type' => 'json_object'],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException($response->json('error.message') ?: 'Gagal menghubungi OpenAI.');
        }

        $data = $this->decodeResponse($response->json());

        return [
            'seo_title' => mb_substr(trim($data['seo_title'] ?? ''), 0, 70),
            'seo_description' => mb_substr(trim($data['seo_description'] ?? ''), 0, 180),
            'seo_keywords' => mb_substr(trim(is_array($data['seo_keywords'] ?? null) ? implode(', ', $data['seo_keywords']) : ($data['seo_keywords'] ?? '')), 0, 255),
        ];
    }

    public function generate(string $content): array
    {
        $apiKey = config('services.openai.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('OPENAI_API_KEY belum dikonfigurasi.');
        }

        $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags($content)));

        if (mb_strlen($plainContent) < 80) {
            throw new RuntimeException('Konten terlalu pendek untuk dianalisis AI.');
        }

        $response = Http::withToken($apiKey)
            ->timeout(45)
            ->acceptJson()
            ->post('https://api.openai.com/v1/responses', [
                'model' => config('services.openai.model', 'gpt-5.5'),
                'input' => [
                    [
                        'role' => 'system',
                        'content' => [[
                            'type' => 'input_text',
                            'text' => 'Anda adalah editor SEO untuk website sekolah Indonesia. Balas hanya JSON valid dengan key: title, meta_title, meta_description, meta_keywords.',
                        ]],
                    ],
                    [
                        'role' => 'user',
                        'content' => [[
                            'type' => 'input_text',
                            'text' => "Buat judul blog yang menarik, meta title maksimal 60 karakter, meta description 140-160 karakter, dan 5-8 keyword SEO dari konten berikut:\n\n{$plainContent}",
                        ]],
                    ],
                ],
                'text' => [
                    'format' => ['type' => 'json_object'],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException($response->json('error.message') ?: 'Gagal menghubungi OpenAI.');
        }

        $data = $this->decodeResponse($response->json());

        return [
            'title' => trim($data['title'] ?? ''),
            'meta_title' => trim($data['meta_title'] ?? ''),
            'meta_description' => trim($data['meta_description'] ?? ''),
            'meta_keywords' => trim(is_array($data['meta_keywords'] ?? null) ? implode(', ', $data['meta_keywords']) : ($data['meta_keywords'] ?? '')),
        ];
    }

    public function polishArticleContent(string $content, string $title = ''): string
    {
        $apiKey = config('services.openai.api_key');

        if (blank($apiKey)) {
            throw new RuntimeException('OPENAI_API_KEY belum dikonfigurasi.');
        }

        $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags($content)));

        if (mb_strlen($plainContent) < 80) {
            throw new RuntimeException('Konten artikel terlalu pendek untuk dirapikan AI.');
        }

        $response = Http::withToken($apiKey)
            ->timeout(60)
            ->acceptJson()
            ->post('https://api.openai.com/v1/responses', [
                'model' => config('services.openai.model', 'gpt-4o-mini'),
                'input' => [
                    [
                        'role' => 'system',
                        'content' => [[
                            'type' => 'input_text',
                            'text' => 'Anda adalah editor bahasa Indonesia untuk artikel website sekolah. Rapikan kalimat agar mudah dipahami, formal hangat, informatif, dan tetap natural. Jangan mengarang fakta baru. Balas hanya JSON valid dengan key: polished_content.',
                        ]],
                    ],
                    [
                        'role' => 'user',
                        'content' => [[
                            'type' => 'input_text',
                            'text' => "Judul artikel: {$title}\n\nRapikan konten HTML berikut.\n\nAturan:\n- Pertahankan fakta, nama, tanggal, angka, dan makna asli.\n- Jangan menambah informasi baru.\n- Boleh memperbaiki ejaan, alur paragraf, transisi, dan kalimat yang kaku.\n- Pertahankan struktur HTML ringan seperti <p>, <h2>, <h3>, <ul>, <ol>, <li>, <blockquote>, <strong>, <em>, dan <a> jika ada.\n- Jangan bungkus dengan markdown/backtick.\n\nKonten:\n{$content}",
                        ]],
                    ],
                ],
                'text' => [
                    'format' => ['type' => 'json_object'],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException($response->json('error.message') ?: 'Gagal menghubungi OpenAI.');
        }

        $data = $this->decodeResponse($response->json());
        $polished = trim((string) ($data['polished_content'] ?? ''));

        if ($polished === '') {
            throw new RuntimeException('AI belum menghasilkan konten yang bisa digunakan.');
        }

        return $polished;
    }

    private function decodeResponse(array $response): array
    {
        $outputText = $response['output_text'] ?? null;

        if (! $outputText) {
            $outputText = collect($response['output'] ?? [])
                ->flatMap(fn ($item) => $item['content'] ?? [])
                ->firstWhere('type', 'output_text')['text'] ?? null;
        }

        $data = json_decode($outputText ?? '', true);

        if (! is_array($data)) {
            throw new RuntimeException('Respons AI tidak dapat dibaca.');
        }

        return $data;
    }
}
