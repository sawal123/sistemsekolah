<?php

namespace App\Livewire\Admin\Website;

use App\Models\Gallery;
use App\Models\Slider;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Galeri & Slider')]
class GaleriSliderIndex extends Component
{
    use WithFileUploads;

    public string $tab = 'slider';

    public bool $isSliderModalOpen = false;
    public bool $isGalleryModalOpen = false;
    public ?int $editSliderId = null;
    public ?int $editGalleryId = null;
    public ?int $idBeingDeleted = null;
    public string $deleteType = '';

    public string $sliderJudul = '';
    public string $sliderDeskripsi = '';
    public bool $sliderIsActive = true;
    public $sliderFoto;
    public ?string $sliderExistingFoto = null;

    public string $galleryJudul = '';
    public string $galleryDeskripsi = '';
    public $galleryFoto;
    public ?string $galleryExistingFoto = null;

    public function mount(): void
    {
        $this->normalizeSliderOrder();
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.admin.website.galeri-slider-index', [
            'sliders' => Slider::orderBy('urutan')->orderByDesc('created_at')->get(),
            'galleries' => Gallery::latest()->get(),
        ]);
    }

    public function openSliderModal(): void
    {
        $this->resetSliderForm();
        $this->isSliderModalOpen = true;
        $this->dispatch('open-modal', 'slider-form');
    }

    public function editSlider(int $id): void
    {
        $slider = Slider::findOrFail($id);

        $this->editSliderId = $slider->id;
        $this->sliderJudul = $slider->judul;
        $this->sliderDeskripsi = $slider->deskripsi ?? '';
        $this->sliderIsActive = (bool) $slider->is_active;
        $this->sliderExistingFoto = $slider->foto;
        $this->sliderFoto = null;

        $this->isSliderModalOpen = true;
        $this->dispatch('open-modal', 'slider-form');
    }

    public function saveSlider(): void
    {
        $this->validate([
            'sliderJudul' => 'required|string|max:150',
            'sliderDeskripsi' => 'nullable|string|max:500',
            'sliderIsActive' => 'boolean',
            'sliderFoto' => [$this->editSliderId ? 'nullable' : 'required', 'image', 'max:4096'],
        ]);

        $fotoPath = $this->sliderExistingFoto;

        if ($this->sliderFoto) {
            $fotoPath = $this->sliderFoto->store('website/sliders', 'public');

            if ($this->sliderExistingFoto) {
                Storage::disk('public')->delete($this->sliderExistingFoto);
            }
        }

        Slider::updateOrCreate(
            ['id' => $this->editSliderId],
            [
                'judul' => $this->sliderJudul,
                'deskripsi' => $this->sliderDeskripsi ?: null,
                'foto' => $fotoPath,
                'urutan' => $this->editSliderId ? Slider::find($this->editSliderId)?->urutan ?? 0 : ((int) Slider::max('urutan') + 1),
                'is_active' => $this->sliderIsActive,
            ]
        );

        $this->closeModal();
        $this->normalizeSliderOrder();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Slider berhasil disimpan.']);
    }

    public function moveSliderUp(int $id): void
    {
        $this->normalizeSliderOrder();
        $current = Slider::findOrFail($id);
        $previous = Slider::where('urutan', '<', $current->urutan)->orderByDesc('urutan')->first();

        if ($previous) {
            [$current->urutan, $previous->urutan] = [$previous->urutan, $current->urutan];
            $current->save();
            $previous->save();
        }
    }

    public function moveSliderDown(int $id): void
    {
        $this->normalizeSliderOrder();
        $current = Slider::findOrFail($id);
        $next = Slider::where('urutan', '>', $current->urutan)->orderBy('urutan')->first();

        if ($next) {
            [$current->urutan, $next->urutan] = [$next->urutan, $current->urutan];
            $current->save();
            $next->save();
        }
    }

    public function toggleSliderStatus(int $id): void
    {
        $slider = Slider::findOrFail($id);
        $slider->update(['is_active' => ! $slider->is_active]);
    }

    public function openGalleryModal(): void
    {
        $this->resetGalleryForm();
        $this->isGalleryModalOpen = true;
        $this->dispatch('open-modal', 'gallery-form');
    }

    public function editGallery(int $id): void
    {
        $gallery = Gallery::findOrFail($id);

        $this->editGalleryId = $gallery->id;
        $this->galleryJudul = $gallery->judul;
        $this->galleryDeskripsi = $gallery->deskripsi ?? '';
        $this->galleryExistingFoto = $gallery->foto;
        $this->galleryFoto = null;

        $this->isGalleryModalOpen = true;
        $this->dispatch('open-modal', 'gallery-form');
    }

    public function saveGallery(): void
    {
        $this->validate([
            'galleryJudul' => 'required|string|max:150',
            'galleryDeskripsi' => 'nullable|string|max:500',
            'galleryFoto' => [$this->editGalleryId ? 'nullable' : 'required', 'image', 'max:4096'],
        ]);

        $fotoPath = $this->galleryExistingFoto;

        if ($this->galleryFoto) {
            $fotoPath = $this->galleryFoto->store('website/galleries', 'public');

            if ($this->galleryExistingFoto) {
                Storage::disk('public')->delete($this->galleryExistingFoto);
            }
        }

        Gallery::updateOrCreate(
            ['id' => $this->editGalleryId],
            [
                'judul' => $this->galleryJudul,
                'deskripsi' => $this->galleryDeskripsi ?: null,
                'foto' => $fotoPath,
            ]
        );

        $this->closeModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Foto galeri berhasil disimpan.']);
    }

    public function confirmDelete(string $type, int $id): void
    {
        $this->deleteType = $type;
        $this->idBeingDeleted = $id;
        $this->dispatch('open-modal', 'confirm-delete-modal');
    }

    public function delete(): void
    {
        if (! $this->idBeingDeleted) {
            return;
        }

        if ($this->deleteType === 'slider') {
            $slider = Slider::findOrFail($this->idBeingDeleted);
            if ($slider->foto) {
                Storage::disk('public')->delete($slider->foto);
            }
            $slider->delete();
            $this->normalizeSliderOrder();
        }

        if ($this->deleteType === 'gallery') {
            $gallery = Gallery::findOrFail($this->idBeingDeleted);
            if ($gallery->foto) {
                Storage::disk('public')->delete($gallery->foto);
            }
            $gallery->delete();
        }

        $this->closeModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Data berhasil dihapus.']);
    }

    public function closeModal(): void
    {
        $this->isSliderModalOpen = false;
        $this->isGalleryModalOpen = false;
        $this->idBeingDeleted = null;
        $this->deleteType = '';
        $this->dispatch('close-modal', 'slider-form');
        $this->dispatch('close-modal', 'gallery-form');
        $this->dispatch('close-modal', 'confirm-delete-modal');
    }

    private function resetSliderForm(): void
    {
        $this->editSliderId = null;
        $this->sliderJudul = '';
        $this->sliderDeskripsi = '';
        $this->sliderIsActive = true;
        $this->sliderFoto = null;
        $this->sliderExistingFoto = null;
        $this->resetValidation();
    }

    private function resetGalleryForm(): void
    {
        $this->editGalleryId = null;
        $this->galleryJudul = '';
        $this->galleryDeskripsi = '';
        $this->galleryFoto = null;
        $this->galleryExistingFoto = null;
        $this->resetValidation();
    }

    private function normalizeSliderOrder(): void
    {
        Slider::orderBy('urutan')->orderBy('id')->get()
            ->each(fn (Slider $slider, int $index) => $slider->update(['urutan' => $index + 1]));
    }
}
