<?php

namespace App\Livewire\Admin\Civitas;

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

    public $selectedUserId = null;

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

    public function assignRole($userId, $roleName)
    {
        if (auth()->id() === $userId) {
            $this->addError('action', 'Penguncian diri (Self-lockout) dicegah. Anda tak bisa mengubah role milik sendiri.');

            return;
        }

        $user = User::find($userId);
        if ($user) {
            $user->syncRoles([$roleName]);
        }
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
        if ($role && ! in_array($role->name, ['admin', 'operator'])) {
            $role->delete();
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
            ->with('roles')
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
        ]);
    }
}
