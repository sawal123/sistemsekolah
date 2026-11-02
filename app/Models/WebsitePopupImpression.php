<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsitePopupImpression extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = ['displayed_at' => 'datetime'];

    public function popup(): BelongsTo
    {
        return $this->belongsTo(WebsitePopup::class, 'website_popup_id');
    }
}
