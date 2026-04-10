<?php

namespace App\Livewire\Admin\DataMaster;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
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

    public $editId = null;

    public $isModalOpen = false;

    // View Students Modal
    public $selectedKelas = null;

    public $studentsInKelas = [];

    protected $rules = [
        'nama_kelas' => 'required|string|max:50',
        'jenjang' => 'required|in:SMP,SMA',
        'wali_kelas_id' => 'nullable|unique:kelas,wali_kelas_id',
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
        $this->reset(['nama_kelas', 'wali_kelas_id', 'editId']);
        $this->jenjang = 'SMP';
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'kelas-form');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->dispatch('close-modal', 'kelas-form');
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->editId) {
            $rules['wali_kelas_id'] = 'nullable|unique:kelas,wali_kelas_id,'.$this->editId;
        }

        $this->validate($rules);

        Kelas::updateOrCreate(['id' => $this->editId], [
            'nama_kelas' => $this->nama_kelas,
            'jenjang' => $this->jenjang,
            'wali_kelas_id' => $this->wali_kelas_id ?: null,
        ]);

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

        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'kelas-form');
    }

    public function confirmDelete($id)
    {
        $this->editId = $id;
        $this->dispatch('open-modal', 'confirm-delete-modal');
    }

    public function delete()
    {
        Kelas::find($this->editId)->delete();
        $this->dispatch('close-modal', 'confirm-delete-modal');
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Kelas berhasil dihapus!']);
        $this->reset(['editId']);
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

        // Refresh selected kelas students
        if ($this->selectedKelas) {
            $this->selectedKelas->load('siswas.user');
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Siswa berhasil dikeluarkan dari kelas.']);
    }

    public function render()
    {
        $query = Kelas::with(['wali_kelas.user'])
            ->withCount('siswas')
            ->when($this->search, function ($q) {
                $q->where('nama_kelas', 'like', '%'.$this->search.'%');
            })
            ->when($this->filterJenjang, function ($q) {
                $q->where('jenjang', $this->filterJenjang);
            });

        // Get teachers for dropdown with "Already Wali" check
        $gurus = Guru::with('user', 'kelas')->get()->mapWithKeys(function ($guru) {
            $label = $guru->user->name;
            if ($guru->kelas && $guru->kelas->id != $this->editId) {
                $label .= ' (Sudah Wali Kelas: '.$guru->kelas->nama_kelas.')';
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
        ];

        return view('livewire.admin.data-master.data-kelas-index', [
            'kelas' => $query->latest()->paginate($this->perPage),
            'gurus' => $gurus,
            'stats' => $stats,
        ]);
    }
}
