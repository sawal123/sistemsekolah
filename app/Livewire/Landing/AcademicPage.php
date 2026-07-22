<?php

namespace App\Livewire\Landing;

use App\Models\Jadwal;
use App\Models\KalenderAkademik;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.landing')]
class AcademicPage extends Component
{
    public string $program = 'kurikulum';

    public function mount(string $program = 'kurikulum'): void
    {
        abort_unless(in_array($program, ['ipa', 'ips', 'bahasa', 'kurikulum'], true), 404);
        $this->program = $program;
    }

    public function render()
    {
        $programs = [
            'ipa' => [
                'label' => 'Program IPA',
                'subtitle' => 'Sains, matematika, riset, dan pemecahan masalah.',
                'keywords' => ['fisika', 'kimia', 'biologi', 'matematika', 'ipa'],
            ],
            'ips' => [
                'label' => 'Program IPS',
                'subtitle' => 'Sosial, ekonomi, geografi, sejarah, dan masyarakat.',
                'keywords' => ['ekonomi', 'geografi', 'sejarah', 'sosiologi', 'ips'],
            ],
            'bahasa' => [
                'label' => 'Program Bahasa',
                'subtitle' => 'Literasi, komunikasi, bahasa, dan kebudayaan.',
                'keywords' => ['bahasa', 'sastra', 'inggris', 'indonesia', 'jepang', 'arab'],
            ],
            'kurikulum' => [
                'label' => 'Kurikulum',
                'subtitle' => 'Struktur pembelajaran dan mata pelajaran sekolah.',
                'keywords' => [],
            ],
        ];

        $meta = $programs[$this->program];
        $mapelQuery = Mapel::query()->whereIn('jenjang', ['SMA', 'Umum']);

        if ($this->program !== 'kurikulum') {
            $keywords = $meta['keywords'];
            $mapelQuery->where(function (Builder $query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('nama_mapel', 'like', "%{$keyword}%")
                        ->orWhere('kode_mapel', 'like', "%{$keyword}%");
                }
            });
        }

        $mapels = $mapelQuery->orderBy('kelompok')->orderBy('nama_mapel')->get();

        return view('livewire.landing.academic-page', [
            'meta' => $meta,
            'programs' => $programs,
            'mapels' => $mapels,
            'allMapels' => Mapel::whereIn('jenjang', ['SMA', 'Umum'])->orderBy('kelompok')->orderBy('nama_mapel')->get(),
            'classes' => Kelas::with(['wali_kelas.user'])->withCount('siswas')->where('jenjang', 'SMA')->orderBy('nama_kelas')->get(),
            'activeYear' => TahunAjaran::where('is_active', true)->first(),
            'scheduleCount' => Jadwal::whereHas('kelas', fn ($query) => $query->where('jenjang', 'SMA'))->count(),
            'events' => KalenderAkademik::where('tanggal', '>=', today())->orderBy('tanggal')->take(6)->get(),
        ]);
    }
}
