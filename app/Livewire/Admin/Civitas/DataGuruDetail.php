<?php

namespace App\Livewire\Admin\Civitas;

use App\Models\Guru;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Detail Guru')]
class DataGuruDetail extends Component
{
    public Guru $guru;

    public function mount(Guru $guru)
    {
        $this->guru = $guru->load(['user', 'kelas', 'jadwals.mapel', 'jadwals.kelas']);
    }

    public function render()
    {
        return view('livewire.admin.civitas.data-guru-detail');
    }
}
