<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteVisit extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'visited_at' => 'datetime',
        'is_suspicious' => 'boolean',
    ];

    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }
}
