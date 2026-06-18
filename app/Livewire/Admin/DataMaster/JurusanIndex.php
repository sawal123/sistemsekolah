<?php

namespace App\Livewire\Admin\DataMaster;

use App\Models\Jurusan;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Master Jurusan SMK')]
class JurusanIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public bool $isModalOpen = false;

    public ?int $editId = null;

    public ?int $idBeingDeleted = null;

    public string $kode = '';

    public string $nama = '';

    public string $deskripsi = '';

    public bool $is_active = true;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Jurusan::withCount(['kelas', 'siswas', 'spps'])
            ->when($this->search, fn ($q) => $q->where(function ($sub) {
                $sub->where('kode', 'like', '%'.$this->search.'%')
                    ->orWhere('nama', 'like', '%'.$this->search.'%');
            }))
            ->when($this->filterStatus !== '', fn ($q) => $q->where('is_active', $this->filterStatus === 'aktif'))
            ->orderBy('nama');

        return view('livewire.admin.data-master.jurusan-index', [
            'jurusans' => $query->paginate(10),
            'stats' => [
                'total' => Jurusan::count(),
                'aktif' => Jurusan::where('is_active', true)->count(),
                'kelas' => Jurusan::withCount('kelas')->get()->sum('kelas_count'),
                'siswa' => Jurusan::withCount('siswas')->get()->sum('siswas_count'),
            ],
        ]);
    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'jurusan-form');
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $jurusan = Jurusan::findOrFail($id);

        $this->editId = $jurusan->id;
        $this->kode = $jurusan->kode;
        $this->nama = $jurusan->nama;
        $this->deskripsi = $jurusan->deskripsi ?? '';
        $this->is_active = $jurusan->is_active;
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'jurusan-form');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'kode' => ['required', 'string', 'max:30', Rule::unique('jurusans', 'kode')->ignore($this->editId)],
            'nama' => ['required', 'string', 'max:150', Rule::unique('jurusans', 'nama')->ignore($this->editId)],
            'deskripsi' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        Jurusan::updateOrCreate(
            ['id' => $this->editId],
            [
                'kode' => strtoupper(trim($validated['kode'])),
                'nama' => trim($validated['nama']),
                'deskripsi' => filled($validated['deskripsi']) ? trim($validated['deskripsi']) : null,
                'is_active' => $validated['is_active'],
            ]
        );

        $message = $this->editId ? 'Jurusan berhasil diperbarui.' : 'Jurusan berhasil ditambahkan.';
        $this->closeModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => $message]);
    }

    public function toggleActive(int $id): void
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->update(['is_active' => ! $jurusan->is_active]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $jurusan->is_active ? 'Jurusan diaktifkan.' : 'Jurusan dinonaktifkan.',
        ]);
    }

    public function confirmDelete(int $id): void
    {
        $this->idBeingDeleted = $id;
        $this->dispatch('open-modal', 'confirm-delete-jurusan');
    }

    public function delete(): void
    {
        if (! $this->idBeingDeleted) {
            return;
        }

        $jurusan = Jurusan::findOrFail($this->idBeingDeleted);
        if ($jurusan->kelas()->exists() || $jurusan->siswas()->exists() || $jurusan->spps()->exists()) {
            $this->closeModal();
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Jurusan tidak dapat dihapus karena sudah digunakan. Nonaktifkan jurusan jika tidak dipakai lagi.',
            ]);

            return;
        }

        $jurusan->delete();
        $this->closeModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Jurusan berhasil dihapus.']);
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->idBeingDeleted = null;
        $this->dispatch('close-modal', 'jurusan-form');
        $this->dispatch('close-modal', 'confirm-delete-jurusan');
    }

    private function resetForm(): void
    {
        $this->reset(['editId', 'kode', 'nama', 'deskripsi', 'idBeingDeleted']);
        $this->is_active = true;
        $this->resetValidation();
    }
}
