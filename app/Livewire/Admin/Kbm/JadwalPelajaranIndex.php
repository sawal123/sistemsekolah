<?php

namespace App\Livewire\Admin\Kbm;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Ruangan;
use App\Models\Setting;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Jadwal Pelajaran')]
class JadwalPelajaranIndex extends Component
{
    use WithPagination;

    // Filter Global
    public $filterTahunAjaran = '';

    public $filterKelas = '';

    public $filterGuru = '';

    public $filterRuangan = '';

    public $activeTab = 'grid'; // grid | list

    // Modal Form State
    public $isModalOpen = false;

    public $isEdit = false;

    public $jadwalId = null;

    public $deleteId = null;

    public $isDeleteModalOpen = false;

    // Form Inputs
    public $kelas_id;

    public $mapel_id;

    public $guru_id;

    public $ruangan_id;

    public $hari;

    public $jam_mulai;

    public $jam_selesai;

    // Auto Time Gen
    public $jamKe = '';

    // Error Bentrok
    public $conflictErrors = [];

    protected $rules = [
        'filterTahunAjaran' => 'required|exists:tahun_ajarans,id',
        'kelas_id' => 'required',
        'mapel_id' => 'required',
        'guru_id' => 'required',
        'ruangan_id' => 'nullable',
        'hari' => 'required',
        'jam_mulai' => 'required|date_format:H:i',
        'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
    ];

    public function mount()
    {
        $this->filterTahunAjaran = TahunAjaran::where('is_active', true)->value('id') ?? '';

        // Tetapkan filter otomatis berdasarkan Role Login
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->hasRole('guru')) {
                $this->filterGuru = $user->guru->id ?? '';
            } elseif ($user->hasRole('siswa')) {
                $this->filterKelas = $user->siswa?->kelasPadaTahunAjaran($this->filterTahunAjaran)?->id
                    ?? $user->siswa->kelas_id
                    ?? '';
            }
        }
    }

    public function updatedJamKe($val)
    {
        // Hitung Otomatis (Smart Time Generator)
        if ($val > 0) {
            $jadwalSettings = Setting::whereIn('key', ['durasi_jam_pelajaran', 'jam_mulai_pelajaran'])->pluck('value', 'key');
            $slotDuration = (int) ($jadwalSettings->get('durasi_jam_pelajaran') ?? 45);
            $startTime = $jadwalSettings->get('jam_mulai_pelajaran') ?? '07:15';

            $startDelay = (int) (($val - 1) * $slotDuration);

            $startObj = Carbon::createFromFormat('H:i', $startTime)->addMinutes($startDelay);
            $endObj = $startObj->copy()->addMinutes($slotDuration);

            $this->jam_mulai = $startObj->format('H:i');
            $this->jam_selesai = $endObj->format('H:i');
        }
    }

    public function calculateOverlap($query, $jam_mulai, $jam_selesai)
    {
        return $query->where(function ($q) use ($jam_mulai, $jam_selesai) {
            // Overlap condition: start < newEnd AND end > newStart
            $q->where('jam_mulai', '<', $jam_selesai)
                ->where('jam_selesai', '>', $jam_mulai);
        });
    }

    public function checkConflicts()
    {
        $this->conflictErrors = [];

        $baseQuery = Jadwal::with(['mapel', 'kelas', 'guru.user'])
            ->where('hari', $this->hari)
            ->where('tahun_ajaran_id', $this->filterTahunAjaran);
        if ($this->isEdit) {
            $baseQuery->where('id', '!=', $this->jadwalId);
        }

        // 1. Guru Overlap
        $guruOverlap = (clone $baseQuery)
            ->where('guru_id', $this->guru_id)
            ->where(function ($q) {
                $this->calculateOverlap($q, $this->jam_mulai, $this->jam_selesai);
            })->first();

        if ($guruOverlap) {
            $this->conflictErrors[] = "Guru ini sedang mengajar {$guruOverlap->mapel->nama_mapel} di kelas {$guruOverlap->kelas->nama_kelas} pada slot waktu ini.";
        }

        // 2. Kelas Overlap
        $kelasOverlap = (clone $baseQuery)
            ->where('kelas_id', $this->kelas_id)
            ->where(function ($q) {
                $this->calculateOverlap($q, $this->jam_mulai, $this->jam_selesai);
            })->first();

        if ($kelasOverlap) {
            $this->conflictErrors[] = "Kelas ini sudah ada jadwal pelajaran {$kelasOverlap->mapel->nama_mapel} bersama {$kelasOverlap->guru->user->name} pada slot waktu ini.";
        }

        // 3. Ruangan Overlap
        if ($this->ruangan_id) {
            $ruanganOverlap = (clone $baseQuery)
                ->where('ruangan_id', $this->ruangan_id)
                ->where(function ($q) {
                    $this->calculateOverlap($q, $this->jam_mulai, $this->jam_selesai);
                })->first();

            if ($ruanganOverlap) {
                $this->conflictErrors[] = "Ruangan ini sedang digunakan oleh kelas {$ruanganOverlap->kelas->nama_kelas} untuk pelajaran {$ruanganOverlap->mapel->nama_mapel} pada waktu tersebut.";
            }
        }

        return count($this->conflictErrors) === 0;
    }

    public function simpanJadwal()
    {
        abort_unless(auth()->user()->hasRole('admin') || auth()->user()->hasRole('operator'), 403, 'Hanya Administrator atau Operator yang dapat mengubah jadwal pelajaran.');

        $this->validate();

        if (! $this->checkConflicts()) {
            return;
        }

        Jadwal::updateOrCreate(
            ['id' => $this->jadwalId],
            [
                'kelas_id' => $this->kelas_id,
                'tahun_ajaran_id' => $this->filterTahunAjaran,
                'mapel_id' => $this->mapel_id,
                'guru_id' => $this->guru_id,
                'ruangan_id' => $this->ruangan_id ?: null,
                'hari' => $this->hari,
                'jam_mulai' => $this->jam_mulai,
                'jam_selesai' => $this->jam_selesai,
            ]
        );

        $this->dispatch('notify', title: 'Berhasil!', message: 'Jadwal '.($this->isEdit ? 'diperbarui' : 'disimpan').' tanpa bentrok.', type: 'success');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->reset(['isEdit', 'jadwalId', 'kelas_id', 'mapel_id', 'guru_id', 'ruangan_id', 'hari', 'jam_mulai', 'jam_selesai', 'jamKe', 'conflictErrors']);
        $this->resetValidation();
        $this->dispatch('close-modal', 'jadwal-modal');
    }

    public function openModalForCreate()
    {
        abort_unless(auth()->user()->hasRole('admin') || auth()->user()->hasRole('operator'), 403, 'Akses ditolak.');
        $this->closeModal();
        $this->dispatch('open-modal', 'jadwal-modal');
    }

    public function confirmHapus($id)
    {
        abort_unless(auth()->user()->hasRole('admin') || auth()->user()->hasRole('operator'), 403, 'Akses ditolak.');
        $this->deleteId = $id;
        $this->dispatch('open-modal', 'delete-modal');
    }

    public function executeHapus()
    {
        abort_unless(auth()->user()->hasRole('admin') || auth()->user()->hasRole('operator'), 403, 'Akses ditolak.');
        if ($this->deleteId) {
            Jadwal::destroy($this->deleteId);
            $this->dispatch('notify', title: 'Dihapus', message: 'Data pelajaran dihapus.', type: 'info');
        }
        $this->dispatch('close-modal', 'delete-modal');
        $this->deleteId = null;
    }

    public function render()
    {
        $user = auth()->user();
        if ($user && $user->hasRole('guru') && ! $user->hasRole('admin')) {
            $this->filterGuru = $user->guru?->id ?? '';
        }

        $query = Jadwal::with(['kelas', 'mapel', 'guru.user', 'ruangan'])
            ->when($this->filterTahunAjaran, fn ($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaran));

        if ($this->filterKelas) {
            $query->where('kelas_id', $this->filterKelas);
        }
        if ($this->filterGuru) {
            $query->where('guru_id', $this->filterGuru);
        }
        if ($this->filterRuangan) {
            $query->where('ruangan_id', $this->filterRuangan);
        }

        $jadwalData = $query->orderBy('jam_mulai')->get();

        // Mapping For Grid 6 Days
        $hariMinggu = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $matrix = [];
        foreach ($hariMinggu as $hari) {
            $matrix[$hari] = $jadwalData->where('hari', $hari)->values();
        }

        $kelases = Kelas::orderBy('jenjang')->orderBy('nama_kelas')->get();
        $mapels = Mapel::orderBy('nama_mapel')->get();
        $gurus = Guru::with('user')->get()->sortBy('user.name');
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();

        // 1. GENERATE SUGGESTED SLOTS (1 query preload di luar loop 8 jam)
        $jadwalSettings = Setting::whereIn('key', ['durasi_jam_pelajaran', 'jam_mulai_pelajaran'])->pluck('value', 'key');
        $slotDuration = (int) ($jadwalSettings->get('durasi_jam_pelajaran') ?? 45);
        $startTimeBase = $jadwalSettings->get('jam_mulai_pelajaran') ?? '07:15';

        $kelasHariJadwals = collect();
        if ($this->kelas_id && $this->hari) {
            $kelasHariJadwals = Jadwal::where('kelas_id', $this->kelas_id)
                ->where('hari', $this->hari)
                ->where('tahun_ajaran_id', $this->filterTahunAjaran)
                ->when($this->isEdit, fn ($q) => $q->where('id', '!=', $this->jadwalId))
                ->get(['jam_mulai', 'jam_selesai']);
        }

        $optSaranJam = ['' => 'Pilih Jam Ke-'];
        for ($i = 1; $i <= 8; $i++) {
            $st = Carbon::createFromFormat('H:i', $startTimeBase)->addMinutes(($i - 1) * $slotDuration)->format('H:i');
            $en = Carbon::createFromFormat('H:i', $startTimeBase)->addMinutes($i * $slotDuration)->format('H:i');

            $isTaken = $kelasHariJadwals->contains(function ($j) use ($st, $en) {
                $mulai = substr((string) $j->jam_mulai, 0, 5);
                $selesai = substr((string) $j->jam_selesai, 0, 5);
                return $mulai < $en && $selesai > $st;
            });

            if (! $isTaken) {
                $optSaranJam[$i] = "Jam ke-$i ($st - $en)";
            }
        }

        // 2. FIND AVAILABLE GURUS & RUANGANS FOR TARGET TIME
        $takenGuruIds = [];
        $takenRuanganIds = [];

        if ($this->hari && $this->jam_mulai && $this->jam_selesai) {
            $overlapQuery = Jadwal::where('hari', $this->hari)
                ->where('tahun_ajaran_id', $this->filterTahunAjaran);
            if ($this->isEdit) {
                $overlapQuery->where('id', '!=', $this->jadwalId);
            }
            $overlapQuery->where(function ($q) {
                $this->calculateOverlap($q, $this->jam_mulai, $this->jam_selesai);
            });

            $takenJadwals = $overlapQuery->get();
            $takenGuruIds = $takenJadwals->pluck('guru_id')->filter()->toArray();
            $takenRuanganIds = $takenJadwals->pluck('ruangan_id')->filter()->toArray();
        }

        $optGuruModal = ['' => 'Pilih Guru (Tersedia)'];
        foreach ($gurus as $g) {
            if (! in_array($g->id, $takenGuruIds)) {
                $optGuruModal[$g->id] = $g->user->name;
            }
        }

        $optRuanganModal = ['' => 'Pilih Ruangan (Tersedia)'];
        foreach ($ruangans as $r) {
            if (! in_array($r->id, $takenRuanganIds)) {
                $optRuanganModal[$r->id] = $r->nama_ruangan;
            }
        }

        return view('livewire.admin.kbm.jadwal-pelajaran-index', [
            'listTahunAjaran' => TahunAjaran::orderBy('tahun', 'desc')->orderBy('semester')->get(),
            'matrix' => $matrix,
            'hariMinggu' => $hariMinggu,
            'dataKelas' => $kelases,
            'dataMapel' => $mapels,
            'dataGuru' => $gurus,
            'dataRuangan' => $ruangans,
            'listJadwals' => clone $jadwalData,
            'optSaranJam' => $optSaranJam,
            'optGuruModal' => $optGuruModal,
            'optRuanganModal' => $optRuanganModal,
        ]);
    }
}
