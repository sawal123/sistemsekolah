<?php

namespace App\Services;

class HolidayService
{
    /**
     * Daftar Hari Libur Resmi Indonesia 2026 (SKB 3 Menteri)
     */
    public static function getHolidays($year)
    {
        if ($year != 2026) {
            return []; // Untuk tahun lain, sistem akan mengandalkan API atau input manual
        }

        return [
            ['date' => '2026-01-01', 'name' => 'Tahun Baru 2026 Masehi', 'type' => 'nasional'],
            ['date' => '2026-01-18', 'name' => 'Isra Mi\'raj Nabi Muhammad SAW', 'type' => 'nasional'],
            ['date' => '2026-02-17', 'name' => 'Tahun Baru Imlek 2577 Kongzili', 'type' => 'nasional'],
            ['date' => '2026-03-19', 'name' => 'Hari Suci Nyepi (Saka 1948)', 'type' => 'nasional'],
            ['date' => '2026-03-20', 'name' => 'Cuti Bersama Hari Suci Nyepi', 'type' => 'darurat'],

            // Pekan Idul Fitri
            ['date' => '2026-03-30', 'name' => 'Cuti Bersama Idul Fitri 1447H', 'type' => 'darurat'],
            ['date' => '2026-03-31', 'name' => 'Hari Raya Idul Fitri 1447H (Hari 1)', 'type' => 'nasional'],
            ['date' => '2026-04-01', 'name' => 'Hari Raya Idul Fitri 1447H (Hari 2)', 'type' => 'nasional'],
            ['date' => '2026-04-02', 'name' => 'Cuti Bersama Idul Fitri 1447H', 'type' => 'darurat'],
            ['date' => '2026-04-03', 'name' => 'Wafat Yesus Kristus / Cuti Bersama', 'type' => 'nasional'],

            // ['date' => '2026-04-05', 'name' => 'Hari Paskah', 'type' => 'nasional'],
            ['date' => '2026-05-01', 'name' => 'Hari Buruh Internasional', 'type' => 'nasional'],
            ['date' => '2026-05-14', 'name' => 'Kenaikan Yesus Kristus', 'type' => 'nasional'],
            ['date' => '2026-05-15', 'name' => 'Cuti Bersama Kenaikan Yesus Kristus', 'type' => 'darurat'],
            ['date' => '2026-05-21', 'name' => 'Cuti Bersama Hari Raya Waisak', 'type' => 'darurat'],
            ['date' => '2026-05-22', 'name' => 'Hari Raya Waisak 2570 BE', 'type' => 'nasional'],
            ['date' => '2026-06-01', 'name' => 'Hari Lahir Pancasila', 'type' => 'nasional'],
            ['date' => '2026-06-27', 'name' => 'Hari Raya Idul Adha 1447H', 'type' => 'nasional'],
            ['date' => '2026-07-17', 'name' => 'Tahun Baru Islam 1448H', 'type' => 'nasional'],
            ['date' => '2026-08-17', 'name' => 'Hari Kemerdekaan RI ke-81', 'type' => 'nasional'],
            ['date' => '2026-09-15', 'name' => 'Maulid Nabi Muhammad SAW', 'type' => 'nasional'],
            ['date' => '2026-12-25', 'name' => 'Hari Raya Natal', 'type' => 'nasional'],
            ['date' => '2026-12-26', 'name' => 'Cuti Bersama Hari Raya Natal', 'type' => 'darurat'],
        ];
    }
}
