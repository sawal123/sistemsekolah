<?php

namespace App\Livewire\Student;

use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.student')]
#[Title('Profil Saya')]
class StudentProfile extends Component
{
    public string $alamat = '';
    public string $noTelpOrtu = '';
    public string $currentPassword = '';
    public string $password = '';
    public string $passwordConfirmation = '';

    public function mount(): void
    {
        $siswa = auth()->user()->siswa;
        $this->alamat = $siswa->alamat ?? '';
        $this->noTelpOrtu = $siswa->no_telp_ortu ?? '';
    }

    public function saveContact(): void
    {
        $this->validate([
            'alamat' => ['nullable', 'string', 'max:1000'],
            'noTelpOrtu' => ['nullable', 'string', 'max:30'],
        ]);

        auth()->user()->siswa->update([
            'alamat' => $this->alamat ?: null,
            'no_telp_ortu' => $this->noTelpOrtu ?: null,
        ]);
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Kontak berhasil diperbarui.']);
    }

    public function updatePassword(): void
    {
        $this->validate([
            'currentPassword' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'same:passwordConfirmation'],
            'passwordConfirmation' => ['required'],
        ]);

        auth()->user()->update(['password' => Hash::make($this->password)]);
        $this->reset('currentPassword', 'password', 'passwordConfirmation');
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Kata sandi berhasil diperbarui.']);
    }

    public function render()
    {
        return view('livewire.student.student-profile', ['siswa' => auth()->user()->siswa->load(['kelas', 'jurusan'])]);
    }
}
