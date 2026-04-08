<?php

namespace App\Livewire\Admin\Alumni;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Jejak Alumni')]
class JejakAlumniIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.alumni.jejak-alumni-index');
    }
}
