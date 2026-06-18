<?php

namespace App\Livewire\Admin\Website;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Struktur Organisasi')]
class StructureOrganizationIndex extends Component
{
    use WithFileUploads;

    public $organizationImage;
    public ?string $existingImage = null;

    public function mount(): void
    {
        $this->existingImage = Setting::where('key', 'organization_structure_image')->value('value');
    }

    public function save(): void
    {
        $this->validate([
            'organizationImage' => 'required|image|max:5120',
        ]);

        $path = $this->organizationImage->store('organization', 'public');

        if ($this->existingImage && $this->existingImage !== 'organization/structure-organisasi-dummy.png') {
            Storage::disk('public')->delete($this->existingImage);
        }

        Setting::updateOrCreate(
            ['key' => 'organization_structure_image'],
            ['value' => $path]
        );

        $this->existingImage = $path;
        $this->organizationImage = null;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Gambar struktur organisasi berhasil diperbarui.',
        ]);
    }

    public function resetToDefault(): void
    {
        if ($this->existingImage && $this->existingImage !== 'organization/structure-organisasi-dummy.png') {
            Storage::disk('public')->delete($this->existingImage);
        }

        Setting::updateOrCreate(
            ['key' => 'organization_structure_image'],
            ['value' => 'organization/structure-organisasi-dummy.png']
        );

        $this->existingImage = 'organization/structure-organisasi-dummy.png';
        $this->organizationImage = null;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Struktur organisasi dikembalikan ke gambar dummy.',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.website.structure-organization-index');
    }
}
