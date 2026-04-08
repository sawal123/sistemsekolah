<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Master Data SPP')]
class MasterSppIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.keuangan.master-spp-index');
    }
}
