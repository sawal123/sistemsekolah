<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'app_name' => 'Sistem Informasi SMA Nusantara',
            'school_name' => 'SMA Nusantara',
            'site_tagline' => 'Membina prestasi, menguatkan karakter, dan menyiapkan masa depan.',
            'seo_description' => 'Website resmi SMA Nusantara untuk informasi sekolah, akademik, PPDB, dan layanan publik.',
            'school_address' => 'Jl. Pendidikan Nusantara No. 45, Kota Bandung, Jawa Barat 40123',
            'school_phone' => '(022) 720 2045',
            'school_email' => 'info@smanusantara.sch.id',
            'school_website' => 'https://smanusantara.sch.id',
            'whatsapp' => '6281223456789',
            'instagram_url' => 'https://instagram.com/smanusantara',
            'youtube_url' => 'https://youtube.com/@smanusantara',
            'linkedin_url' => 'https://linkedin.com/school/sma-nusantara',
            'app_logo' => 'seed-images/logo-sekolah.svg',
            'app_icon' => 'seed-images/logo-sekolah.svg',
            'principal_name' => 'Dr. Rina Wulandari, M.Pd.',
            'principal_title' => 'Kepala SMA Nusantara',
            'principal_nip' => '197905122005012001',
            'principal_image' => 'seed-images/principal.jpg',
            'principal_greeting' => "Selamat datang di SMA Nusantara. Kami menempatkan keselamatan, karakter, dan rasa ingin tahu sebagai dasar setiap pengalaman belajar.\n\nBersama keluarga dan masyarakat, kami mendampingi peserta didik untuk tumbuh menjadi pembelajar yang berintegritas, kreatif, dan siap berkontribusi.",
            'school_vision' => 'Menjadi sekolah pembelajar yang unggul, berkarakter, berwawasan lingkungan, dan berdaya saing global.',
            'school_mission' => "Menyelenggarakan pembelajaran yang aktif, inklusif, dan bermakna.\nMenguatkan karakter, literasi, numerasi, serta kepemimpinan peserta didik.\nMengembangkan talenta sains, teknologi, seni, dan olahraga.\nMembangun kemitraan positif dengan orang tua, alumni, dan masyarakat.",
            'alumni_count' => '1250',
            'show_signature_on_print' => '1',
            'admin_signature_name' => 'Siti Rahmawati, S.E.',
            'admin_signature_role' => 'Kepala Tata Usaha',
            'organization_structure_image' => null,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
