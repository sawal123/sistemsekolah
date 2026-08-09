<?php

namespace App\Models;

use App\Models\Tagihan;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'nominal' => 'float',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getNamaSiswaAttribute(): string
    {
        return $this->tagihan?->siswa?->user?->name ?? '-';
    }

    public function getJenisBiayaAttribute(): string
    {
        return $this->tagihan?->jenis_biaya ?? '-';
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopePadaTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal_bayar', $tanggal);
    }

    public function scopePadaBulan($query, int $tahun, int $bulan)
    {
        return $query->whereYear('tanggal_bayar', $tahun)
            ->whereMonth('tanggal_bayar', $bulan);
    }

    public function scopeOlehPetugas($query, int $petugasId)
    {
        return $query->where('petugas_id', $petugasId);
    }
}
