<?php

namespace App\Livewire\Admin\DataMaster;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Data Kelas')]
class DataKelasIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.data-master.data-kelas-index');
    }
}
