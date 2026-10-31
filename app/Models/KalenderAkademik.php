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

    /**
     * Ambil peta tanggal libur dalam satu bulan dengan 1 query (mencegah N+1 di loop kalender).
     *
     * @return array<string, bool> Map ['YYYY-MM-DD' => true] untuk semua tanggal libur/Minggu di bulan tsb
     */
    public static function getMapHariLiburBulan(int|string $tahun, int|string $bulan): array
    {
        $start = Carbon::createFromDate((int) $tahun, (int) $bulan, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $dbHolidays = self::whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->where('is_libur', true)
            ->pluck('tanggal')
            ->mapWithKeys(fn ($tgl) => [Carbon::parse($tgl)->format('Y-m-d') => true])
            ->all();

        $map = [];
        $daysInMonth = $start->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dateObj = Carbon::createFromDate((int) $tahun, (int) $bulan, $i);
            $dateStr = $dateObj->format('Y-m-d');
            if ($dateObj->isSunday() || isset($dbHolidays[$dateStr])) {
                $map[$dateStr] = true;
            }
        }

        return $map;
    }
}
