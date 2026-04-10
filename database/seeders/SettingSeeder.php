<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'app_name' => 'Sistem Sekolah Antigravity',
            'school_name' => 'SMA NEGERI 1 ANTIGRAVITY',
            'school_address' => 'Jl. Pendidikan No. 45, Kota Masa Depan, 12345',
            'school_phone' => '(021) 1234567',
            'school_website' => 'www.sekolahanterah.sch.id',
            'app_logo' => 'branding/logo.png',
            'app_icon' => 'branding/logo.png',
            'show_signature_on_print' => '1',
            'admin_signature_name' => 'Admin Sekolah',
            'admin_signature_role' => 'Kepala Tata Usaha',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
