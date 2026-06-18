<?php

namespace App\Livewire;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\PendaftaranMuridBaru;
use App\Models\PpdbGelombang;
use App\Models\Siswa;
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
        $visitChart = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->translatedFormat('D'),
                'date' => $date->format('d/m'),
                'total' => WebsiteVisit::whereDate('visited_at', $date->toDateString())->count(),
                'danger' => WebsiteVisit::whereDate('visited_at', $date->toDateString())->where('is_suspicious', true)->count(),
            ];
        });

        $maxVisit = max($visitChart->max('total') ?: 1, 1);
        $today = now()->toDateString();

        return view('livewire.dashboard', [
            'totalSiswa' => Siswa::where('status', 'Aktif')->count(),
            'totalGuru' => Guru::count(),
            'totalKelas' => Kelas::count(),
            'userName' => auth()->user()->name ?? 'Admin',
            'visitToday' => WebsiteVisit::whereDate('visited_at', $today)->count(),
            'uniqueVisitToday' => WebsiteVisit::whereDate('visited_at', $today)->distinct('ip_address')->count('ip_address'),
            'dangerToday' => WebsiteVisit::whereDate('visited_at', $today)->where('is_suspicious', true)->count(),
            'visitChart' => $visitChart,
            'maxVisit' => $maxVisit,
            'dangerLogs' => WebsiteVisit::suspicious()->latest('visited_at')->limit(5)->get(),
            'ppdbOpen' => PpdbGelombang::where('status', 'Dibuka')->count(),
            'ppdbNew' => PendaftaranMuridBaru::where('status', 'Baru')->count(),
            'ppdbNeedReview' => PendaftaranMuridBaru::whereIn('status', ['Baru', 'Diperiksa'])->count(),
        ]);
    }
}
