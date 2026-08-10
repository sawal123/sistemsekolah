<?php

namespace App\Models;

use App\Models\Pembayaran;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'nominal' => 'float',
        'potongan' => 'float',
        'jatuh_tempo' => 'date',
        'tahun' => 'integer',
        'bulan' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function siswa()
    {
        return $this->belongsTo(Siswa::class)->withTrashed();
    }

    public function spp()
    {
        return $this->belongsTo(Spp::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getTotalTerbayarAttribute(): float
    {
        return $this->pembayarans()->sum('nominal');
    }

    public function getSisaTagihanAttribute(): float
    {
        return max(0, $this->nominal - $this->total_terbayar - $this->potongan);
    }

    public function getPersentaseTerbayarAttribute(): float
    {
        if ($this->nominal <= 0) {
            return 100;
        }

        return min(100, round(($this->total_terbayar / $this->nominal) * 100, 1));
    }

    public function getNamaBulanAttribute(): string
    {
        if (! $this->bulan) {
            return '-';
        }

        $bulanNames = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $bulanNames[$this->bulan] ?? '-';
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->whereNotIn('status', ['Lunas', 'Dibatalkan']);
    }

    public function scopeUntukTahun($query, int $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    // ── Helpers ───────────────────────────────────────────────

    /**
     * Catat pembayaran dan perbarui status tagihan secara otomatis.
     *
     * @throws \InvalidArgumentException bila nominal <= 0 atau melebihi sisa tagihan
     */
    public function bayar(float $nominal, string $metode = 'Tunai', ?int $petugasId = null, ?string $keterangan = null, ?\Carbon\Carbon $tanggal = null): Pembayaran
    {
        if ($nominal <= 0) {
            throw new \InvalidArgumentException('Nominal pembayaran harus lebih dari 0.');
        }

        $sisa = $this->sisa_tagihan;
        if ($nominal > $sisa) {
            throw new \InvalidArgumentException(
                "Nominal pembayaran (Rp {$nominal}) melebihi sisa tagihan (Rp {$sisa})."
            );
        }

        $pembayaran = $this->pembayarans()->create([
            'tanggal_bayar' => $tanggal ?? now()->toDateString(),
            'nominal' => $nominal,
            'metode' => $metode,
            'petugas_id' => $petugasId ?? (auth()->check() ? auth()->id() : null),
            'keterangan' => $keterangan,
        ]);

        // Perbarui status tagihan
        $this->refreshStatus();

        return $pembayaran;
    }

    /**
     * Perbarui status berdasarkan total terbayar.
     */
    public function refreshStatus(): void
    {
        if ($this->sisa_tagihan <= 0) {
            $this->update(['status' => 'Lunas']);
        } elseif ($this->total_terbayar > 0) {
            $this->update(['status' => 'Lunas Sebagian']);
        } else {
            $this->update(['status' => 'Belum Lunas']);
        }
    }

    /**
     * Generate tagihan untuk satu siswa berdasarkan template SPP.
     */
    public static function generateDariSpp(Siswa $siswa, Spp $spp, int $tahun, ?int $bulan = null, ?string $jatuhTempo = null): self
    {
        // Cek apakah tagihan sudah ada
        $existing = static::where('siswa_id', $siswa->id)
            ->where('spp_id', $spp->id)
            ->where('tahun', $tahun)
            ->when($bulan, fn($q) => $q->where('bulan', $bulan))
            ->first();

        if ($existing) {
            return $existing;
        }

        return static::create([
            'siswa_id' => $siswa->id,
            'spp_id' => $spp->id,
            'jenis_biaya' => $spp->kategori,
            'tahun' => $tahun,
            'bulan' => $bulan,
            'nominal' => $spp->nominal,
            'jatuh_tempo' => $jatuhTempo,
            'status' => 'Belum Lunas',
        ]);
    }
}
