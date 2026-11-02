<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsitePopup extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'display_interval_hours' => 'integer',
    ];

    public function impressions(): HasMany
    {
        return $this->hasMany(WebsitePopupImpression::class);
    }
}
