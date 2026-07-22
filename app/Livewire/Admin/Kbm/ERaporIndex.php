<?php

namespace App\Livewire\Admin\Kbm;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Models\Kelas;
use App\Models\Rapor;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Absensi;
use Carbon\Carbon;

#[Layout('layouts.admin')]
#[Title('e-Rapor')]
class ERaporIndex extends Component
{
    #[Url]
    public $filterTahunAjaran;
    
    #[Url]
    public $filterKelas;

    // Form Modal State
    public $editSiswaId;
    public $formCatatan = '';
    public $formSakit = 0;
    public $formIzin = 0;
    public $formAlpa = 0;
    public $formKeputusan = '';
    
    // JSON arrays
    public $formEkskul = [];
    public $formPrestasi = [];
    public $formKarakter = [];

    public function mount()
    {
        $ta = TahunAjaran::where('is_active', true)->first();
        if ($ta && !$this->filterTahunAjaran) {
            $this->filterTahunAjaran = $ta->id;
        }

        // Jika Guru (Wali Kelas)
        if(auth()->user()->hasRole('guru') && auth()->user()->guru) {
            $kelasWali = Kelas::where('wali_kelas_id', auth()->user()->guru->id)->first();
            if ($kelasWali && !$this->filterKelas) {
                $this->filterKelas = $kelasWali->id;
            }
        }
    }

    // Modal Ekstrakurikuler dll
    public function openEditModal($siswaId)
    {
        if (!$this->filterTahunAjaran || !$this->filterKelas) return;

        $this->editSiswaId = $siswaId;
        $rapor = Rapor::firstOrCreate([
            'siswa_id' => $siswaId,
            'tahun_ajaran_id' => $this->filterTahunAjaran,
            'kelas_id' => $this->filterKelas,
        ]);

        $this->formCatatan = $rapor->catatan_wali_kelas ?? '';
        $this->formKeputusan = $rapor->keputusan ?? '';
        
        $this->formEkskul = $rapor->ekskul ?? [];
        $this->formPrestasi = $rapor->prestasi ?? [];
        $this->formKarakter = $rapor->karakter ?? [];

        // Auto-Pull Absensi: Jika belum ada data tersimpan (masih 0), tarik otomatis
        // Atau jika user ingin selalu yang terbaru, kita tarik saja lalu biarkan user override
        if ($rapor->total_sakit == 0 && $rapor->total_izin == 0 && $rapor->total_alpa == 0) {
            $this->syncAttendance();
        } else {
            $this->formSakit = $rapor->total_sakit ?? 0;
            $this->formIzin = $rapor->total_izin ?? 0;
            $this->formAlpa = $rapor->total_alpa ?? 0;
        }

        $this->dispatch('open-modal', 'edit-rapor-modal');
    }

    public function syncAttendance()
    {
        if (!$this->editSiswaId || !$this->filterTahunAjaran) return;

        $range = $this->getSemesterDateRange($this->filterTahunAjaran);
        if (!$range[0] || !$range[1]) return;

        $absensiCount = Absensi::where('siswa_id', $this->editSiswaId)
            ->whereBetween('tanggal', [$range[0], $range[1]])
            ->selectRaw("
                SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'alpa' THEN 1 ELSE 0 END) as alpa
            ")
            ->first();

        $this->formSakit = $absensiCount->sakit ?? 0;
        $this->formIzin = $absensiCount->izin ?? 0;
        $this->formAlpa = $absensiCount->alpa ?? 0;
        
        if ($this->editSiswaId) {
             $this->dispatch('notify', title: 'Sinkronisasi', message: 'Data absensi harian berhasil ditarik.', type: 'info');
        }
    }

    private function getSemesterDateRange($taId)
    {
        $ta = TahunAjaran::find($taId);
        if (!$ta) return [null, null];

        // Format tahun: "2025/2026" atau "2025"
        $parts = explode('/', $ta->tahun);
        $tahunGanjil = $parts[0];
        $tahunGenap = $parts[1] ?? ($parts[0] + 1);

        if (str_contains(strtolower($ta->semester), 'ganjil') || $ta->semester == '1') {
            return [
                Carbon::create($tahunGanjil, 7, 1)->startOfDay(),
                Carbon::create($tahunGanjil, 12, 31)->endOfDay()
            ];
        } else {
            return [
                Carbon::create($tahunGenap, 1, 1)->startOfDay(),
                Carbon::create($tahunGenap, 6, 30)->endOfDay()
            ];
        }
    }

    public function addEkskul() { $this->formEkskul[] = ['nama' => '', 'predikat' => '', 'keterangan' => '']; }
    public function addPrestasi() { $this->formPrestasi[] = ['jenis' => '', 'keterangan' => '']; }
    public function removeEkskul($index) { unset($this->formEkskul[$index]); $this->formEkskul = array_values($this->formEkskul); }
    public function removePrestasi($index) { unset($this->formPrestasi[$index]); $this->formPrestasi = array_values($this->formPrestasi); }

    public function simpanRapor()
    {
        $rapor = Rapor::where([
            'siswa_id' => $this->editSiswaId,
            'tahun_ajaran_id' => $this->filterTahunAjaran,
            'kelas_id' => $this->filterKelas,
        ])->first();

        if ($rapor) {
            $rapor->update([
                'catatan_wali_kelas' => $this->formCatatan,
                'total_sakit' => $this->formSakit ?: 0,
                'total_izin' => $this->formIzin ?: 0,
                'total_alpa' => $this->formAlpa ?: 0,
                'keputusan' => $this->formKeputusan,
                'ekskul' => array_filter($this->formEkskul, fn($i) => !empty($i['nama'])),
                'prestasi' => array_filter($this->formPrestasi, fn($i) => !empty($i['jenis'])),
                'karakter' => $this->formKarakter,
            ]);

            $this->dispatch('close-modal', 'edit-rapor-modal');
            $this->dispatch('notify', title: 'Tersimpan', message: 'Data pendamping Rapor telah disimpan.', type: 'success');
        }
    }

    public function toggleLock($siswaId)
    {
        $rapor = Rapor::where([
            'siswa_id' => $siswaId,
            'tahun_ajaran_id' => $this->filterTahunAjaran,
        ])->first();

        if ($rapor) {
            $rapor->update(['is_locked' => !$rapor->is_locked]);
            $msg = $rapor->is_locked ? 'Rapor berhasil dikunci.' : 'Kunci Rapor telah dibuka.';
            $this->dispatch('notify', title: 'Status Berubah', message: $msg, type: 'success');
        }
    }

    public function render()
    {
        $user = auth()->user();
        
        // Authorization Logic
        if ($user->hasRole('guru') && $user->guru) {
            $listKelas = Kelas::where('wali_kelas_id', $user->guru->id)->get();
        } else {
            $listKelas = Kelas::orderBy('jenjang')->orderBy('nama_kelas')->get();
        }
        
        $listTahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->orderBy('semester')->get();

        $siswas = [];
        $raporsMap = collect();
        $rankings = [];

        if ($this->filterKelas) {
            // Eager load nilais to avoid N+1 inside Rapor getRataRataNilaiAttribute
            $taId = $this->filterTahunAjaran;
            $siswas = Siswa::with(['user', 'nilais' => function($q) use ($taId) {
                $q->where('tahun_ajaran_id', $taId);
            }])->where('kelas_id', $this->filterKelas)
                ->get()
                ->sortBy('user.name')
                ->values();

            // Setup empty rapors if they don't exist logic handled below if needed
            $rapors = Rapor::where('kelas_id', $this->filterKelas)
                           ->where('tahun_ajaran_id', $this->filterTahunAjaran)
                           ->whereIn('siswa_id', $siswas->pluck('id'))
                           ->get()
                           ->keyBy('siswa_id');

            // Caching average and mapping
            $rRatas = [];
            foreach ($rapors as $siswaId => $rp) {
                $s = $siswas->firstWhere('id', $siswaId);
                if ($s) {
                    $rp->setRelation('siswa', $s);
                    $rRatas[$siswaId] = $rp->rata_rata_nilai;
                }
            }
            $raporsMap = $rapors;

            // Kalkulasi Ranking
            arsort($rRatas);
            $r = 1;
            foreach ($rRatas as $sId => $avg) {
                if ($avg > 0) {
                    $rankings[$sId] = $r++;
                } else {
                    $rankings[$sId] = '-';
                }
            }
        }

        return view('livewire.admin.kbm.e-rapor-index', compact('listKelas', 'listTahunAjaran', 'siswas', 'raporsMap', 'rankings'));
    }
}
