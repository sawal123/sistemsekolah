<?php

namespace App\Models;

use App\Models\Concerns\BelongsToUnit;
use Illuminate\Database\Eloquent\Model;

class PpdbGelombang extends Model
{
    use BelongsToUnit;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'dibuka_pada' => 'datetime',
        'ditutup_pada' => 'datetime',
    ];

    public function pendaftarans()
    {
        return $this->hasMany(PendaftaranMuridBaru::class);
    }

    public function riwayats()
    {
        return $this->hasMany(PpdbGelombangRiwayat::class)->latest('terjadi_pada');
    }

    public function getIsDibukaAttribute(): bool
    {
        return $this->status === 'Dibuka';
    }
}
