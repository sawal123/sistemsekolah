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

    public $tanggal_mulai;

    public $tanggal_selesai;

    public $status = 'Draft';

    public $is_active = false;

    public $idBeingDeleted = null;

    public $deleteErrorMessage = null;

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
        $this->tanggal_mulai = '';
        $this->tanggal_selesai = '';
        $this->status = 'Draft';
        $this->is_active = false;
        $this->editId = null;
        $this->idBeingDeleted = null;
        $this->deleteErrorMessage = null;
    }

    public function edit($id)
    {
        $this->resetForm();
        $item = TahunAjaran::findOrFail($id);
        $this->editId = $id;
        $this->tahun = $item->tahun;
        $this->semester = $item->semester;
        $this->tanggal_mulai = $item->tanggal_mulai ? $item->tanggal_mulai->format('Y-m-d') : '';
        $this->tanggal_selesai = $item->tanggal_selesai ? $item->tanggal_selesai->format('Y-m-d') : '';
        $this->status = $item->status;
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
            $item->update(['is_active' => true, 'status' => 'Aktif']);
            $msg = 'Status Tahun Ajaran diaktifkan!';
        } else {
            $item->update([
                'is_active' => false,
                'status' => $item->status === 'Aktif' ? 'Ditutup' : $item->status,
            ]);
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
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:Draft,Aktif,Ditutup,Diarsipkan',
        ]);

        // Jika set Aktif, sinkronkan is_active
        $isActive = $this->status === 'Aktif' || $this->is_active;

        if ($this->editId) {
            $item = TahunAjaran::findOrFail($this->editId);

            // Cegah perubahan status dari Draft ke Aktif jika sudah ada yang aktif
            if ($isActive && ! $item->is_active) {
                TahunAjaran::where('id', '!=', $item->id)->update(['is_active' => false]);
            }

            $item->update([
                'tahun' => $this->tahun,
                'semester' => $this->semester,
                'tanggal_mulai' => $this->tanggal_mulai ?: null,
                'tanggal_selesai' => $this->tanggal_selesai ?: null,
                'status' => $this->status,
                'is_active' => $isActive,
            ]);

            if ($isActive) {
                TahunAjaran::where('id', '!=', $item->id)->update(['is_active' => false]);
            }
        } else {
            if ($isActive) {
                TahunAjaran::where('is_active', true)->update(['is_active' => false]);
            }

            $item = TahunAjaran::create([
                'tahun' => $this->tahun,
                'semester' => $this->semester,
                'tanggal_mulai' => $this->tanggal_mulai ?: null,
                'tanggal_selesai' => $this->tanggal_selesai ?: null,
                'status' => $this->status,
                'is_active' => $isActive,
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->editId ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.',
        ]);
    }

    public function confirmDelete($id)
    {
        $item = TahunAjaran::findOrFail($id);

        if (! $item->canBeDeleted()) {
            $this->deleteErrorMessage = "Tahun ajaran {$item->tahun} - {$item->semester} tidak dapat dihapus karena berstatus '{$item->status}'. Hanya tahun ajaran dengan status 'Draft' yang boleh dihapus. Ubah status menjadi 'Ditutup' atau 'Diarsipkan' jika sudah tidak digunakan.";
            $this->dispatch('open-modal', 'cannot-delete-modal');

            return;
        }

        $this->deleteErrorMessage = null;
        $this->idBeingDeleted = $id;
        $this->dispatch('open-modal', 'confirm-delete-modal');
    }

    public function delete()
    {
        if (! $this->idBeingDeleted) {
            return;
        }

        $item = TahunAjaran::findOrFail($this->idBeingDeleted);

        if (! $item->canBeDeleted()) {
            $this->closeModal();
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Tahun ajaran ini tidak dapat dihapus karena sudah memiliki data transaksi.',
            ]);

            return;
        }

        $item->delete();
        $this->closeModal();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }
}
