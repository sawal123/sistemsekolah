<?php

namespace App\Services;

use App\Models\WebsitePopup;
use App\Models\WebsitePopupImpression;
use Illuminate\Http\Request;

class VisitorPopupService
{
    /** IP + User-Agent identifies the visitor without retaining these raw values. */
    public function identity(Request $request): array
    {
        $key = (string) config('app.key');
        $ipHash = hash_hmac('sha256', (string) $request->ip(), $key);
        $deviceHash = hash_hmac('sha256', trim((string) $request->userAgent()), $key);

        return [
            'visitor_key' => hash_hmac('sha256', $ipHash.':'.$deviceHash, $key),
            'ip_hash' => $ipHash,
            'device_hash' => $deviceHash,
        ];
    }

    public function nextFor(Request $request): ?WebsitePopup
    {
        $this->forgetExpiredImpressions();
        $identity = $this->identity($request);

        return WebsitePopup::where('is_active', true)
            ->whereDoesntHave('impressions', fn ($query) => $query->where('visitor_key', $identity['visitor_key']))
            ->latest('id')->first();
    }

    public function claim(WebsitePopup $popup, Request $request): bool
    {
        $this->forgetExpiredImpressions();
        $identity = $this->identity($request);

        // firstOrCreate recovers concurrent unique-key conflicts; only the winner displays.
        return WebsitePopupImpression::firstOrCreate([
            'website_popup_id' => $popup->id,
            'visitor_key' => $identity['visitor_key'],
        ], [
            'ip_hash' => $identity['ip_hash'],
            'device_hash' => $identity['device_hash'],
            'displayed_at' => now(),
        ])->wasRecentlyCreated;
    }

    /**
     * Remove display history that has exceeded the interval configured on its popup.
     */
    public function forgetExpiredImpressions(): int
    {
        $now = now();
        $deleted = 0;

        WebsitePopupImpression::with('popup:id,display_interval_hours')
            ->lazyById()
            ->each(function (WebsitePopupImpression $impression) use ($now, &$deleted): void {
                $intervalHours = max(1, (int) ($impression->popup?->display_interval_hours ?? 24));

                if ($impression->displayed_at?->lte($now->copy()->subHours($intervalHours))) {
                    $impression->delete();
                    $deleted++;
                }
            });

        return $deleted;
    }
}
