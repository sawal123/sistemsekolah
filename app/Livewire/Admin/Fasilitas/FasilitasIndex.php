<?php

namespace App\Livewire\Admin\Fasilitas;

use App\Models\Fasilitas;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Fasilitas Sekolah')]
class FasilitasIndex extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Search and Filters
    public string $search = '';
    public string $filterKategori = '';
    public string $filterKondisi = '';
    public int $perPage = 10;

    // Modal state
    public bool $isModalOpen = false;
    public ?int $editId = null;
    public ?int $idBeingDeleted = null;

    // Form fields
    public string $nama_fasilitas = '';
    public string $kategori = 'Ruang Kelas';
    public string $deskripsi = '';
    public int $jumlah = 1;
    public string $kondisi = 'Baik';
    public string $lokasi = '';
    public $foto;
    public ?string $existingFoto = null;

    // Reset pagination when filter changes
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKategori(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKondisi(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Fasilitas::query();

        if ($this->search !== '') {
            $query->where('nama_fasilitas', 'like', '%' . $this->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $this->search . '%');
        }

        if ($this->filterKategori !== '') {
            $query->where('kategori', $this->filterKategori);
        }

        if ($this->filterKondisi !== '') {
            $query->where('kondisi', $this->filterKondisi);
        }

        $fasilitas = $query->latest()->paginate($this->perPage);

        return view('livewire.admin.fasilitas.fasilitas-index', [
            'fasilitas' => $fasilitas,
            'kategoriOptions' => [
                'Ruang Kelas' => 'Ruang Kelas',
                'Laboratorium' => 'Laboratorium',
                'Olahraga' => 'Olahraga',
                'Penunjang' => 'Penunjang',
                'Ibadah' => 'Ibadah',
                'Sanitasi' => 'Sanitasi',
                'Lainnya' => 'Lainnya',
            ],
            'filterKategoriOptions' => [
                '' => 'Semua Kategori',
                'Ruang Kelas' => 'Ruang Kelas',
                'Laboratorium' => 'Laboratorium',
                'Olahraga' => 'Olahraga',
                'Penunjang' => 'Penunjang',
                'Ibadah' => 'Ibadah',
                'Sanitasi' => 'Sanitasi',
                'Lainnya' => 'Lainnya',
            ],
            'kondisiOptions' => [
                'Baik' => 'Baik',
                'Rusak Ringan' => 'Rusak Ringan',
                'Rusak Berat' => 'Rusak Berat',
            ],
            'filterKondisiOptions' => [
                '' => 'Semua Kondisi',
                'Baik' => 'Baik',
                'Rusak Ringan' => 'Rusak Ringan',
                'Rusak Berat' => 'Rusak Berat',
            ],
        ]);
    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'fasilitas-form');
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->idBeingDeleted = null;
        $this->dispatch('close-modal', 'fasilitas-form');
        $this->dispatch('close-modal', 'confirm-delete-modal');
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->nama_fasilitas = '';
        $this->kategori = 'Ruang Kelas';
        $this->deskripsi = '';
        $this->jumlah = 1;
        $this->kondisi = 'Baik';
        $this->lokasi = '';
        $this->foto = null;
        $this->existingFoto = null;
        $this->resetValidation();
    }

    public function edit(int $id): void
    {
        $this->resetForm();
        $item = Fasilitas::findOrFail($id);
        
        $this->editId = $item->id;
        $this->nama_fasilitas = $item->nama_fasilitas;
        $this->kategori = $item->kategori;
        $this->deskripsi = $item->deskripsi ?? '';
        $this->jumlah = $item->jumlah;
        $this->kondisi = $item->kondisi;
        $this->lokasi = $item->lokasi ?? '';
        $this->existingFoto = $item->foto;
        
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'fasilitas-form');
    }

    public function save(): void
    {
        $this->validate([
            'nama_fasilitas' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'deskripsi' => 'nullable|string|max:1000',
            'jumlah' => 'required|integer|min:1',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'lokasi' => 'nullable|string|max:100',
            'foto' => 'nullable|image|max:4096',
        ]);

        $fotoPath = $this->existingFoto;

        if ($this->foto) {
            $fotoPath = $this->foto->store('fasilitas', 'public');

            if ($this->existingFoto) {
                Storage::disk('public')->delete($this->existingFoto);
            }
        }

        Fasilitas::updateOrCreate(
            ['id' => $this->editId],
            [
                'nama_fasilitas' => $this->nama_fasilitas,
                'kategori' => $this->kategori,
                'deskripsi' => $this->deskripsi ?: null,
                'jumlah' => $this->jumlah,
                'kondisi' => $this->kondisi,
                'lokasi' => $this->lokasi ?: null,
                'foto' => $fotoPath,
            ]
        );

        $this->closeModal();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->editId ? 'Fasilitas berhasil diperbarui.' : 'Fasilitas berhasil ditambahkan.',
        ]);
    }

    public function confirmDelete(int $id): void
    {
        $this->idBeingDeleted = $id;
        $this->dispatch('open-modal', 'confirm-delete-modal');
    }

    public function delete(): void
    {
        if ($this->idBeingDeleted) {
            $item = Fasilitas::findOrFail($this->idBeingDeleted);
            
            if ($item->foto) {
                Storage::disk('public')->delete($item->foto);
            }

            $item->delete();
            $this->closeModal();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Fasilitas berhasil dihapus.',
            ]);
        }
    }
}
