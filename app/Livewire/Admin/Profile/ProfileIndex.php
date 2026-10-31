<?php

namespace App\Livewire\Admin\Profile;

use App\Models\Guru;
use App\Models\Rombel;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Profil Saya')]
class ProfileIndex extends Component
{
    // Informasi Akun
    public string $name = '';
    public string $email = '';
    public string $staff_position = '';

    // Informasi Khusus Guru
    public bool $isGuru = false;
    public string $nip = '';
    public string $no_telp = '';
    public string $tempat_lahir = '';
    public string $tanggal_lahir = '';
    public string $agama = '';
    public string $alamat = '';
    public string $jabatan = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->staff_position = $user->staff_position ?? '';

        if ($user->hasRole('guru') && $user->guru) {
            $this->isGuru = true;
            $guru = $user->guru;
            $this->nip = $guru->nip ?? '';
            $this->no_telp = $guru->no_telp ?? '';
            $this->tempat_lahir = $guru->tempat_lahir ?? '';
            $this->tanggal_lahir = $guru->tanggal_lahir ? $guru->tanggal_lahir->format('Y-m-d') : '';
            $this->agama = $guru->agama ?? '';
            $this->alamat = $guru->alamat ?? '';
            $this->jabatan = $guru->jabatan ?? '';
        }
    }

    public function updateProfile(): void
    {
        $userId = auth()->id();

        $rules = [
            'name' => 'required|string|min:3|max:150',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($userId)],
        ];

        if ($this->isGuru) {
            $rules['no_telp'] = 'nullable|string|max:25';
            $rules['tempat_lahir'] = 'nullable|string|max:100';
            $rules['tanggal_lahir'] = 'nullable|date';
            $rules['agama'] = 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya';
            $rules['alamat'] = 'nullable|string|max:500';
        }

        $this->validate($rules);

        $user = User::findOrFail($userId);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        if ($this->isGuru && $user->guru) {
            $user->guru->update([
                'no_telp' => $this->no_telp ?: null,
                'tempat_lahir' => $this->tempat_lahir ?: null,
                'tanggal_lahir' => $this->tanggal_lahir ?: null,
                'agama' => $this->agama ?: null,
                'alamat' => $this->alamat ?: null,
            ]);
        }

        $this->dispatch('notify',
            type: 'success',
            title: 'Profil Diperbarui',
            message: 'Data profil Anda telah berhasil disimpan.',
        );
    }

    public function render()
    {
        $user = auth()->user();
        $taAktif = TahunAjaran::where('is_active', true)->first();

        $kelasWali = null;
        $totalJadwal = 0;

        if ($this->isGuru && $user->guru && $taAktif) {
            $rombel = Rombel::with('kelas')
                ->where('wali_kelas_id', $user->guru->id)
                ->where('tahun_ajaran_id', $taAktif->id)
                ->first();
            $kelasWali = $rombel?->kelas;
            $totalJadwal = $user->guru->jadwals()->where('tahun_ajaran_id', $taAktif->id)->count();
        }

        return view('livewire.admin.profile.profile-index', [
            'user' => $user,
            'taAktif' => $taAktif,
            'kelasWali' => $kelasWali,
            'totalJadwal' => $totalJadwal,
        ]);
    }
}
