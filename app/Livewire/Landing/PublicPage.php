<?php

namespace App\Livewire\Landing;

use App\Models\Gallery;
use App\Models\KalenderAkademik;
use App\Models\KegiatanAlumni;
use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.landing')]
class PublicPage extends Component
{
    use WithPagination;

    public const PAGES = [
        'pengumuman' => ['Pusat Pengumuman', 'Informasi dan edaran resmi yang diterbitkan sekolah.'],
        'kalender-akademik' => ['Kalender Akademik', 'Agenda kegiatan dan hari libur sekolah.'],
        'prestasi' => ['Prestasi Sekolah', 'Capaian akademik dan non-akademik siswa yang dipublikasikan sekolah.'],
        'galeri' => ['Galeri Kegiatan', 'Dokumentasi pembelajaran dan kegiatan warga sekolah.'],
        'alumni' => ['Jejaring Alumni', 'Perjalanan pendidikan, karier, dan usaha alumni sekolah.'],
        'kebijakan-privasi' => ['Kebijakan Privasi', 'Informasi penggunaan data pada layanan website sekolah.'],
        'standar-pelayanan' => ['Standar Pelayanan', 'Panduan mengakses layanan informasi dan administrasi sekolah.'],
        'peta-situs' => ['Peta Situs', 'Temukan seluruh halaman dan layanan publik sekolah.'],
    ];

    #[Locked]
    public string $page = '';

    public function mount(string $page): void
    {
        abort_unless(array_key_exists($page, self::PAGES), 404);
        $this->page = $page;
    }

    public function render()
    {
        $data = match ($this->page) {
            'pengumuman', 'prestasi' => [
                'posts' => Post::with('kategori')
                    ->where('status', 'Published')
                    ->whereHas('kategori', fn ($query) => $query->where(function ($category) {
                        $category->where('slug', $this->page)
                            ->orWhere('slug', 'like', $this->page.'-%')
                            ->orWhere('nama_kategori', 'like', '%'.$this->page.'%');
                    }))
                    ->latest()->paginate(9),
            ],
            'kalender-akademik' => [
                'events' => KalenderAkademik::where('tanggal', '>=', today()->startOfMonth())
                    ->orderBy('tanggal')->paginate(15),
            ],
            'galeri' => ['photos' => Gallery::latest()->paginate(12)],
            'alumni' => [
                'alumni' => KegiatanAlumni::with('siswa.user')
                    ->where('status_verifikasi', 'Verified')
                    ->where('is_public', true)
                    ->whereHas('siswa.user')
                    ->latest()->paginate(12),
            ],
            default => [],
        };

        return view('livewire.landing.public-page', $data + [
            'heading' => self::PAGES[$this->page][0],
            'description' => self::PAGES[$this->page][1],
        ])->title(self::PAGES[$this->page][0]);
    }
}
