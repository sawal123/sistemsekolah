<?php

namespace App\Support;

use Illuminate\Support\Str;

class PublicImage
{
    /**
     * Resolve CMS uploads and bundled seed assets without requiring storage:link
     * for the latter. User/admin uploads remain served from public storage.
     */
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        if (Str::startsWith($path, 'seed-images/')) {
            return asset('assets/images/'.basename($path));
        }

        if (Str::startsWith($path, 'assets/')) {
            return asset($path);
        }

        return asset('storage/'.$path);
    }
}
