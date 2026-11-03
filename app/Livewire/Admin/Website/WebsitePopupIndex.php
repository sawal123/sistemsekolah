<?php

namespace App\Livewire\Admin\Website;

use App\Models\MediaAsset;
use App\Models\WebsitePopup;
use App\Services\MediaAssetService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Modal Pengunjung')]
class WebsitePopupIndex extends Component
{
    use WithFileUploads, WithPagination;

    #[Locked]
    public ?int $editId = null;

    #[Locked]
    public ?string $existingImage = null;

    public bool $isModalOpen = false;
    public string $judul = '';
    public string $link = '';
    public int $displayIntervalHours = 24;
    public bool $isActive = true;
    public $gambar;
    public ?int $mediaAssetId = null;

    public function mount(): void
    {
        $this->authorizeAdmin();
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);
    }

    public function create(): void
    {
        $this->authorizeAdmin();
        $this->resetForm();
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'website-popup-form');
    }

    public function edit(int $id): void
    {
        $this->authorizeAdmin();
        $this->resetForm();
        $popup = WebsitePopup::findOrFail($id);
        $this->editId = $popup->id;
        $this->existingImage = $popup->gambar;
        $this->mediaAssetId = MediaAsset::where('path', $popup->gambar)->value('id');
        $this->judul = $popup->judul;
        $this->link = $popup->link;
        $this->displayIntervalHours = $popup->display_interval_hours;
        $this->isActive = $popup->is_active;
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'website-popup-form');
    }

    public function save(MediaAssetService $media): void
    {
        $this->authorizeAdmin();
        $popup = $this->editId ? WebsitePopup::findOrFail($this->editId) : new WebsitePopup;
        $this->validate([
            'judul' => 'required|string|max:150',
            'link' => 'required|url:http,https|max:2048',
            'displayIntervalHours' => 'required|integer|min:1|max:8760',
            'isActive' => 'boolean',
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'mediaAssetId' => ['nullable', 'exists:media_assets,id'],
        ]);

        if (! $popup->exists && ! $this->gambar && ! $this->mediaAssetId) {
            $this->addError('gambar', 'Pilih gambar dari Asset Media atau unggah gambar baru.');
            return;
        }

        $oldImage = $popup->gambar;
        $newImage = $this->gambar
            ? $media->store($this->gambar, auth()->user(), $this->judul)->path
            : ($this->mediaAssetId ? MediaAsset::findOrFail($this->mediaAssetId)->path : $oldImage);
        $popup->fill([
            'judul' => $this->judul,
            'gambar' => $newImage,
            'link' => $this->link,
            'display_interval_hours' => $this->displayIntervalHours,
            'is_active' => $this->isActive,
        ])->save();

        $this->closeModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Modal pengunjung berhasil disimpan.']);
    }

    public function toggleStatus(int $id): void
    {
        $this->authorizeAdmin();
        $popup = WebsitePopup::findOrFail($id);
        $popup->update(['is_active' => ! $popup->is_active]);
    }

    public function delete(int $id): void
    {
        $this->authorizeAdmin();
        $popup = WebsitePopup::findOrFail($id);
        $popup->delete();
        $this->resetPage();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Modal pengunjung berhasil dihapus.']);
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->dispatch('close-modal', 'website-popup-form');
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editId', 'existingImage', 'judul', 'link', 'displayIntervalHours', 'isActive', 'gambar', 'mediaAssetId']);
        $this->resetValidation();
    }

    public function render()
    {
        $this->authorizeAdmin();

        return view('livewire.admin.website.website-popup-index', [
            'popups' => WebsitePopup::withCount('impressions')->latest('id')->paginate(10),
            'mediaAssets' => MediaAsset::latest()->limit(200)->get(['id', 'original_name', 'path']),
        ]);
    }
}
