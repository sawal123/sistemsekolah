<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AddressGeocodingService
{
    public function geocode(array $addressParts): ?string
    {
        $query = $this->buildQuery($addressParts);

        if ($query === '') {
            return null;
        }

        $response = Http::timeout(20)
            ->acceptJson()
            ->withHeaders([
                'User-Agent' => str(config('app.name', 'Sistem Sekolah'))->slug() . '/1.0 (' . config('app.url', 'local') . ')',
            ])
            ->get('https://nominatim.openstreetmap.org/search', [
                'q' => $query,
                'format' => 'jsonv2',
                'limit' => 1,
                'countrycodes' => 'id',
                'addressdetails' => 1,
            ]);

        if ($response->failed()) {
            return null;
        }

        $result = $response->json('0');

        if (! is_array($result) || blank($result['lat'] ?? null) || blank($result['lon'] ?? null)) {
            return null;
        }

        return trim($result['lat']) . ', ' . trim($result['lon']);
    }

    private function buildQuery(array $addressParts): string
    {
        return collect([
            $addressParts['alamat'] ?? null,
            $addressParts['kelurahan'] ?? null,
            $addressParts['kecamatan'] ?? null,
            $addressParts['kota_kabupaten'] ?? null,
            $addressParts['provinsi'] ?? null,
            'Indonesia',
        ])->filter(fn ($part) => filled($part))->implode(', ');
    }
}
