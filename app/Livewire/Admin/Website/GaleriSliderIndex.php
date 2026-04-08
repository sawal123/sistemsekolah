<?php

namespace App\Livewire\Admin\Website;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Galeri & Slider')]
class GaleriSliderIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.website.galeri-slider-index');
    }
}
