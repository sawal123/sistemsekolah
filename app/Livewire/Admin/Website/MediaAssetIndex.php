<?php

namespace App\Livewire\Admin\Website;

use App\Models\MediaAsset;
use App\Services\MediaAssetService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Asset Media')]
class MediaAssetIndex extends Component
{
    use WithFileUploads, WithPagination;

    public array $uploads = [];
    public string $search = '';
    public ?int $editingId = null;
    public string $altText = '';
    public ?int $idBeingDeleted = null;

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function upload(MediaAssetService $media): void
    {
        $this->validate([
            'uploads' => ['required', 'array', 'min:1', 'max:20'],
            'uploads.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        foreach ($this->uploads as $file) {
            $media->store($file, auth()->user());
        }

        $count = count($this->uploads);
        $this->reset('uploads');
        $this->dispatch('notify', ['type' => 'success', 'message' => $count . ' gambar berhasil ditambahkan ke Asset Media.']);
    }

    public function editAlt(int $id): void
    {
        $asset = MediaAsset::findOrFail($id);
        $this->editingId = $asset->id;
        $this->altText = $asset->alt_text ?? '';
        $this->dispatch('open-modal', 'media-alt-form');
    }

    public function saveAlt(): void
    {
        $this->validate(['altText' => ['nullable', 'string', 'max:180']]);
        MediaAsset::findOrFail($this->editingId)->update(['alt_text' => $this->altText ?: null]);
        $this->closeAltModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Teks alternatif gambar diperbarui.']);
    }

    public function confirmDelete(int $id): void
    {
        $this->idBeingDeleted = $id;
        $this->dispatch('open-modal', 'delete-media-asset');
    }

    public function delete(MediaAssetService $media): void
    {
        $asset = MediaAsset::findOrFail($this->idBeingDeleted);
        $usages = $media->usages($asset);

        if ($usages !== []) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gambar masih digunakan pada: ' . implode(', ', $usages) . '.']);
            return;
        }

        $media->delete($asset);
        $this->idBeingDeleted = null;
        $this->dispatch('close-modal', 'delete-media-asset');
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Asset media berhasil dihapus.']);
    }

    public function closeAltModal(): void
    {
        $this->reset('editingId', 'altText');
        $this->dispatch('close-modal', 'media-alt-form');
    }

    public function render()
    {
        $assets = MediaAsset::query()
            ->with('uploader:id,name')
            ->when($this->search !== '', fn ($query) => $query->where(function ($query) {
                $query->where('original_name', 'like', '%' . $this->search . '%')
                    ->orWhere('alt_text', 'like', '%' . $this->search . '%');
            }))
            ->latest()
            ->paginate(24);

        return view('livewire.admin.website.media-asset-index', compact('assets'));
    }
}
