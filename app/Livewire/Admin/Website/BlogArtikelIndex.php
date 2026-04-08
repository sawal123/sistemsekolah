<?php

namespace App\Livewire\Admin\Website;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Blog / Artikel')]
class BlogArtikelIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.website.blog-artikel-index');
    }
}
