<?php

namespace App\Livewire;

use App\Models\Absensi;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\KalenderAkademik;
use App\Models\Kelas;
use App\Models\PendaftaranMuridBaru;
use App\Models\PpdbGelombang;
use App\Models\Rapor;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\WebsiteVisit;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $isGuru = $user && $user->hasRole('guru') && ! $user->hasRole('admin');

        if ($isGuru) {
            $taAktif = TahunAjaran::where('is_active', true)->first();
            $guru = $user->guru;

            $rombelWali = null;
            $kelasWali = null;
            $totalSiswaWali = 0;
            $rekapAbsen = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0, 'belum' => 0];
            $raporStats = (object) ['total' => 0, 'locked' => 0];

            if ($guru && $taAktif) {
                $rombelWali = Rombel::with('kelas')
                    ->where('wali_kelas_id', $guru->id)
                    ->where('tahun_ajaran_id', $taAktif->id)
                    ->first();

                if ($rombelWali?->kelas) {
                    $kelasWali = $rombelWali->kelas;
                    $totalSiswaWali = Siswa::aktifDiKelasPadaTahunAjaran($kelasWali->id, $taAktif->id)->count();

                    $siswaWaliIds = Siswa::aktifDiKelasPadaTahunAjaran($kelasWali->id, $taAktif->id)->pluck('id');
                    $absenHariIni = Absensi::whereIn('siswa_id', $siswaWaliIds)
                        ->where('tanggal', now()->toDateString())
                        ->get();

                    $rekapAbsen = [
                        'hadir' => $absenHariIni->where('status', 'hadir')->count(),
                        'sakit' => $absenHariIni->where('status', 'sakit')->count(),
                        'izin' => $absenHariIni->where('status', 'izin')->count(),
                        'alpa' => $absenHariIni->where('status', 'alpa')->count(),
                        'belum' => max(0, $totalSiswaWali - $absenHariIni->count()),
                    ];

                    $raporStats = Rapor::where('kelas_id', $kelasWali->id)
                        ->where('tahun_ajaran_id', $taAktif->id)
                        ->whereIn('siswa_id', $siswaWaliIds)
                        ->selectRaw('COUNT(*) as total, SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as locked')
                        ->first() ?? (object) ['total' => 0, 'locked' => 0];
                }
            }

            $totalKelasDiajar = 0;
            $totalMapelDiajar = 0;
            $totalSesiMinggu = 0;
            $jadwalHariIni = collect();

            $hariMap = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
            $hariIni = $hariMap[now()->dayOfWeekIso] ?? 'Senin';

            if ($guru && $taAktif) {
                $jadwalBase = Jadwal::where('guru_id', $guru->id)
                    ->where('tahun_ajaran_id', $taAktif->id);

                $totalKelasDiajar = (clone $jadwalBase)->distinct('kelas_id')->count('kelas_id');
                $totalMapelDiajar = (clone $jadwalBase)->distinct('mapel_id')->count('mapel_id');
                $totalSesiMinggu = (clone $jadwalBase)->count();

                $jadwalHariIni = (clone $jadwalBase)
                    ->with(['kelas', 'mapel', 'ruangan'])
                    ->where('hari', $hariIni)
                    ->orderBy('jam_mulai')
                    ->get();
            }

            $agendaTerdekat = KalenderAkademik::where('tanggal', '>=', now()->toDateString())
                ->orderBy('tanggal')
                ->limit(4)
                ->get();

            return view('livewire.dashboard', [
                'isGuru' => true,
                'guru' => $guru,
                'taAktif' => $taAktif,
                'rombelWali' => $rombelWali,
                'kelasWali' => $kelasWali,
                'totalSiswaWali' => $totalSiswaWali,
                'rekapAbsen' => $rekapAbsen,
                'raporStats' => $raporStats,
                'totalKelasDiajar' => $totalKelasDiajar,
                'totalMapelDiajar' => $totalMapelDiajar,
                'totalSesiMinggu' => $totalSesiMinggu,
                'hariIni' => $hariIni,
                'jadwalHariIni' => $jadwalHariIni,
                'agendaTerdekat' => $agendaTerdekat,
            ]);
        }

        // Admin Dashboard
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $dailyStats = WebsiteVisit::whereBetween('visited_at', [$startDate, $endDate])
            ->selectRaw('DATE(visited_at) as visit_date, COUNT(*) as total, SUM(CASE WHEN is_suspicious = 1 THEN 1 ELSE 0 END) as danger')
            ->groupByRaw('DATE(visited_at)')
            ->get()
            ->keyBy('visit_date');

        $visitChart = collect(range(6, 0))->map(function ($daysAgo) use ($dailyStats) {
            $date = now()->subDays($daysAgo);
            $dateKey = $date->toDateString();
            $stat = $dailyStats->get($dateKey);

            return [
                'label' => $date->translatedFormat('D'),
                'date' => $date->format('d/m'),
                'total' => (int) ($stat->total ?? 0),
                'danger' => (int) ($stat->danger ?? 0),
            ];
        });

        $maxVisit = max($visitChart->max('total') ?: 1, 1);
        $todayKey = now()->toDateString();
        $todayStat = $dailyStats->get($todayKey);
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        return view('livewire.dashboard', [
            'isGuru' => false,
            'totalSiswa' => Siswa::where('status', 'Aktif')->count(),
            'totalGuru' => Guru::count(),
            'totalKelas' => Kelas::count(),
            'userName' => auth()->user()->name ?? 'Admin',
            'visitToday' => (int) ($todayStat->total ?? 0),
            'uniqueVisitToday' => WebsiteVisit::whereBetween('visited_at', [$todayStart, $todayEnd])->distinct('ip_address')->count('ip_address'),
            'dangerToday' => (int) ($todayStat->danger ?? 0),
            'visitChart' => $visitChart,
            'maxVisit' => $maxVisit,
            'dangerLogs' => WebsiteVisit::suspicious()->latest('visited_at')->limit(5)->get(),
            'ppdbOpen' => PpdbGelombang::where('status', 'Dibuka')->count(),
            'ppdbNew' => PendaftaranMuridBaru::where('status', 'Baru')->count(),
            'ppdbNeedReview' => PendaftaranMuridBaru::whereIn('status', ['Baru', 'Diperiksa'])->count(),
        ]);
    }
}
