<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranSpp extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar'  => 'float',
        'potongan'      => 'float',
        'bulan'         => 'integer',
        'tahun'         => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function spp()
    {
        return $this->belongsTo(Spp::class);
    }

    /**
     * Petugas TU yang menerima pembayaran
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────

    public function getNettoBayarAttribute(): float
    {
        return $this->jumlah_bayar - $this->potongan;
    }

    public function getNamaBulanAttribute(): string
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April',   5 => 'Mei',      6 => 'Juni',
            7 => 'Juli',    8 => 'Agustus',  9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $this->bulan ? ($bulanNames[$this->bulan] ?? '-') : 'Sekali Bayar';
    }
}
