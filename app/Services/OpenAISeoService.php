<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAISeoService
{
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

        $outputText = $response->json('output_text');

        if (! $outputText) {
            $outputText = collect($response->json('output', []))
                ->flatMap(fn ($item) => $item['content'] ?? [])
                ->firstWhere('type', 'output_text')['text'] ?? null;
        }

        $data = json_decode($outputText ?? '', true);

        if (! is_array($data)) {
            throw new RuntimeException('Respons AI tidak dapat dibaca.');
        }

        return [
            'title' => trim($data['title'] ?? ''),
            'meta_title' => trim($data['meta_title'] ?? ''),
            'meta_description' => trim($data['meta_description'] ?? ''),
            'meta_keywords' => trim(is_array($data['meta_keywords'] ?? null) ? implode(', ', $data['meta_keywords']) : ($data['meta_keywords'] ?? '')),
        ];
    }
}
