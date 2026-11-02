<?php

namespace App\Providers;

use App\Models\PpdbGelombang;
use App\Models\Setting;
use App\Services\VisitorPopupService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.landing', function ($view) {
            $view->with('siteSettings', Setting::pluck('value', 'key'));
            $view->with('visitorPopup', app(VisitorPopupService::class)->nextFor(request()));
            $view->with('siteOpenWave', PpdbGelombang::where('status', 'Dibuka')
                ->orderByDesc('tahun_pendaftaran')->orderBy('tanggal_mulai')->first());
        });

        Event::listen(function (Login $event) {
            $event->user->update(['last_login_at' => now()]);
        });
    }
}
