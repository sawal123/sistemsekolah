<?php

namespace App\Livewire\Components;

use App\Models\Unit;
use App\Support\UnitContext;
use Livewire\Component;

class UnitSwitcher extends Component
{
    public string $selectedUnitId = '';

    public function mount(): void
    {
        $activeId = UnitContext::activeUnitId();
        $this->selectedUnitId = $activeId ? (string) $activeId : '';
    }

    public function updatedSelectedUnitId($value): void
    {
        $user = auth()->user();

        if (! $user || ! $user->isSuperAdmin()) {
            return;
        }

        UnitContext::setSuperAdminUnit($value !== '' ? (int) $value : null);

        $this->redirect(request()->header('Referer') ?: route('dashboard'), navigate: true);
    }

    public function render()
    {
        $user = auth()->user();
        $isSuperAdmin = (bool) ($user?->isSuperAdmin());
        $units = $isSuperAdmin ? Unit::active()->get(['id', 'kode', 'nama']) : collect();
        $lockedUnit = ! $isSuperAdmin ? UnitContext::activeUnit($user) : null;

        return view('livewire.components.unit-switcher', [
            'isSuperAdmin' => $isSuperAdmin,
            'units' => $units,
            'lockedUnit' => $lockedUnit,
        ]);
    }
}
