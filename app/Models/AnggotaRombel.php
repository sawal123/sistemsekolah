<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggotaRombel extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class)->withTrashed();
    }
}
