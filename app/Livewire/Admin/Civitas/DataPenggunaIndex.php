<?php

namespace App\Livewire\Admin\Civitas;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Data Pengguna')]
class DataPenggunaIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.civitas.data-pengguna-index');
    }
}
