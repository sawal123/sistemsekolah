<?php

namespace App\Livewire\Admin\DataMaster;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\AnggotaRombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Data Kelas')]

class DataKelasIndex extends Component
{
    use WithPagination;

    // Search & Filter
    public $search = '';

    public $perPage = 10;

    public $filterJenjang = '';

    // Form Properties
    public $nama_kelas;

    public $jenjang = 'SMP';

    public $wali_kelas_id;

    public $jurusan_id;

    public $editId = null;

    public $isModalOpen = false;

    public ?int $deleteId = null;

    public string $deleteClassName = '';

    public int $deleteStudentCount = 0;

    public string $deleteMessage = '';

    // View Students Modal
    public $selectedKelas = null;

    public $studentsInKelas = [];

    protected $rules = [
        'nama_kelas' => 'required|string|max:50',
        'jenjang' => 'required|in:SMP,SMA,SMK',
        'jurusan_id' => 'nullable|required_if:jenjang,SMK|exists:jurusans,id',
        'wali_kelas_id' => 'nullable|exists:gurus,id',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterJenjang()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['nama_kelas', 'wali_kelas_id', 'jurusan_id', 'editId']);
        $this->jenjang = 'SMP';
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'kelas-form');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['deleteId', 'deleteClassName', 'deleteStudentCount', 'deleteMessage']);
        $this->dispatch('close-modal', 'kelas-form');
        $this->dispatch('close-modal', 'confirm-delete-modal');
    }

    public function save()
    {
        $rules = $this->rules;
        $this->validate($rules);
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if ($tahunAjaranAktif && $this->wali_kelas_id) {
            $waliDipakai = Rombel::where('tahun_ajaran_id', $tahunAjaranAktif->id)
                ->where('wali_kelas_id', $this->wali_kelas_id)
                ->when($this->editId, fn($q) => $q->where('kelas_id', '!=', $this->editId))
                ->exists();

            if ($waliDipakai) {
                $this->addError('wali_kelas_id', 'Guru ini sudah menjadi wali kelas pada tahun ajaran aktif.');

                return;
            }
        }

        $kelas = Kelas::updateOrCreate(['id' => $this->editId], [
            'nama_kelas' => $this->nama_kelas,
            'jenjang' => $this->jenjang,
            'jurusan_id' => $this->jenjang === 'SMK' ? $this->jurusan_id : null,
            'wali_kelas_id' => $this->wali_kelas_id ?: null,
        ]);
        if ($tahunAjaranAktif) {
            Rombel::updateOrCreate(
                [
                    'kelas_id' => $kelas->id,
                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                ],
                [
                    'wali_kelas_id' => $this->wali_kelas_id ?: null,
                    'status' => 'Aktif',
                ]
            );
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->editId ? 'Kelas berhasil diperbarui!' : 'Kelas berhasil ditambahkan!',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $this->resetValidation();
        $kelas = Kelas::findOrFail($id);
        $this->editId = $id;
        $this->nama_kelas = $kelas->nama_kelas;
        $this->jenjang = $kelas->jenjang;
        $this->wali_kelas_id = $kelas->wali_kelas_id;
        $this->jurusan_id = $kelas->jurusan_id;

        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'kelas-form');
    }

    public function confirmDelete($id)
    {
        $kelas = Kelas::withCount('siswas')->findOrFail($id);

        $this->deleteId = $kelas->id;
        $this->deleteClassName = $kelas->nama_kelas;
        $this->deleteStudentCount = $kelas->siswas_count;
        $this->deleteMessage = $kelas->siswas_count > 0
            ? "Kelas {$kelas->nama_kelas} berisi {$kelas->siswas_count} siswa. Jika dilanjutkan, kelas akan dihapus (soft delete) dan siswa akan dikeluarkan dari kelas ini. Akun siswa tetap aktif dan data historis (nilai, rapor, pembayaran) tetap tersimpan. Siswa dapat dimasukkan ke kelas lain melalui menu Data Siswa."
            : "Kelas {$kelas->nama_kelas} tidak memiliki siswa. Kelas akan dinonaktifkan menggunakan soft delete dan tetap tersimpan di database.";

        $this->dispatch('open-modal', 'confirm-delete-modal');
    }

    public function delete()
    {
        if (! $this->deleteId) {
            return;
        }

        $kelas = Kelas::with(['siswas'])->findOrFail($this->deleteId);
        $studentCount = $kelas->siswas->count();
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        DB::transaction(function () use ($kelas, $tahunAjaranAktif) {
            // 1. Keluarkan semua siswa dari kelas ini (set kelas_id = null)
            if ($kelas->siswas->isNotEmpty()) {
                Siswa::where('kelas_id', $kelas->id)->update(['kelas_id' => null]);

                // 2. Hapus keanggotaan siswa dari rombel aktif tahun ajaran ini
                if ($tahunAjaranAktif) {
                    $rombelIds = Rombel::where('kelas_id', $kelas->id)
                        ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
                        ->pluck('id');

                    if ($rombelIds->isNotEmpty()) {
                        AnggotaRombel::whereIn('rombel_id', $rombelIds)->delete();
                    }
                }
            }

            // 3. Tutup rombel aktif untuk kelas ini (jika ada)
            if ($tahunAjaranAktif) {
                Rombel::where('kelas_id', $kelas->id)
                    ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
                    ->update(['status' => 'Ditutup']);
            }

            // 4. Hapus wali kelas, lalu soft-delete kelas
            $kelas->update(['wali_kelas_id' => null]);
            $kelas->delete();
        });

        $this->closeModal();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $studentCount > 0
                ? "Kelas berhasil dihapus. {$studentCount} siswa dikeluarkan dari kelas — akun tetap aktif. Data historis tetap tersimpan."
                : 'Kelas berhasil dinonaktifkan. Data tetap tersimpan.',
        ]);
    }

    public function viewStudents($id)
    {
        $this->selectedKelas = Kelas::with(['siswas.user'])->findOrFail($id);
        $this->dispatch('open-modal', 'view-students-modal');
    }

    public function removeStudent($siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);
        $siswa->update(['kelas_id' => null]);
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        if ($tahunAjaranAktif) {
            AnggotaRombel::where('siswa_id', $siswa->id)
                ->whereHas('rombel', fn($q) => $q->where('tahun_ajaran_id', $tahunAjaranAktif->id))
                ->delete();
        }

        // Refresh selected kelas students
        if ($this->selectedKelas) {
            $this->selectedKelas->load('siswas.user');
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Siswa berhasil dikeluarkan dari kelas.']);
    }

    public function render()
    {
        $query = Kelas::with(['wali_kelas.user', 'jurusan'])
            ->withCount('siswas')
            ->when($this->search, function ($q) {
                $q->where('nama_kelas', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterJenjang, function ($q) {
                $q->where('jenjang', $this->filterJenjang);
            });

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        // Get teachers for dropdown with "Already Wali" check
        $gurus = Guru::with(['user', 'rombels' => function ($q) use ($tahunAjaranAktif) {
            $q->with('kelas')
                ->when($tahunAjaranAktif, fn($query) => $query->where('tahun_ajaran_id', $tahunAjaranAktif->id));
        }])->get()->mapWithKeys(function ($guru) {
            $label = $guru->user->name;
            $rombelAktif = $guru->rombels->first();
            if ($rombelAktif?->kelas && $rombelAktif->kelas->id != $this->editId) {
                $label .= ' (Sudah Wali Kelas: ' . $rombelAktif->kelas->nama_kelas . ')';
            }

            return [$guru->id => $label];
        })->toArray();

        // Add null option explicitly as key-value
        $gurus = ['' => 'Pilih Wali Kelas'] + $gurus;

        // General Stats
        $stats = [
            'total_kelas' => Kelas::count(),
            'total_siswa' => Siswa::whereNotNull('kelas_id')->count(),
            'smp_count' => Kelas::where('jenjang', 'SMP')->count(),
            'sma_count' => Kelas::where('jenjang', 'SMA')->count(),
            'smk_count' => Kelas::where('jenjang', 'SMK')->count(),
        ];

        return view('livewire.admin.data-master.data-kelas-index', [
            'kelas' => $query->latest()->paginate($this->perPage),
            'gurus' => $gurus,
            'jurusans' => Jurusan::where('is_active', true)->orderBy('nama')->get(),
            'stats' => $stats,
        ]);
    }
}
