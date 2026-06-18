<?php

namespace App\Livewire\Landing;

use App\Models\Fasilitas;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.landing')]
#[Title('Fasilitas Sekolah')]
class FacilitiesPage extends Component
{
    public string $category = '';

    public function render()
    {
        return view('livewire.landing.facilities-page', [
            'facilities' => Fasilitas::query()
                ->when($this->category, fn ($query) => $query->where('kategori', $this->category))
                ->orderBy('kategori')
                ->orderBy('nama_fasilitas')
                ->get(),
            'categories' => Fasilitas::query()->whereNotNull('kategori')->distinct()->orderBy('kategori')->pluck('kategori'),
            'totalUnits' => Fasilitas::sum('jumlah'),
            'goodUnits' => Fasilitas::where('kondisi', 'Baik')->sum('jumlah'),
        ]);
    }
}
