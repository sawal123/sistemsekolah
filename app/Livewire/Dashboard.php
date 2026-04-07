<?php

namespace App\Livewire;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard', [
            'totalSiswa' => Siswa::where('status', 'Aktif')->count(),
            'totalGuru' => Guru::count(),
            'totalKelas' => Kelas::count(),
            'userName' => auth()->user()->name ?? 'Admin',
        ]);
    }
}
