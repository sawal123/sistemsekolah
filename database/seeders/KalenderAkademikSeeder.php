<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KalenderAkademikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');
        
        $holidays = [
            // Contoh Libur Nasional Indonesia
            ['tanggal' => "$currentYear-01-01", 'jenis_libur' => 'nasional', 'keterangan' => 'Tahun Baru Masehi', 'is_libur' => true],
            ['tanggal' => "$currentYear-05-01", 'jenis_libur' => 'nasional', 'keterangan' => 'Hari Buruh Internasional', 'is_libur' => true],
            ['tanggal' => "$currentYear-06-01", 'jenis_libur' => 'nasional', 'keterangan' => 'Hari Lahir Pancasila', 'is_libur' => true],
            ['tanggal' => "$currentYear-08-17", 'jenis_libur' => 'nasional', 'keterangan' => 'Hari Kemerdekaan RI', 'is_libur' => true],
            ['tanggal' => "$currentYear-12-25", 'jenis_libur' => 'nasional', 'keterangan' => 'Hari Raya Natal', 'is_libur' => true],
            
            // Contoh Libur Sekolah/Kalender Khusus
            // Misalnya hari ini, atau besok (untuk tujuan tes)
            ['tanggal' => date('Y-m-d', strtotime('+2 days')), 'jenis_libur' => 'sekolah', 'keterangan' => 'Rapat dewan Guru Akhir Bulan', 'is_libur' => true],
        ];

        foreach ($holidays as $holiday) {
            DB::table('kalender_akademiks')->updateOrInsert(
                ['tanggal' => $holiday['tanggal']],
                $holiday
            );
        }
    }
}
