<?php

namespace App\Livewire\Admin\Profile;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Pengaturan')]
class SettingsIndex extends Component
{
    // Form Ubah Password
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // Pengaturan KBM (Admin Only)
    public bool $isAdmin = false;
    public int $durasi_jam_pelajaran = 45;
    public string $jam_mulai_pelajaran = '07:15';

    public function mount(): void
    {
        $user = auth()->user();
        $this->isAdmin = $user && $user->hasRole('admin');

        if ($this->isAdmin) {
            $settings = Setting::whereIn('key', ['durasi_jam_pelajaran', 'jam_mulai_pelajaran'])->pluck('value', 'key');
            $this->durasi_jam_pelajaran = (int) ($settings->get('durasi_jam_pelajaran') ?? 45);
            $this->jam_mulai_pelajaran = (string) ($settings->get('jam_mulai_pelajaran') ?? '07:15');
        }
    }

    public function updatePassword(): void
    {
        $user = User::findOrFail(auth()->id());

        $this->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'new_password.different' => 'Kata sandi baru harus berbeda dari kata sandi saat ini.',
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Kata sandi saat ini yang Anda masukkan salah.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch('notify',
            type: 'success',
            title: 'Kata Sandi Diperbarui',
            message: 'Kata sandi akun Anda berhasil diganti.',
        );
    }

    public function updateKbmSettings(): void
    {
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Akses ditolak.');

        $this->validate([
            'durasi_jam_pelajaran' => 'required|integer|min:20|max:120',
            'jam_mulai_pelajaran' => 'required|date_format:H:i',
        ], [
            'durasi_jam_pelajaran.min' => 'Durasi minimal 20 menit per jam pelajaran.',
            'durasi_jam_pelajaran.max' => 'Durasi maksimal 120 menit per jam pelajaran.',
            'jam_mulai_pelajaran.date_format' => 'Format jam mulai harus JJ:MM (contoh: 07:15).',
        ]);

        Setting::updateOrCreate(
            ['key' => 'durasi_jam_pelajaran'],
            ['value' => $this->durasi_jam_pelajaran]
        );

        Setting::updateOrCreate(
            ['key' => 'jam_mulai_pelajaran'],
            ['value' => $this->jam_mulai_pelajaran]
        );

        $this->dispatch('notify',
            type: 'success',
            title: 'Pengaturan Tersimpan',
            message: 'Standar waktu dan durasi KBM telah diperbarui.',
        );
    }

    public function render()
    {
        return view('livewire.admin.profile.settings-index', [
            'user' => auth()->user(),
        ]);
    }
}
