<?php

namespace App\Livewire\Admin\Civitas;

use App\Models\Guru;
use App\Models\Unit;
use App\Models\User;
use App\Support\UnitContext;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
#[Title('Form Data Guru')]
class DataGuruForm extends Component
{
    use WithFileUploads;

    public ?Guru $guru = null;

    public $nip;
    public $nama_lengkap;
    public $tempat_lahir;
    public $tanggal_lahir;
    public $agama;
    public $alamat;
    public $no_telp;
    public $jabatan;
    public $email;
    public $foto;
    public $existing_foto;
    public $unit_id;

    public function mount(?Guru $guru = null)
    {
        if ($guru && $guru->exists) {
            UnitContext::authorizeUnit(auth()->user(), $guru->unit_id);
            $this->guru = $guru;
            $this->unit_id = $guru->unit_id;
            $this->nip = $guru->nip;
            $this->nama_lengkap = $guru->user->name ?? '';
            $this->tempat_lahir = $guru->tempat_lahir;
            $this->tanggal_lahir = $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->format('Y-m-d') : null;
            $this->agama = $guru->agama;
            $this->alamat = $guru->alamat;
            $this->no_telp = $guru->no_telp;
            $this->jabatan = $guru->jabatan;
            $this->email = $guru->user->email ?? '';
            $this->existing_foto = $guru->foto;
        } else {
            $this->unit_id = UnitContext::activeUnitId();
        }
    }

    public function save()
    {
        $authUser = auth()->user();
        if ($authUser && $authUser->isUnitAdmin()) {
            $this->unit_id = $authUser->effectiveUnitId();
        }

        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'unit_id' => 'nullable|exists:units,id',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'required|date',
            'agama' => 'nullable|string',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'jabatan' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ];

        if ($this->guru && $this->guru->exists) {
            UnitContext::authorizeUnit($authUser, $this->guru->unit_id);
            $rules['nip'] = 'required|string|unique:gurus,nip,' . $this->guru->id;
            $rules['email'] = 'nullable|email|unique:users,email,' . $this->guru->user_id;
        } else {
            $rules['nip'] = 'required|string|unique:gurus,nip';
            $rules['email'] = 'nullable|email|unique:users,email';
        }

        $this->validate($rules);
        $resolvedUnitId = $this->unit_id ?: null;
        UnitContext::authorizeUnit($authUser, $resolvedUnitId, $authUser?->isSuperAdmin() ?? false);

        $email = $this->email;
        if (empty($email)) {
            $email = strtolower($this->nip) . '@sekolah.sch.id';
        }

        $fotoPath = $this->existing_foto;
        if ($this->foto) {
            $fotoPath = $this->foto->store('guru_photos', 'public');
        }

        if ($this->guru && $this->guru->exists) {
            // Update
            $user = $this->guru->user;
            $user->update([
                'name' => $this->nama_lengkap,
                'email' => $email,
                'unit_id' => $resolvedUnitId,
            ]);
            $this->ensureGuruRole($user);

            $this->guru->update([
                'unit_id' => $resolvedUnitId,
                'nip' => $this->nip,
                'tempat_lahir' => $this->tempat_lahir,
                'tanggal_lahir' => $this->tanggal_lahir,
                'agama' => $this->agama,
                'alamat' => $this->alamat,
                'no_telp' => $this->no_telp,
                'jabatan' => $this->jabatan,
                'foto' => $fotoPath,
            ]);

            session()->flash('notify', ['type' => 'success', 'message' => 'Data guru berhasil diperbarui.']);
        } else {
            // Create
            $password = \Carbon\Carbon::parse($this->tanggal_lahir)->format('dmY');

            $user = User::create([
                'name' => $this->nama_lengkap,
                'email' => $email,
                'password' => Hash::make($password),
                'staff_position' => null,
                'unit_id' => $resolvedUnitId,
            ]);

            $this->ensureGuruRole($user);

            Guru::create([
                'user_id' => $user->id,
                'unit_id' => $resolvedUnitId,
                'nip' => $this->nip,
                'tempat_lahir' => $this->tempat_lahir,
                'tanggal_lahir' => $this->tanggal_lahir,
                'agama' => $this->agama,
                'alamat' => $this->alamat,
                'no_telp' => $this->no_telp,
                'jabatan' => $this->jabatan,
                'foto' => $fotoPath,
            ]);

            session()->flash('notify', ['type' => 'success', 'message' => 'Data guru berhasil ditambahkan.']);
        }

        return $this->redirectRoute('admin.civitas.data-guru', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.civitas.data-guru-form', [
            'units' => Unit::active()->get(['id', 'kode', 'nama']),
        ]);
    }

    private function ensureGuruRole(User $user): void
    {
        Role::firstOrCreate([
            'name' => 'guru',
            'guard_name' => config('auth.defaults.guard', 'web'),
        ]);

        if (! $user->hasRole('guru')) {
            $user->assignRole('guru');
        }
    }
}
