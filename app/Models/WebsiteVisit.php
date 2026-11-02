<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class WebsiteVisit extends Model
{
    use MassPrunable;

    protected $guarded = ['id'];

    protected $casts = [
        'visited_at' => 'datetime',
        'is_suspicious' => 'boolean',
    ];

    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    /**
     * Retensi data: hapus log kunjungan normal > 90 hari, dan log mencurigakan > 180 hari.
     */
    public function prunable(): Builder
    {
        return static::where(function (Builder $query) {
            $query->where('is_suspicious', false)
                ->where('visited_at', '<=', now()->subDays(90));
        })->orWhere(function (Builder $query) {
            $query->where('is_suspicious', true)
                ->where('visited_at', '<=', now()->subDays(180));
        });
    }
}
