<?php

namespace App\Livewire\Admin\Akademik;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Ruangan;
use App\Models\Setting;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Smart Jadwal Pelajaran')]
class JadwalIndex extends Component
{
    use WithPagination;

    // Filter Global
    public $filterKelas = '';
    public $filterGuru = '';
    public $filterRuangan = '';
    public $activeTab = 'grid'; // grid | list

    // Modal Form State
    public $isModalOpen = false;
    public $isEdit = false;
    public $jadwalId = null;

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
        // Tetapkan filter otomatis berdasarkan Role Login
        $user = auth()->user();
        
        if ($user->hasRole('guru')) {
            // Guru hanya bisa melihat jadwal dirinya
            $this->filterGuru = $user->guru->id ?? '';
        } elseif ($user->hasRole('siswa')) {
            // Siswa hanya bisa melihat jadwal kelasnya
            $this->filterKelas = $user->siswa->kelas_id ?? '';
        }
    }

    public function updatedJamKe($val)
    {
        // Hitung Otomatis (Smart Time Generator)
        if ($val > 0) {
            $slotDuration = Setting::where('key', 'durasi_jam_pelajaran')->value('value') ?? 45;
            $startTime = Setting::where('key', 'jam_mulai_pelajaran')->value('value') ?? '07:15';

            $startDelay = ($val - 1) * $slotDuration;
            
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
        
        $baseQuery = Jadwal::where('hari', $this->hari);
        if ($this->isEdit) {
            $baseQuery->where('id', '!=', $this->jadwalId);
        }

        // 1. Guru Overlap (Apakah pak guru Budi double ajar?)
        $guruOverlap = (clone $baseQuery)
            ->where('guru_id', $this->guru_id)
            ->where(function($q) {
                $this->calculateOverlap($q, $this->jam_mulai, $this->jam_selesai);
            })->first();

        if ($guruOverlap) {
            $this->conflictErrors[] = "Guru ini sedang mengajar {$guruOverlap->mapel->nama_mapel} di kelas {$guruOverlap->kelas->nama_kelas} pada slot waktu ini.";
        }

        // 2. Kelas Overlap (Apakah kelas ini jam tsb sudah ada belajar?)
        $kelasOverlap = (clone $baseQuery)
            ->where('kelas_id', $this->kelas_id)
            ->where(function($q) {
                $this->calculateOverlap($q, $this->jam_mulai, $this->jam_selesai);
            })->first();

        if ($kelasOverlap) {
            $this->conflictErrors[] = "Kelas ini sudah ada jadwal pelajaran {$kelasOverlap->mapel->nama_mapel} bersama {$kelasOverlap->guru->user->name} pada slot waktu ini.";
        }

        // 3. Ruangan Overlap (Apakah Lab ini diperebutkan?)
        if ($this->ruangan_id) {
            $ruanganOverlap = (clone $baseQuery)
                ->where('ruangan_id', $this->ruangan_id)
                ->where(function($q) {
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
        $this->validate();

        if (!$this->checkConflicts()) {
            return; // Hentikan proses simpankan karena bentrok
        }

        Jadwal::updateOrCreate(
            ['id' => $this->jadwalId],
            [
                'kelas_id' => $this->kelas_id,
                'mapel_id' => $this->mapel_id,
                'guru_id' => $this->guru_id,
                'ruangan_id' => $this->ruangan_id ?: null,
                'hari' => $this->hari,
                'jam_mulai' => $this->jam_mulai,
                'jam_selesai' => $this->jam_selesai,
            ]
        );

        session()->flash('message', 'Jadwal berhasil ' . ($this->isEdit ? 'diperbarui' : 'disimpan') . ' tanpa bentrok!');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->reset(['isModalOpen', 'isEdit', 'jadwalId', 'kelas_id', 'mapel_id', 'guru_id', 'ruangan_id', 'hari', 'jam_mulai', 'jam_selesai', 'jamKe', 'conflictErrors']);
        $this->resetValidation();
    }

    public function openModalForCreate()
    {
        $this->closeModal();
        $this->isModalOpen = true;
    }

    public function hapusJadwal($id)
    {
        Jadwal::destroy($id);
        session()->flash('message', 'Agenda jadwal berhasil dihapus.');
    }

    public function render()
    {
        $query = Jadwal::with(['kelas', 'mapel', 'guru', 'ruangan']);

        if ($this->filterKelas) $query->where('kelas_id', $this->filterKelas);
        if ($this->filterGuru) $query->where('guru_id', $this->filterGuru);
        if ($this->filterRuangan) $query->where('ruangan_id', $this->filterRuangan);

        $jadwalData = $query->orderBy('jam_mulai')->get();

        // Mapping For Grid 6 Days
        $hariMinggu = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $matrix = [];
        foreach ($hariMinggu as $hari) {
            $matrix[$hari] = $jadwalData->where('hari', $hari)->values();
        }

        // Dependent Dropdowns Logic - Just pass all maps/classes/teachers
        $kelases = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        // Here we could filter Mapel based on SMP/SMA logic, but for simplicity we fetch all
        $mapels = Mapel::orderBy('nama_mapel')->get();
        $gurus = Guru::with('user')->get()->sortBy('user.name');
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();

        return view('livewire.admin.akademik.jadwal-index', [
            'matrix' => $matrix,
            'hariMinggu' => $hariMinggu,
            'dataKelas' => $kelases,
            'dataMapel' => $mapels,
            'dataGuru' => $gurus,
            'dataRuangan' => $ruangans,
            'listJadwals' => clone $jadwalData // For List view mode
        ]);
    }
}
