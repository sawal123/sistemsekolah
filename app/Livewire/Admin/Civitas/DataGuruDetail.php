<?php

namespace App\Livewire\Admin\Civitas;

use App\Models\Guru;
use App\Models\TahunAjaran;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Detail Guru')]
class DataGuruDetail extends Component
{
    public Guru $guru;
    public ?TahunAjaran $taAktif = null;

    public function mount(Guru $guru)
    {
        $this->taAktif = TahunAjaran::where('is_active', true)->first();
        $taId = $this->taAktif?->id;

        $this->guru = $guru->load([
            'user',
            'kelas',
            'rombels' => fn($q) => $taId ? $q->where('tahun_ajaran_id', $taId)->with('kelas') : $q,
            'jadwals' => fn($q) => $taId 
                ? $q->where('tahun_ajaran_id', $taId)->with(['mapel', 'kelas']) 
                : $q->with(['mapel', 'kelas']),
        ]);
    }

    public function render()
    {
        return view('livewire.admin.civitas.data-guru-detail');
    }
}
