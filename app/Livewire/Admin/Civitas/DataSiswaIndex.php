<?php

namespace App\Livewire\Admin\Civitas;

use App\Models\Kelas;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Data Siswa')]
class DataSiswaIndex extends Component
{
    use WithFileUploads, WithPagination;

    // Filters
    public $search = '';
    public $perPage = 10;

    public $filterJenjang = '';

    public $filterKelas = '';

    public $filterStatus = '';

    // Form Properties (Tab 1: Akun & Akademik)
    public $name;

    public $email;

    public $password;

    public $nisn;

    public $nis;

    public $jenjang = 'SMP';

    public $kelas_id;

    // Form Properties (Tab 2: Data Diri)
    public $tempat_lahir;

    public $tanggal_lahir;

    public $agama;

    public $alamat;

    public $foto;

    public $existingFoto;

    // Form Properties (Tab 3: Data Orang Tua)
    public $nama_ayah;

    public $nama_ibu;

    public $pekerjaan_ayah;

    public $pekerjaan_ibu;

    public $no_telp_ortu;

    // Status
    public $status = 'Aktif';

    // State
    public $editId = null;

    public $isModalOpen = false;

    // Detail View
    public $detailSiswa = null;

    public $schoolSettings = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterJenjang()
    {
        $this->resetPage();
        $this->filterKelas = '';
    }

    public function updatingFilterKelas()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset([
            'editId', 'name', 'email', 'password', 'nisn', 'nis', 'jenjang', 'kelas_id',
            'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'foto', 'existingFoto',
            'nama_ayah', 'nama_ibu', 'pekerjaan_ayah', 'pekerjaan_ibu', 'no_telp_ortu', 'status',
        ]);
        $this->foto = null; // Explicitly clear file instance
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'siswa-form');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->dispatch('close-modal', 'siswa-form');
    }

    public function showDetail($id)
    {
        $this->detailSiswa = Siswa::with(['user', 'kelas'])->findOrFail($id);
        $this->schoolSettings = Setting::pluck('value', 'key')->toArray();
        $this->dispatch('open-modal', 'view-detail-modal');
    }

    public function downloadPdf($id)
    {
        $siswa = Siswa::with(['user', 'kelas'])->findOrFail($id);
        $settings = Setting::pluck('value', 'key')->toArray();

        // Use storage_path for absolute internal path to file
        $logoPath = storage_path('app/public/'.($settings['app_logo'] ?? 'branding/logo.png'));

        // Safety check if logo doesn't exist
        $logo = file_exists($logoPath) ? $logoPath : null;

        $pdf = Pdf::loadView('exports.siswa-profile-pdf', [
            'siswa' => $siswa,
            'settings' => $settings,
            'logo' => $logo,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Profil-Siswa-'.$siswa->nis.'.pdf');
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,'.($this->editId ? Siswa::find($this->editId)->user_id : ''),
            'nisn' => 'required|string|unique:siswas,nisn,'.$this->editId,
            'nis' => 'required|string|unique:siswas,nis,'.$this->editId,
            'jenjang' => 'required|in:SMP,SMA',
            'kelas_id' => 'required|exists:kelas,id',
            'status' => 'required|in:Aktif,Lulus,Pindah,Dikeluarkan',
            'foto' => 'nullable|image|max:1024',
        ]);

        // 1. Process User Account
        $finalEmail = $this->email;
        if (! $finalEmail) {
            $finalEmail = $this->nis.'@sekolah.sch.id';
        }

        $finalPassword = $this->password;
        if (! $finalPassword) {
            if ($this->tanggal_lahir) {
                $finalPassword = Carbon::parse($this->tanggal_lahir)->format('dmY');
            } else {
                $finalPassword = 'siswa123';
            }
        }

        if ($this->editId) {
            $siswa = Siswa::findOrFail($this->editId);
            $user = $siswa->user;
            $user->update([
                'name' => $this->name,
                'email' => $finalEmail,
            ]);
            if ($this->password) {
                $user->update(['password' => Hash::make($this->password)]);
            }
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $finalEmail,
                'password' => Hash::make($finalPassword),
            ]);
            $user->assignRole('siswa');
        }

        // 2. Process Photo
        $fotoPath = $this->existingFoto;
        if ($this->foto) {
            if ($this->existingFoto) {
                Storage::disk('public')->delete($this->existingFoto);
            }
            $fotoPath = $this->foto->store('students/avatars', 'public');
        }

        // 3. Process Siswa Data
        $siswaData = [
            'user_id' => $user->id,
            'kelas_id' => $this->kelas_id,
            'nisn' => $this->nisn,
            'nis' => $this->nis,
            'jenjang' => $this->jenjang,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'agama' => $this->agama,
            'alamat' => $this->alamat,
            'foto' => $fotoPath,
            'nama_ayah' => $this->nama_ayah,
            'nama_ibu' => $this->nama_ibu,
            'pekerjaan_ayah' => $this->pekerjaan_ayah,
            'pekerjaan_ibu' => $this->pekerjaan_ibu,
            'no_telp_ortu' => $this->no_telp_ortu,
            'status' => $this->status,
        ];

        // Automation for Graduation
        if ($this->status === 'Lulus') {
            $siswaData['tahun_lulus'] = now()->year;
            // Also assign alumni role to user
            if (!$user->hasRole('alumni')) {
                $user->assignRole('alumni');
            }
        }

        Siswa::updateOrCreate(['id' => $this->editId], $siswaData);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->editId ? 'Data siswa berhasil diperbarui!' : 'Siswa baru berhasil ditambahkan!',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->foto = null; // Fix: clear any previous upload instance

        $siswa = Siswa::with('user')->findOrFail($id);
        $this->editId = $id;

        $this->name = $siswa->user->name;
        $this->email = $siswa->user->email;
        $this->password = ''; // Don't show password

        $this->nisn = $siswa->nisn;
        $this->nis = $siswa->nis;
        $this->jenjang = $siswa->jenjang;
        $this->kelas_id = $siswa->kelas_id;
        $this->status = $siswa->status;

        $this->tempat_lahir = $siswa->tempat_lahir;
        $this->tanggal_lahir = $siswa->tanggal_lahir;
        $this->agama = $siswa->agama;
        $this->alamat = $siswa->alamat;
        $this->existingFoto = $siswa->foto;

        $this->nama_ayah = $siswa->nama_ayah;
        $this->nama_ibu = $siswa->nama_ibu;
        $this->pekerjaan_ayah = $siswa->pekerjaan_ayah;
        $this->pekerjaan_ibu = $siswa->pekerjaan_ibu;
        $this->no_telp_ortu = $siswa->no_telp_ortu;

        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'siswa-form');
    }

    public function delete($id)
    {
        $siswa = Siswa::findOrFail($id);
        // We'll follow "soft" approach by changing status or just deleting Siswa record.
        // But migration has cascadeOnDelete for user_id.
        // User said: "Use deactivation/status instead of permanent deletion."
        // So for "Delete" button, maybe we just deactivate?
        // Let's implement deactivation for "Delete" if preferred, or just remove Siswa but keep user.
        // Actually, user said: "Gunakan sistem Status Dinonaktifkan atau Soft Deletes."

        $siswa->update(['status' => 'Pindah']); // Or similar
        $this->dispatch('notify', ['type' => 'warning', 'message' => 'Status siswa diubah menjadi Pindah (Nonaktif).']);
    }

    public function render()
    {
        $query = Siswa::with(['user', 'kelas'])
            ->when($this->search, function ($q) {
                $q->where('nis', 'like', '%'.$this->search.'%')
                    ->orWhere('nisn', 'like', '%'.$this->search.'%')
                    ->orWhereHas('user', function ($qu) {
                        $qu->where('name', 'like', '%'.$this->search.'%');
                    });
            })
            ->when($this->filterJenjang, function ($q) {
                $q->where('jenjang', $this->filterJenjang);
            })
            ->when($this->filterKelas, function ($q) {
                $q->where('kelas_id', $this->filterKelas);
            })
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            });

        $availableClasses = Kelas::when($this->filterJenjang, function ($q) {
            $q->where('jenjang', $this->filterJenjang);
        })->get();

        $formClasses = Kelas::where('jenjang', $this->jenjang)->get();

        return view('livewire.admin.civitas.data-siswa-index', [
            'siswas' => $query->latest()->paginate($this->perPage),
            'availableClasses' => $availableClasses,
            'formClasses' => $formClasses,
            'stats' => [
                'total' => Siswa::count(),
                'aktif' => Siswa::where('status', 'Aktif')->count(),
                'lulus' => Siswa::where('status', 'Lulus')->count(),
                'nonaktif' => Siswa::whereIn('status', ['Pindah', 'Dikeluarkan'])->count(),
            ],
        ]);
    }
}
