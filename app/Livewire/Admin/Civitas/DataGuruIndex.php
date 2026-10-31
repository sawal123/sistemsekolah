<?php

namespace App\Livewire\Admin\Civitas;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\TahunAjaran;
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
            $this->dispatch('notify', type: 'success', message: 'Password berhasil direset menjadi: '.$password);
        } else {
            $this->dispatch('notify', type: 'error', message: 'Gagal mereset password. Pastikan tanggal lahir sudah diisi.');
        }
    }

    public function toggleStatus($guruId)
    {
        $guru = Guru::with('user')->findOrFail($guruId);
        if ($guru->user) {
            $guru->user->update([
                'is_active' => ! $guru->user->is_active,
            ]);
            $status = $guru->user->is_active ? 'diaktifkan' : 'dinonaktifkan (ditangguhkan)';
            $this->dispatch('notify', type: 'success', message: "Akun guru {$guru->user->name} berhasil {$status}.");
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
            $guru = Guru::with(['user', 'kelas'])->findOrFail($this->confirmDeleteId);
            $taAktif = TahunAjaran::where('is_active', true)->first();

            // 1. Cek apakah guru sedang menjadi Wali Kelas di kelas/rombel aktif
            $isWaliKelas = ($guru->kelas !== null)
                || ($taAktif && Rombel::where('wali_kelas_id', $guru->id)->where('tahun_ajaran_id', $taAktif->id)->exists());

            if ($isWaliKelas) {
                $this->dispatch('notify', 
                    type: 'error',
                    message: 'Guru tidak dapat dihapus karena masih tercatat sebagai Wali Kelas aktif. Silakan alihkan perwalian kelas terlebih dahulu atau gunakan opsi suspend/nonaktifkan akun.'
                );
                $this->dispatch('close-modal', 'confirm-delete-guru');
                $this->confirmDeleteId = null;

                return;
            }

            // 2. Cek apakah guru memiliki jadwal mengajar aktif pada semester ini
            $hasActiveSchedule = $taAktif && Jadwal::where('guru_id', $guru->id)
                ->where('tahun_ajaran_id', $taAktif->id)
                ->exists();

            if ($hasActiveSchedule) {
                $this->dispatch('notify', 
                    type: 'error',
                    message: 'Guru tidak dapat dihapus karena memiliki jadwal mengajar aktif pada semester ini. Silakan hapus/pindahkan jadwal mengajar terlebih dahulu.'
                );
                $this->dispatch('close-modal', 'confirm-delete-guru');
                $this->confirmDeleteId = null;

                return;
            }

            // Aman untuk dihapus jika tidak ada keterikatan aktif
            if ($guru->user) {
                $guru->user->delete();
            } else {
                $guru->delete();
            }

            $this->dispatch('notify', type: 'success', message: 'Data guru berhasil dihapus.');
            $this->dispatch('close-modal', 'confirm-delete-guru');
            $this->confirmDeleteId = null;
        }
    }

    public function render()
    {
        $taAktif = TahunAjaran::where('is_active', true)->first();

        $query = Guru::with([
            'user:id,name,email,is_active',
            'kelas:id,nama_kelas,wali_kelas_id',
            'rombels' => fn($q) => $taAktif ? $q->where('tahun_ajaran_id', $taAktif->id)->with('kelas:id,nama_kelas') : $q,
        ]);

        // Calculate Stats (1 query agregasi + 1 count relasi)
        $statRow = Guru::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN jabatan = 'Guru Honorer' THEN 1 ELSE 0 END) as honorer,
            SUM(CASE WHEN jabatan = 'Guru Tetap' THEN 1 ELSE 0 END) as tetap,
            SUM(CASE WHEN jabatan = 'Wakasek' THEN 1 ELSE 0 END) as wakasek
        ")->first();

        $waliCount = $taAktif
            ? Rombel::where('tahun_ajaran_id', $taAktif->id)->whereNotNull('wali_kelas_id')->distinct('wali_kelas_id')->count('wali_kelas_id')
            : Guru::has('kelas')->count();

        $stats = [
            'total' => (int) ($statRow->total ?? 0),
            'honorer' => (int) ($statRow->honorer ?? 0),
            'tetap' => (int) ($statRow->tetap ?? 0),
            'wakasek' => (int) ($statRow->wakasek ?? 0),
            'wali_kelas' => $waliCount,
        ];

        if ($this->search) {
            $query->where(function ($sub) {
                $sub->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%');
                })->orWhere('nip', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filterJabatan) {
            $query->where('jabatan', $this->filterJabatan);
        }

        if ($this->filterWaliKelas !== '') {
            if ($this->filterWaliKelas == 'ya') {
                $query->where(function ($q) use ($taAktif) {
                    $q->has('kelas');
                    if ($taAktif) {
                        $q->orWhereHas('rombels', fn($r) => $r->where('tahun_ajaran_id', $taAktif->id));
                    }
                });
            } elseif ($this->filterWaliKelas == 'tidak') {
                $query->doesntHave('kelas');
                if ($taAktif) {
                    $query->whereDoesntHave('rombels', fn($r) => $r->where('tahun_ajaran_id', $taAktif->id));
                }
            }
        }

        $gurus = $query->latest()->paginate($this->perPage);

        return view('livewire.admin.civitas.data-guru-index', [
            'gurus' => $gurus,
            'stats' => $stats,
            'taAktif' => $taAktif,
        ]);
    }
}
