<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PpdbGelombangRiwayat extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'terjadi_pada' => 'datetime',
    ];

    public function gelombang()
    {
        return $this->belongsTo(PpdbGelombang::class, 'ppdb_gelombang_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
