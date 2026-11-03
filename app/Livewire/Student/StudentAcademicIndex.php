<?php

namespace App\Livewire\Student;

use App\Models\Absensi;
use App\Services\StudentPortalService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.student')]
#[Title('Akademik Saya')]
class StudentAcademicIndex extends Component
{
    public function render(StudentPortalService $portal)
    {
        $siswa = auth()->user()->siswa;
        $tahunAjaran = $portal->activeAcademicYear();
        $rombel = $portal->activeRombel($siswa, $tahunAjaran);
        $jadwal = $portal->schedules($rombel?->kelas_id, $tahunAjaran?->id);
        $attendance = Absensi::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir, SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin, SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit, SUM(CASE WHEN status = 'alpa' THEN 1 ELSE 0 END) as alpa")
            ->first();

        return view('livewire.student.student-academic-index', compact('siswa', 'tahunAjaran', 'rombel', 'jadwal', 'attendance'));
    }
}
