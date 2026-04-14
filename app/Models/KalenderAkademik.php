<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class KalenderAkademik extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'tanggal' => 'date',
        'is_libur' => 'boolean',
    ];

    /**
     * Helper untuk mengecek apakah suatu tanggal (Y-m-d) adalah hari libur.
     * Logika: 
     * 1. Apakah hari Minggu?
     * 2. Apakah ada di tabel (is_libur = true)?
     */
    public static function isHariLibur($tanggal)
    {
        $date = Carbon::parse($tanggal);

        // Jika hari Minggu
        if ($date->isSunday()) {
            return true;
        }

        // Cek di tabel kalender_akademiks
        return self::where('tanggal', $tanggal)->where('is_libur', true)->exists();
    }
}
