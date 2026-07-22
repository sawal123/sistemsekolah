<?php

namespace App\Livewire\Landing;

use App\Models\Guru;
use App\Models\Setting;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.landing')]
class StaffingPage extends Component
{
    public string $section = 'guru';

    public function mount(string $section = 'guru'): void
    {
        abort_unless(in_array($section, ['guru', 'tata-usaha', 'struktur-organisasi'], true), 404);
        $this->section = $section;
    }

    public function render()
    {
        $settings = Setting::pluck('value', 'key');
        $teachers = Guru::with(['user', 'kelas'])
            ->whereHas('user', fn ($query) => $query->where('is_active', true))
            ->orderBy('jabatan')
            ->get();

        $staff = User::with('roles')
            ->where('is_active', true)
            ->whereDoesntHave('siswa')
            ->whereDoesntHave('guru')
            ->whereNotNull('staff_position')
            ->orderBy('name')
            ->get();

        return view('livewire.landing.staffing-page', [
            'settings' => $settings,
            'teachers' => $teachers,
            'staff' => $staff,
        ])->title(match ($this->section) {
            'guru' => 'Daftar Guru',
            'tata-usaha' => 'Tata Usaha',
            default => 'Struktur Organisasi',
        });
    }

}
