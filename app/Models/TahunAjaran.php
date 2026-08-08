<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    protected $guarded = ['id'];

    public function nilais() { return $this->hasMany(Nilai::class); }
    public function rapors() { return $this->hasMany(Rapor::class); }
    public function spps() { return $this->hasMany(Spp::class); }
    public function keanggotaanKelas() { return $this->hasMany(KelasSiswa::class); }

    public static function forDate($date): ?self
    {
        $date = \Carbon\Carbon::parse($date);
        $tahun = $date->month >= 7
            ? $date->year.'/'.($date->year + 1)
            : ($date->year - 1).'/'.$date->year;
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
