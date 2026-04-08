<?php

namespace App\Livewire\Admin\Website;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Pengaturan Umum')]
class PengaturanUmumIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.website.pengaturan-umum-index');
    }
}
