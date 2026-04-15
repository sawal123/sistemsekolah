<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'nominal'  => 'float',
    ];

    // Kategori yang tersedia
    public const KATEGORIS = [
        'SPP Bulanan',
        'Uang Bangunan',
        'Uang Seragam',
        'Uang Kegiatan',
        'Lainnya',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function pembayaranSpps()
    {
        return $this->hasMany(PembayaranSpp::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeForJenjang($query, string $jenjang)
    {
        return $query->where(function ($q) use ($jenjang) {
            $q->where('jenjang', $jenjang)->orWhere('jenjang', 'Semua');
        });
    }

    public function scopeBulanan($query)
    {
        return $query->where('kategori', 'SPP Bulanan');
    }

    // ── Helpers ───────────────────────────────────────────────

    public function getIsBulananAttribute(): bool
    {
        return $this->kategori === 'SPP Bulanan';
    }
}
