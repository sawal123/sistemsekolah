<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Status: Draft, Aktif, Ditutup, Diarsipkan.
     * Hanya Draft yang boleh dihapus (belum memiliki transaksi akademik).
     */
    public function canBeDeleted(): bool
    {
        return $this->status === 'Draft';
    }

    public function isDeletable(): bool
    {
        return $this->canBeDeleted();
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }
    public function rapors()
    {
        return $this->hasMany(Rapor::class);
    }
    public function spps()
    {
        return $this->hasMany(Spp::class);
    }
    public function rombels()
    {
        return $this->hasMany(Rombel::class);
    }

    public static function forDate($date, ?\Illuminate\Support\Collection $preloaded = null): ?self
    {
        $date = \Carbon\Carbon::parse($date);
        $dateStr = $date->toDateString();

        if ($preloaded !== null) {
            $byRange = $preloaded->first(function (self $ta) use ($dateStr) {
                return $ta->tanggal_mulai !== null
                    && $ta->tanggal_selesai !== null
                    && $ta->tanggal_mulai->toDateString() <= $dateStr
                    && $ta->tanggal_selesai->toDateString() >= $dateStr;
            });

            if ($byRange) {
                return $byRange;
            }

            $tahun = $date->month >= 7
                ? $date->year . '/' . ($date->year + 1)
                : ($date->year - 1) . '/' . $date->year;
            $semester = $date->month >= 7 ? 'Ganjil' : 'Genap';
            $allowedSemesters = [$semester, strtolower($semester), $semester === 'Ganjil' ? '1' : '2'];

            return $preloaded->first(function (self $ta) use ($tahun, $allowedSemesters) {
                return $ta->tahun === $tahun && in_array((string) $ta->semester, $allowedSemesters, true);
            });
        }

        // Prioritaskan pencarian berdasarkan tanggal_mulai/tanggal_selesai jika tersedia
        $byRange = static::whereNotNull('tanggal_mulai')
            ->whereNotNull('tanggal_selesai')
            ->where('tanggal_mulai', '<=', $dateStr)
            ->where('tanggal_selesai', '>=', $dateStr)
            ->first();

        if ($byRange) {
            return $byRange;
        }

        // Fallback ke logika semester-based
        $tahun = $date->month >= 7
            ? $date->year . '/' . ($date->year + 1)
            : ($date->year - 1) . '/' . $date->year;
        $semester = $date->month >= 7 ? 'Ganjil' : 'Genap';

        return static::where('tahun', $tahun)
            ->where(function ($query) use ($semester) {
                $query->where('semester', $semester)
                    ->orWhere('semester', strtolower($semester))
                    ->orWhere('semester', $semester === 'Ganjil' ? '1' : '2');
            })
            ->first();
    }
}
