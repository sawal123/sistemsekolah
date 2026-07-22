<?php

namespace App\Livewire\Admin\Civitas;

use App\Models\Guru;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Data Guru')]
class DataGuruIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterJabatan = '';
    public $filterWaliKelas = '';
    public $confirmDeleteId = null;
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetPassword($guruId)
    {
        $guru = Guru::findOrFail($guruId);
        if ($guru->user && $guru->tanggal_lahir) {
            $password = Carbon::parse($guru->tanggal_lahir)->format('dmY');
            $guru->user->update([
                'password' => Hash::make($password),
            ]);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Password berhasil direset menjadi: '.$password]);
        } else {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal mereset password. Pastikan tanggal lahir sudah diisi.']);
        }
    }

    public function confirmDelete($guruId)
    {
        $this->confirmDeleteId = $guruId;
        $this->dispatch('open-modal', 'confirm-delete-guru');
    }

    public function delete()
    {
        if ($this->confirmDeleteId) {
            $guru = Guru::findOrFail($this->confirmDeleteId);
            if ($guru->user) {
                $guru->user->delete();
            } else {
                $guru->delete();
            }
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Data guru berhasil dihapus.']);
            $this->dispatch('close-modal', 'confirm-delete-guru');
            $this->confirmDeleteId = null;
        }
    }

    public function render()
    {
        $query = Guru::with(['user', 'kelas']);

        // Calculate Stats (Unfiltered total)
        $stats = [
            'total' => Guru::count(),
            'honorer' => Guru::where('jabatan', 'Guru Honorer')->count(),
            'tetap' => Guru::where('jabatan', 'Guru Tetap')->count(),
            'wakasek' => Guru::where('jabatan', 'Wakasek')->count(),
            'wali_kelas' => Guru::has('kelas')->count(),
        ];

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            })->orWhere('nip', 'like', '%'.$this->search.'%');
        }

        if ($this->filterJabatan) {
            $query->where('jabatan', $this->filterJabatan);
        }

        if ($this->filterWaliKelas !== '') {
            if ($this->filterWaliKelas == 'ya') {
                $query->has('kelas');
            } elseif ($this->filterWaliKelas == 'tidak') {
                $query->doesntHave('kelas');
            }
        }

        $gurus = $query->latest()->paginate($this->perPage);

        return view('livewire.admin.civitas.data-guru-index', [
            'gurus' => $gurus,
            'stats' => $stats,
        ]);
    }
}
