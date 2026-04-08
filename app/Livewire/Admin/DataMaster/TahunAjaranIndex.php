<?php

namespace App\Livewire\Admin\DataMaster;

use App\Models\TahunAjaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Tahun Ajaran & Semester')]
class TahunAjaranIndex extends Component
{
    use WithPagination;

    public $isModalOpen = false;

    public $editId = null;

    // Form fields
    public $tahun;

    public $semester;

    public $is_active = false;

    public $idBeingDeleted = null;

    public function render()
    {
        // Mengambil data terbaru dan membaginya 10 baris per halaman
        $tahunAjarans = TahunAjaran::latest()->paginate(10);

        return view('livewire.admin.data-master.tahun-ajaran-index', [
            'tahunAjarans' => $tahunAjarans,
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'tahun-ajaran-form');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->idBeingDeleted = null;
        $this->dispatch('close-modal', 'tahun-ajaran-form');
        $this->dispatch('close-modal', 'confirm-delete-modal');
    }

    public function resetForm()
    {
        $this->tahun = '';
        $this->semester = 'Ganjil';
        $this->is_active = false;
        $this->editId = null;
        $this->idBeingDeleted = null;
    }

    public function edit($id)
    {
        $this->resetForm();
        $item = TahunAjaran::findOrFail($id);
        $this->editId = $id;
        $this->tahun = $item->tahun;
        $this->semester = $item->semester;
        $this->is_active = (bool) $item->is_active;
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'tahun-ajaran-form');
    }

    public function toggleStatus($id)
    {
        $item = TahunAjaran::findOrFail($id);

        // Disable others if setting this to active
        if (! $item->is_active) {
            TahunAjaran::where('id', '!=', $id)->update(['is_active' => false]);
            $item->update(['is_active' => true]);
            $msg = 'Status Tahun Ajaran diaktifkan!';
        } else {
            $item->update(['is_active' => false]);
            $msg = 'Status Tahun Ajaran dinonaktifkan!';
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $msg,
        ]);
    }

    public function save()
    {
        $this->validate([
            'tahun' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
        ]);

        if ($this->editId) {
            $item = TahunAjaran::findOrFail($this->editId);
            $item->update([
                'tahun' => $this->tahun,
                'semester' => $this->semester,
                'is_active' => $this->is_active,
            ]);

            if ($this->is_active) {
                TahunAjaran::where('id', '!=', $item->id)->update(['is_active' => false]);
            }
        } else {
            $item = TahunAjaran::create([
                'tahun' => $this->tahun,
                'semester' => $this->semester,
                'is_active' => $this->is_active,
            ]);

            if ($this->is_active) {
                TahunAjaran::where('id', '!=', $item->id)->update(['is_active' => false]);
            }
        }

        $this->closeModal();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->editId ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.',
        ]);
    }

    public function confirmDelete($id)
    {
        $this->idBeingDeleted = $id;
        $this->dispatch('open-modal', 'confirm-delete-modal');
    }

    public function delete()
    {
        if ($this->idBeingDeleted) {
            TahunAjaran::findOrFail($this->idBeingDeleted)->delete();
            $this->closeModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Data berhasil dihapus.',
            ]);
        }
    }
}
