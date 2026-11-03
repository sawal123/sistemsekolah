<?php

namespace App\Services;

use App\Models\Fasilitas;
use App\Models\Gallery;
use App\Models\MediaAsset;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use App\Models\WebsitePopup;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaAssetService
{
    public function store(UploadedFile $file, ?User $uploader = null, ?string $altText = null): MediaAsset
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $path = $file->storeAs(
            'media/' . now()->format('Y/m'),
            Str::uuid() . '.' . $extension,
            'public'
        );

        $dimensions = @getimagesize($file->getRealPath()) ?: null;

        return MediaAsset::create([
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'disk' => 'public',
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size' => $file->getSize() ?: 0,
            'width' => $dimensions[0] ?? null,
            'height' => $dimensions[1] ?? null,
            'alt_text' => filled($altText) ? $altText : null,
            'uploaded_by' => $uploader?->id,
        ]);
    }

    /** @return array<int, string> */
    public function usages(MediaAsset $asset): array
    {
        $path = $asset->path;
        $usages = [];

        foreach ([
            'Slider utama' => Slider::where('foto', $path)->count(),
            'Galeri website' => Gallery::where('foto', $path)->count(),
            'Fasilitas' => Fasilitas::where('foto', $path)->count(),
            'Artikel blog' => Post::where('thumbnail', $path)->count(),
            'Modal pengunjung' => WebsitePopup::where('gambar', $path)->count(),
        ] as $label => $count) {
            if ($count > 0) {
                $usages[] = $label . ' (' . $count . ')';
            }
        }

        $settingCount = Setting::query()->where('value', $path)->count();
        if ($settingCount > 0) {
            $usages[] = 'Pengaturan website (' . $settingCount . ')';
        }

        return $usages;
    }

    public function isUsed(MediaAsset $asset): bool
    {
        return $this->usages($asset) !== [];
    }

    public function delete(MediaAsset $asset): void
    {
        Storage::disk($asset->disk)->delete($asset->path);
        $asset->delete();
    }

    /**
     * Register existing user-uploaded content files in the media library.
     *
     * @return int Number of new media records created.
     */
    public function syncExistingContent(): int
    {
        $paths = collect([
            Slider::query()->pluck('foto'),
            Gallery::query()->pluck('foto'),
            Fasilitas::query()->pluck('foto'),
            Post::query()->pluck('thumbnail'),
            WebsitePopup::query()->pluck('gambar'),
        ])->flatten()
            ->filter(fn ($path) => filled($path) && ! Str::startsWith($path, ['http://', 'https://', 'seed-images/', 'assets/']))
            ->unique()
            ->values();

        $created = 0;

        foreach ($paths as $path) {
            if (MediaAsset::where('path', $path)->exists() || ! Storage::disk('public')->exists($path)) {
                continue;
            }

            $absolutePath = Storage::disk('public')->path($path);
            $dimensions = @getimagesize($absolutePath) ?: null;

            MediaAsset::create([
                'original_name' => basename($path),
                'path' => $path,
                'disk' => 'public',
                'mime_type' => Storage::disk('public')->mimeType($path) ?: 'application/octet-stream',
                'size' => Storage::disk('public')->size($path),
                'width' => $dimensions[0] ?? null,
                'height' => $dimensions[1] ?? null,
            ]);

            $created++;
        }

        return $created;
    }
}
