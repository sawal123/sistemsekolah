<?php

namespace App\Livewire\Landing;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.landing')]
#[Title('Kontak Sekolah')]
class ContactPage extends Component
{
    public function render()
    {
        return view('livewire.landing.contact-page', [
            'settings' => Setting::pluck('value', 'key'),
        ]);
    }
}
