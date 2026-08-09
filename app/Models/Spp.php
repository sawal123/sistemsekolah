<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'nominal' => 'float',
        'is_active' => 'boolean',
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

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function pembayaranSpps()
    {
        return $this->hasMany(PembayaranSpp::class);
    }

    /**
     * Tagihan yang dihasilkan dari template SPP ini.
     */
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublicFees($query)
    {
        return $query->whereIn('kategori', ['SPP Bulanan', 'Uang Bangunan']);
    }

    public function scopeApplicableTo($query, string $jenjang, ?int $jurusanId = null)
    {
        return $query
            ->where(fn ($q) => $q->where('jenjang', $jenjang)->orWhere('jenjang', 'Semua'))
            ->where(function ($q) use ($jenjang, $jurusanId) {
                if ($jenjang === 'SMK' && $jurusanId) {
                    $q->whereNull('jurusan_id')->orWhere('jurusan_id', $jurusanId);
                } else {
                    $q->whereNull('jurusan_id');
                }
            });
    }

    // ── Helpers ───────────────────────────────────────────────

    public function getIsBulananAttribute(): bool
    {
        return $this->kategori === 'SPP Bulanan';
    }
}
