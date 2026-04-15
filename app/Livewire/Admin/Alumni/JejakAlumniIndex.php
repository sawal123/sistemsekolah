<?php

namespace App\Livewire\Admin\Alumni;

use App\Models\KegiatanAlumni;
use App\Models\Siswa;
use App\Exports\AlumniExport;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.admin')]
#[Title('Jejak Alumni (Tracer Study)')]
class JejakAlumniIndex extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $filterTahun = '';
    public $filterJenis = '';
    public $filterStatusVerifikasi = '';

    // Form
    public $isModalOpen = false;
    public $editId = null;
    public $siswa_id;
    public $jenis_kegiatan = 'Kuliah';
    public $nama_instansi;
    public $posisi_jurusan;
    public $tahun_mulai;
    public $deskripsi;
    public $status_verifikasi = 'Verified';
    public $is_public = true;

    protected $rules = [
        'siswa_id' => 'required|exists:siswas,id',
        'jenis_kegiatan' => 'required|in:Kuliah,Kerja,Wirausaha,Mencari_Kerja,Lainnya',
        'nama_instansi' => 'nullable|string|max:255',
        'posisi_jurusan' => 'nullable|string|max:255',
        'tahun_mulai' => 'required|digits:4',
        'deskripsi' => 'nullable|string',
    ];

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterTahun() { $this->resetPage(); }
    public function updatingFilterJenis() { $this->resetPage(); }

    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'alumni-form');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->dispatch('close-modal', 'alumni-form');
    }

    public function resetForm()
    {
        $this->reset(['editId', 'siswa_id', 'jenis_kegiatan', 'nama_instansi', 'posisi_jurusan', 'tahun_mulai', 'deskripsi', 'status_verifikasi', 'is_public']);
        $this->jenis_kegiatan = 'Kuliah';
        $this->status_verifikasi = 'Verified';
        $this->is_public = true;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $this->resetForm();
        $item = KegiatanAlumni::findOrFail($id);
        $this->editId = $id;
        $this->siswa_id = $item->siswa_id;
        $this->jenis_kegiatan = $item->jenis_kegiatan;
        $this->nama_instansi = $item->nama_instansi;
        $this->posisi_jurusan = $item->posisi_jurusan;
        $this->tahun_mulai = $item->tahun_mulai;
        $this->deskripsi = $item->deskripsi;
        $this->status_verifikasi = $item->status_verifikasi;
        $this->is_public = $item->is_public;

        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'alumni-form');
    }

    public function save()
    {
        $this->validate();

        KegiatanAlumni::updateOrCreate(['id' => $this->editId], [
            'siswa_id' => $this->siswa_id,
            'jenis_kegiatan' => $this->jenis_kegiatan,
            'nama_instansi' => $this->nama_instansi,
            'posisi_jurusan' => $this->posisi_jurusan,
            'tahun_mulai' => $this->tahun_mulai,
            'deskripsi' => $this->deskripsi,
            'status_verifikasi' => $this->status_verifikasi,
            'is_public' => $this->is_public,
        ]);

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Data jejak alumni berhasil disimpan!']);
        $this->closeModal();
    }

    public function delete($id)
    {
        KegiatanAlumni::findOrFail($id)->delete();
        $this->dispatch('notify', ['type' => 'warning', 'message' => 'Data jejak alumni telah dihapus.']);
    }

    public function verify($id, $status)
    {
        KegiatanAlumni::findOrFail($id)->update(['status_verifikasi' => $status]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Status verifikasi berhasil diperbarui!']);
    }

    #[On('export-alumni')]
    public function export()
    {
        return Excel::download(new AlumniExport, 'Data-Jejak-Alumni-'.now()->format('Y-m-d').'.xlsx');
    }

    public function render()
    {
        // 1. Stats Data
        $stats = [
            'total' => KegiatanAlumni::count(),
            'verifikasi_pending' => KegiatanAlumni::where('status_verifikasi', 'Pending')->count(),
            // Sebaran (Pie Chart Data)
            'sebaran' => KegiatanAlumni::select('jenis_kegiatan', DB::raw('count(*) as total'))
                ->groupBy('jenis_kegiatan')
                ->get()
                ->pluck('total', 'jenis_kegiatan')
                ->toArray(),
            // Top Instansi
            'top_instansi' => KegiatanAlumni::whereNotNull('nama_instansi')
                ->select('nama_instansi', DB::raw('count(*) as total'))
                ->groupBy('nama_instansi')
                ->orderByDesc('total')
                ->limit(5)
                ->get(),
        ];

        // 2. Query
        $query = KegiatanAlumni::with(['siswa.user', 'siswa.kelas'])
            ->when($this->search, function ($q) {
                $q->whereHas('siswa.user', fn($qu) => $qu->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhere('nama_instansi', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterTahun, function ($q) {
                $q->whereHas('siswa', fn($qu) => $qu->where('tahun_lulus', $this->filterTahun));
            })
            ->when($this->filterJenis, function ($q) {
                $q->where('jenis_kegiatan', $this->filterJenis);
            })
            ->when($this->filterStatusVerifikasi, function ($q) {
                $q->where('status_verifikasi', $this->filterStatusVerifikasi);
            });

        // 3. Selection Data for Modal
        $alumniOptions = Siswa::with('user')
            ->where('status', 'Lulus')
            ->orderBy('tahun_lulus', 'desc')
            ->get();

        $availableYears = Siswa::whereNotNull('tahun_lulus')
            ->distinct()
            ->orderBy('tahun_lulus', 'desc')
            ->pluck('tahun_lulus');

        return view('livewire.admin.alumni.jejak-alumni-index', [
            'kegiatans' => $query->latest()->paginate(10),
            'stats' => $stats,
            'alumniOptions' => $alumniOptions,
            'availableYears' => $availableYears,
        ]);
    }
}
