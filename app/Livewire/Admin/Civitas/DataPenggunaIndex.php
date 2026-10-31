<?php

namespace App\Livewire\Admin\Civitas;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
#[Title('Data Pengguna')]
class DataPenggunaIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $roleFilter = '';

    public $tab = 'pengguna'; // Tab aktif (pengguna / roles)

    // Properti Modal
    public $isSuspendModalOpen = false;

    public $isResetModalOpen = false;

    public $isCreateUserModalOpen = false;

    public $selectedUserId = null;

    // Properti Pengguna Baru
    public $newUserName = '';

    public $newUserEmail = '';

    public $newUserPassword = '';

    public $newUserRole = 'admin';

    public $newUserStaffPosition = '';

    // Properti Role
    public $newRoleName = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Membuka konfirmasi
    public function confirmSuspend($userId)
    {
        if (auth()->id() === $userId) {
            $this->addError('action', 'Anda tidak dapat mengubah status akun Anda sendiri.');

            return;
        }
        $this->selectedUserId = $userId;
        $this->isSuspendModalOpen = true;
    }

    public function confirmReset($userId)
    {
        if (auth()->id() === $userId) {
            $this->addError('action', 'Setel ulang kata sandi diri sendiri dilarang melalui panel ini.');

            return;
        }
        $this->selectedUserId = $userId;
        $this->isResetModalOpen = true;
    }

    public function toggleSuspend()
    {
        if ($this->selectedUserId) {
            $user = User::find($this->selectedUserId);
            if ($user && auth()->id() !== $user->id) {
                $user->is_active = ! $user->is_active;
                $user->save();
            }
        }
        $this->isSuspendModalOpen = false;
        $this->selectedUserId = null;
    }

    public function resetPassword()
    {
        if ($this->selectedUserId) {
            $user = User::find($this->selectedUserId);
            if ($user && auth()->id() !== $user->id) {
                $user->password = Hash::make('password123');
                $user->save();
                session()->flash('message', "Sandi untuk {$user->name} berhasil di-reset menjadi: password123");
            }
        }
        $this->isResetModalOpen = false;
        $this->selectedUserId = null;
    }

    public function openCreateUserModal()
    {
        $this->resetValidation();
        $this->reset(['newUserName', 'newUserEmail', 'newUserPassword', 'newUserRole', 'newUserStaffPosition']);
        $this->newUserRole = 'admin';
        $this->isCreateUserModalOpen = true;
    }

    public function closeCreateUserModal()
    {
        $this->isCreateUserModalOpen = false;
    }

    public function createUser()
    {
        $this->validate([
            'newUserName' => 'required|string|max:255',
            'newUserEmail' => 'required|email|unique:users,email',
            'newUserPassword' => 'nullable|min:6',
            'newUserRole' => 'required|exists:roles,name',
            'newUserStaffPosition' => 'nullable|string|max:100',
        ]);

        $password = ! empty($this->newUserPassword) ? $this->newUserPassword : 'password123';

        $user = User::create([
            'name' => $this->newUserName,
            'email' => $this->newUserEmail,
            'password' => Hash::make($password),
            'is_active' => true,
            'staff_position' => $this->newUserStaffPosition ?: null,
        ]);

        $user->assignRole($this->newUserRole);

        if ($this->newUserRole === 'guru' && ! $user->guru) {
            Guru::create([
                'user_id' => $user->id,
                'nip' => 'G-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
                'jabatan' => 'Guru Tetap',
            ]);
        }

        $this->dispatch('notify', 
            type: 'success', 
            message: "Pengguna {$user->name} berhasil ditambahkan dengan kata sandi: {$password}"
        );

        $this->closeCreateUserModal();
    }

    public function assignRole($userId, $roleName)
    {
        if (auth()->id() === $userId) {
            $this->addError('action', 'Penguncian diri (Self-lockout) dicegah. Anda tak bisa mengubah role milik sendiri.');

            return;
        }

        $user = User::find($userId);
        if ($user) {
            $user->syncRoles([$roleName]);

            if ($roleName === 'guru' && ! $user->guru) {
                Guru::create([
                    'user_id' => $user->id,
                    'nip' => 'G-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
                    'jabatan' => 'Guru Tetap',
                ]);
            }

            $this->dispatch('notify', 
                type: 'success', 
                message: "Role pengguna {$user->name} berhasil diubah menjadi: " . ucfirst($roleName)
            );
        }
    }

    public function updateStaffPosition(int $userId, string $position): void
    {
        $user = User::whereDoesntHave('siswa')->findOrFail($userId);
        $user->update(['staff_position' => $position !== '' ? $position : null]);

        $this->dispatch('notify', 
            type: 'success', 
            message: 'Jabatan pegawai berhasil diperbarui.'
        );
    }

    public function createRole()
    {
        $this->validate(['newRoleName' => 'required|min:3|unique:roles,name']);
        Role::firstOrCreate(['name' => strtolower($this->newRoleName)]);
        $this->newRoleName = '';
        session()->flash('role_message', 'Role baru berhasil dibuat.');
    }

    public function deleteRole($roleId)
    {
        $role = Role::find($roleId);
        // Lindungi role krusial
        $coreRoles = ['admin', 'operator', 'guru', 'siswa', 'alumni'];
        if ($role && ! in_array(strtolower($role->name), $coreRoles, true)) {
            $role->delete();
            session()->flash('role_message', 'Role berhasil dihapus.');
        } else {
            $this->addError('action', 'Role dasar sistem tidak dapat dihapus.');
        }
    }

    public function togglePermission($roleName, $permissionName)
    {
        $role = Role::findByName($roleName);
        $permission = Permission::firstOrCreate(['name' => $permissionName]);

        if ($role->hasPermissionTo($permissionName)) {
            $role->revokePermissionTo($permissionName);
        } else {
            $role->givePermissionTo($permissionName);
        }
    }

    public function render()
    {
        // Sembunyikan relasi siswa (Fokus pada Staff/Admin/Guru)
        $usersQuery = User::whereDoesntHave('siswa')
            ->with(['roles', 'guru'])
            ->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            });

        if ($this->roleFilter) {
            $usersQuery->role($this->roleFilter);
        }

        // Available standard modules to generate permissions dynamically
        $modules = ['Pengguna', 'Siswa', 'Guru', 'Keuangan', 'Website', 'Kelas'];
        $actions = ['view', 'create', 'edit', 'delete'];

        return view('livewire.admin.civitas.data-pengguna-index', [
            'users' => $usersQuery->paginate(10),
            'roles' => Role::with('permissions')->get(),
            'systemModules' => $modules,
            'systemActions' => $actions,
            'staffPositionOptions' => [
                '' => 'Belum ditentukan',
                'Kepala Tata Usaha' => 'Kepala Tata Usaha',
                'Staf Tata Usaha' => 'Staf Tata Usaha',
                'Operator Sekolah' => 'Operator Sekolah',
                'Administrasi Siswa' => 'Administrasi Siswa',
                'Bendahara' => 'Bendahara',
                'Pustakawan' => 'Pustakawan',
                'Laboran' => 'Laboran',
                'Petugas Keamanan' => 'Petugas Keamanan',
                'Petugas Kebersihan' => 'Petugas Kebersihan',
            ],
        ]);
    }
}
