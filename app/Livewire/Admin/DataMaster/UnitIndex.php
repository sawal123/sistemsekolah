<?php

namespace App\Livewire\Admin\DataMaster;

use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('Master Unit Sekolah')]
class UnitIndex extends Component
{
    use WithFileUploads;

    public ?int $editId = null;

    public bool $isModalOpen = false;

    public string $kode = '';

    public string $nama = '';

    public string $npsn = '';

    public int $tingkat_awal = 1;

    public int $tingkat_akhir = 6;

    public bool $punya_jurusan = false;

    public string $kepala_sekolah = '';

    public string $nip_kepala_sekolah = '';

    public string $alamat = '';

    public string $telepon = '';

    public string $email = '';

    public bool $is_active = true;

    public int $urutan = 0;

    public $logo = null;

    public ?string $existingLogo = null;

    public function mount(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);
    }

    public function openModal(): void
    {
        $this->resetValidation();
        $this->reset([
            'editId',
            'kode',
            'nama',
            'npsn',
            'tingkat_awal',
            'tingkat_akhir',
            'punya_jurusan',
            'kepala_sekolah',
            'nip_kepala_sekolah',
            'alamat',
            'telepon',
            'email',
            'logo',
            'existingLogo',
        ]);
        $this->tingkat_awal = 1;
        $this->tingkat_akhir = 6;
        $this->is_active = true;
        $this->urutan = (int) Unit::max('urutan') + 1;
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'unit-form');
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $unit = Unit::findOrFail($id);

        $this->editId = $unit->id;
        $this->kode = $unit->kode;
        $this->nama = $unit->nama;
        $this->npsn = (string) ($unit->npsn ?? '');
        $this->tingkat_awal = (int) $unit->tingkat_awal;
        $this->tingkat_akhir = (int) $unit->tingkat_akhir;
        $this->punya_jurusan = (bool) $unit->punya_jurusan;
        $this->kepala_sekolah = (string) ($unit->kepala_sekolah ?? '');
        $this->nip_kepala_sekolah = (string) ($unit->nip_kepala_sekolah ?? '');
        $this->alamat = (string) ($unit->alamat ?? '');
        $this->telepon = (string) ($unit->telepon ?? '');
        $this->email = (string) ($unit->email ?? '');
        $this->is_active = (bool) $unit->is_active;
        $this->urutan = (int) $unit->urutan;
        $this->existingLogo = $unit->logo;
        $this->logo = null;

        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'unit-form');
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->dispatch('close-modal', 'unit-form');
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $this->kode = strtoupper(trim($this->kode));

        $this->validate([
            'kode' => 'required|string|max:20|unique:units,kode,' . ($this->editId ?: 'NULL'),
            'nama' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:30',
            'tingkat_awal' => 'required|integer|min:1|max:12',
            'tingkat_akhir' => 'required|integer|gte:tingkat_awal|max:13',
            'punya_jurusan' => 'boolean',
            'kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:1000',
            'telepon' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
            'urutan' => 'integer|min:0|max:999',
            'logo' => 'nullable|image|max:2048',
        ]);

        $logoPath = $this->existingLogo;
        if ($this->logo) {
            if ($this->existingLogo) {
                Storage::disk('public')->delete($this->existingLogo);
            }
            $logoPath = $this->logo->store('units/logos', 'public');
        }

        Unit::updateOrCreate(
            ['id' => $this->editId],
            [
                'kode' => $this->kode,
                'nama' => $this->nama,
                'npsn' => $this->npsn ?: null,
                'tingkat_awal' => $this->tingkat_awal,
                'tingkat_akhir' => $this->tingkat_akhir,
                'punya_jurusan' => $this->punya_jurusan,
                'kepala_sekolah' => $this->kepala_sekolah ?: null,
                'nip_kepala_sekolah' => $this->nip_kepala_sekolah ?: null,
                'alamat' => $this->alamat ?: null,
                'telepon' => $this->telepon ?: null,
                'email' => $this->email ?: null,
                'logo' => $logoPath,
                'is_active' => $this->is_active,
                'urutan' => $this->urutan,
            ]
        );

        $this->dispatch('notify', type: 'success', message: $this->editId ? 'Unit sekolah berhasil diperbarui.' : 'Unit sekolah baru berhasil ditambahkan.');
        $this->closeModal();
    }

    public function toggleActive(int $id): void
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        $unit = Unit::findOrFail($id);
        $unit->update(['is_active' => ! $unit->is_active]);

        $this->dispatch('notify', type: 'success', message: "Status unit {$unit->kode} berhasil diubah.");
    }

    public function render()
    {
        $units = Unit::withCount(['siswas', 'gurus', 'kelas'])
            ->orderBy('urutan')
            ->orderBy('nama')
            ->get();

        return view('livewire.admin.data-master.unit-index', [
            'units' => $units,
        ]);
    }
}
