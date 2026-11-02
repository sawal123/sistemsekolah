<?php

namespace App\Console\Commands;

use App\Services\VisitorPopupService;
use Illuminate\Console\Command;

class PurgeExpiredWebsitePopupImpressions extends Command
{
    protected $signature = 'website-popups:purge-impressions';

    protected $description = 'Remove expired visitor display history for website popups';

    public function handle(VisitorPopupService $visitorPopupService): int
    {
        $deleted = $visitorPopupService->forgetExpiredImpressions();

        $this->info("{$deleted} riwayat modal kedaluwarsa dihapus.");

        return self::SUCCESS;
    }
}
