<?php

namespace App\Livewire\Admin\Website;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Visi, Misi & Sambutan')]
class VisiMisiIndex extends Component
{
    use WithFileUploads;

    // Vision and Mission Fields
    public string $school_vision = '';
    public string $school_mission = '';

    // Principal Greeting Fields
    public string $principal_name = '';
    public string $principal_nip = '';
    public string $principal_title = 'Kepala Sekolah';
    public string $principal_greeting = '';

    // Image Upload Fields
    public $principalImageFile;
    public ?string $existingPrincipalImage = null;

    public function mount(): void
    {
        $settings = Setting::pluck('value', 'key');

        $this->school_vision = (string) ($settings->get('school_vision') ?? '');
        $this->school_mission = (string) ($settings->get('school_mission') ?? '');
        $this->principal_name = (string) ($settings->get('principal_name') ?? '');
        $this->principal_nip = (string) ($settings->get('principal_nip') ?? '');
        $this->principal_title = (string) ($settings->get('principal_title') ?? 'Kepala Sekolah');
        $this->principal_greeting = (string) ($settings->get('principal_greeting') ?? '');
        $this->existingPrincipalImage = $settings->get('principal_image');
    }

    public function save(): void
    {
        $this->validate([
            'school_vision' => 'required|string|max:1500',
            'school_mission' => 'required|string|max:2500',
            'principal_name' => 'required|string|max:150',
            'principal_nip' => 'nullable|string|max:50',
            'principal_title' => 'required|string|max:100',
            'principal_greeting' => 'required|string|max:3000',
            'principalImageFile' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $imagePath = $this->existingPrincipalImage;
        if ($this->principalImageFile) {
            $imagePath = $this->principalImageFile->store('principal', 'public');
            $this->deleteOldFile($this->existingPrincipalImage);
        }

        $settings = [
            'school_vision' => $this->school_vision,
            'school_mission' => $this->school_mission,
            'principal_name' => $this->principal_name,
            'principal_nip' => $this->principal_nip,
            'principal_title' => $this->principal_title,
            'principal_greeting' => $this->principal_greeting,
            'principal_image' => $imagePath,
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?: null]);
        }

        $this->existingPrincipalImage = $imagePath;
        $this->principalImageFile = null;

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Visi, misi, dan sambutan berhasil disimpan.']);
    }

    public function deletePrincipalImage(): void
    {
        if ($this->existingPrincipalImage) {
            $this->deleteOldFile($this->existingPrincipalImage);
            Setting::updateOrCreate(['key' => 'principal_image'], ['value' => null]);
            $this->existingPrincipalImage = null;
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Foto kepala sekolah berhasil dihapus.']);
        }
    }

    public function render()
    {
        return view('livewire.admin.website.visi-misi-index');
    }

    private function deleteOldFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
