<?php

namespace App\Livewire\Student;

use App\Models\Absensi;
use App\Models\KalenderAkademik;
use App\Models\PaymentSubmission;
use App\Models\Tagihan;
use App\Services\StudentPortalService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.student')]
#[Title('Beranda Siswa')]
class StudentDashboard extends Component
{
    public function render(StudentPortalService $portal)
    {
        $siswa = auth()->user()->siswa;
        $tahunAjaran = $portal->activeAcademicYear();
        $rombel = $portal->activeRombel($siswa, $tahunAjaran);
        $jadwal = $portal->schedules($rombel?->kelas_id, $tahunAjaran?->id);
        $hariIni = now()->locale('id')->dayName;
        $tagihans = Tagihan::where('siswa_id', $siswa->id)
            ->whereNotIn('status', ['Lunas', 'Dibatalkan'])
            ->withSum('pembayarans', 'nominal')
            ->orderByRaw('jatuh_tempo IS NULL, jatuh_tempo')
            ->get();
        $lastSubmission = PaymentSubmission::where('siswa_id', $siswa->id)->latest()->first();
        $attendance = Absensi::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', [now()->startOfMonth(), now()->endOfMonth()])
            ->selectRaw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir, SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin, SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit, SUM(CASE WHEN status = 'alpa' THEN 1 ELSE 0 END) as alpa")
            ->first();

        return view('livewire.student.student-dashboard', [
            'siswa' => $siswa,
            'tahunAjaran' => $tahunAjaran,
            'rombel' => $rombel,
            'jadwalHariIni' => $jadwal->where('hari', $hariIni),
            'tagihans' => $tagihans,
            'totalTagihan' => $tagihans->sum('sisa_tagihan'),
            'lastSubmission' => $lastSubmission,
            'attendance' => $attendance,
            'agenda' => KalenderAkademik::where('tanggal', '>=', today())->orderBy('tanggal')->limit(3)->get(),
        ]);
    }
}
