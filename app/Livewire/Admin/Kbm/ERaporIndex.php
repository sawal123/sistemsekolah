<?php

namespace App\Livewire\Admin\Kbm;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('e-Rapor')]
class ERaporIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.kbm.e-rapor-index');
    }
}
