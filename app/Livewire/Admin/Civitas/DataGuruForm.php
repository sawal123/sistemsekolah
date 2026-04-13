<?php

namespace App\Livewire\Admin\Civitas;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

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

    public function mount(Guru $guru = null)
    {
        if ($guru && $guru->exists) {
            $this->guru = $guru;
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
        }
    }

    public function save()
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'required|date',
            'agama' => 'nullable|string',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|string|max:20',
            'jabatan' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ];

        if ($this->guru && $this->guru->exists) {
            $rules['nip'] = 'required|string|unique:gurus,nip,' . $this->guru->id;
            $rules['email'] = 'nullable|email|unique:users,email,' . $this->guru->user_id;
        } else {
            $rules['nip'] = 'required|string|unique:gurus,nip';
            $rules['email'] = 'nullable|email|unique:users,email';
        }

        $this->validate($rules);

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
            ]);

            $this->guru->update([
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
            ]);

            // Assign Spatie Role
            if (\Spatie\Permission\Models\Role::where('name', 'guru')->exists()) {
                $user->assignRole('guru');
            }

            Guru::create([
                'user_id' => $user->id,
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
        return view('livewire.admin.civitas.data-guru-form');
    }
}
