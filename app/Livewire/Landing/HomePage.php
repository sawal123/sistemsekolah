<?php

namespace App\Livewire\Landing;

use App\Models\Fasilitas;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\KegiatanAlumni;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\Slider;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.landing')]
#[Title('Beranda')]
class HomePage extends Component
{
    public function render()
    {
        $settings = Setting::pluck('value', 'key');
        $sliders = Slider::where('is_active', true)->orderBy('urutan')->get();

        return view('livewire.landing.home-page', [
            'settings' => $settings,
            'heroSlides' => $this->heroSlides($sliders, $settings),
            'facilities' => Fasilitas::latest()->take(6)->get(),
            'posts' => Post::with(['kategori', 'user'])
                ->where('status', 'Published')
                ->latest()
                ->take(3)
                ->get(),
            'stats' => [
                'students' => $this->formatNumber(Siswa::where('status', 'Aktif')->count(), '1.250+'),
                'teachers' => $this->formatNumber(Guru::count(), '85'),
                'programs' => $this->formatNumber(Kelas::count(), '3'),
                'alumni' => $this->formatNumber((int) ($settings->get('alumni_count') ?: KegiatanAlumni::count()), '7.800+'),
            ],
        ]);
    }

    private function heroSlides(Collection $sliders, Collection $settings): array
    {
        if ($sliders->isNotEmpty()) {
            return $sliders->map(fn ($slider) => [
                'badge' => 'Informasi Sekolah',
                'title' => $slider->judul,
                'description' => $slider->deskripsi ?: ($settings->get('site_tagline') ?: 'Informasi terbaru dari sekolah kami.'),
                'image' => $slider->foto ? asset('storage/' . $slider->foto) : null,
                'primary_label' => 'Pendaftaran Siswa Baru',
                'primary_url' => route('landing.ppdb.section', 'daftar') . '#daftar',
                'secondary_label' => 'Profil Sekolah',
                'secondary_url' => route('landing.about'),
            ])->values()->all();
        }

        $schoolName = $settings->get('school_name') ?: 'SMA Negeri 1 Cendekia';

        return [
            [
                'badge' => 'Tahun Ajaran 2026/2027',
                'title' => 'Membentuk Generasi Cerdas, Berkarakter, dan Siap Bersaing',
                'description' => $schoolName . ' hadir mendampingi setiap siswa menemukan potensi terbaiknya melalui pendidikan berkualitas.',
                'image' => null,
                'primary_label' => 'Pendaftaran Siswa Baru',
                'primary_url' => route('landing.ppdb.section', 'daftar') . '#daftar',
                'secondary_label' => 'Profil Sekolah',
                'secondary_url' => route('landing.about'),
            ],
            [
                'badge' => 'Prestasi Sekolah',
                'title' => 'Lingkungan Belajar yang Mendorong Prestasi dan Karakter',
                'description' => 'Kegiatan akademik dan non-akademik dirancang untuk menumbuhkan percaya diri, disiplin, dan kemampuan berpikir kritis.',
                'image' => null,
                'primary_label' => 'Lihat Berita',
                'primary_url' => route('landing.blog'),
                'secondary_label' => null,
                'secondary_url' => null,
            ],
            [
                'badge' => 'Fasilitas Modern',
                'title' => 'Belajar Nyaman dengan Fasilitas Lengkap dan Terkini',
                'description' => 'Ruang belajar, laboratorium, perpustakaan, dan fasilitas pendukung disiapkan untuk pengalaman belajar yang lebih baik.',
                'image' => null,
                'primary_label' => 'Jelajahi Fasilitas',
                'primary_url' => route('landing.facilities'),
                'secondary_label' => null,
                'secondary_url' => null,
            ],
        ];
    }

    private function formatNumber(int $value, string $fallback): string
    {
        if ($value <= 0) {
            return $fallback;
        }

        return $value >= 1000 ? number_format($value, 0, ',', '.') . '+' : (string) $value;
    }
}
