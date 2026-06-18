<?php

namespace App\Livewire\Landing;

use App\Models\Gallery;
use App\Models\Guru;
use App\Models\KegiatanAlumni;
use App\Models\Setting;
use App\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.landing')]
#[Title('Tentang Sekolah')]
class AboutPage extends Component
{
    public function render()
    {
        return view('livewire.landing.about-page', [
            'settings' => Setting::pluck('value', 'key'),
            'galleries' => Gallery::latest()->take(6)->get(),
            'stats' => [
                'students' => Siswa::where('status', 'Aktif')->count(),
                'teachers' => Guru::count(),
                'alumni' => KegiatanAlumni::where('status_verifikasi', 'Verified')->count(),
            ],
        ]);
    }
}
