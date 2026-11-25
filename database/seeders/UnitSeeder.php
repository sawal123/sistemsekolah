<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'kode' => 'SD',
                'nama' => 'SD Nusantara',
                'npsn' => '20201101',
                'tingkat_awal' => 1,
                'tingkat_akhir' => 6,
                'punya_jurusan' => false,
                'kepala_sekolah' => 'Hj. Siti Aminah, S.Pd.SD',
                'nip_kepala_sekolah' => '19750312 200003 2 001',
                'alamat' => 'Jl. Pendidikan No. 10, Blok A, Kota Bandung',
                'telepon' => '(022) 7011-001',
                'email' => 'sd@sekolah.com',
                'is_active' => true,
                'urutan' => 1,
            ],
            [
                'kode' => 'SMP',
                'nama' => 'SMP Nusantara',
                'npsn' => '20202202',
                'tingkat_awal' => 7,
                'tingkat_akhir' => 9,
                'punya_jurusan' => false,
                'kepala_sekolah' => 'Drs. H. Dedi Kusuma, M.Pd.',
                'nip_kepala_sekolah' => '19720815 199802 1 002',
                'alamat' => 'Jl. Pendidikan No. 10, Blok B, Kota Bandung',
                'telepon' => '(022) 7011-002',
                'email' => 'smp@sekolah.com',
                'is_active' => true,
                'urutan' => 2,
            ],
            [
                'kode' => 'SMA',
                'nama' => 'SMA Nusantara',
                'npsn' => '20203303',
                'tingkat_awal' => 10,
                'tingkat_akhir' => 12,
                'punya_jurusan' => true,
                'kepala_sekolah' => 'Dr. H. Ahmad Daris, M.Pd.',
                'nip_kepala_sekolah' => '19700512 199501 1 003',
                'alamat' => 'Jl. Pendidikan No. 10, Blok C, Kota Bandung',
                'telepon' => '(022) 7011-003',
                'email' => 'sma@sekolah.com',
                'is_active' => true,
                'urutan' => 3,
            ],
            [
                'kode' => 'SMK',
                'nama' => 'SMK Nusantara',
                'npsn' => '20204404',
                'tingkat_awal' => 10,
                'tingkat_akhir' => 12,
                'punya_jurusan' => true,
                'kepala_sekolah' => 'Ir. Hendra Wijaya, M.T.',
                'nip_kepala_sekolah' => '19740920 199903 1 004',
                'alamat' => 'Jl. Pendidikan No. 10, Blok D, Kota Bandung',
                'telepon' => '(022) 7011-004',
                'email' => 'smk@sekolah.com',
                'is_active' => true,
                'urutan' => 4,
            ],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(['kode' => $unit['kode']], $unit);
        }
    }
}
