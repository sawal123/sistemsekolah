<?php

namespace App\Livewire\Admin\Kbm;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Rekap Absensi')]
class RekapAbsensiIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.kbm.rekap-absensi-index');
    }
}
