<?php

namespace App\Console\Commands;

use App\Services\MediaAssetService;
use Illuminate\Console\Command;

class SyncMediaAssets extends Command
{
    protected $signature = 'media:sync-content';

    protected $description = 'Tambahkan gambar lama dari konten website ke Asset Media';

    public function handle(MediaAssetService $media): int
    {
        $created = $media->syncExistingContent();
        $this->info("Sinkronisasi selesai. {$created} asset media baru ditambahkan.");

        return self::SUCCESS;
    }
}
