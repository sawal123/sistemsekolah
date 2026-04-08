<?php

namespace App\Livewire\Admin\DataMaster;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Mata Pelajaran')]
class MataPelajaranIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.data-master.mata-pelajaran-index');
    }
}
