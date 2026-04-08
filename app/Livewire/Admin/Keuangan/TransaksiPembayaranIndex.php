<?php

namespace App\Livewire\Admin\Keuangan;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Transaksi Pembayaran')]
class TransaksiPembayaranIndex extends Component
{
    public function render()
    {
        return view('livewire.admin.keuangan.transaksi-pembayaran-index');
    }
}
