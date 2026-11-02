<?php

namespace App\Livewire\Admin\Civitas;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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

    private const PROFILE_MANAGED_ROLES = ['guru', 'siswa', 'alumni'];

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

    public string $newUserSource = 'new';

    public $newUserProfileId = null;

    public string $newUserProfileSearch = '';

    // Properti Role
    public $newRoleName = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
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
        $this->dispatch('open-modal', 'suspend-modal');
    }

    public function confirmReset($userId)
    {
        if (auth()->id() === $userId) {
            $this->addError('action', 'Setel ulang kata sandi diri sendiri dilarang melalui panel ini.');

            return;
        }
        $this->selectedUserId = $userId;
        $this->isResetModalOpen = true;
        $this->dispatch('open-modal', 'reset-modal');
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
        $this->dispatch('close-modal', 'suspend-modal');
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
        $this->dispatch('close-modal', 'reset-modal');
    }

    public function openCreateUserModal()
    {
        $this->resetValidation();
        $this->reset(['newUserName', 'newUserEmail', 'newUserPassword', 'newUserRole', 'newUserStaffPosition', 'newUserSource', 'newUserProfileId', 'newUserProfileSearch']);
        $this->newUserRole = 'admin';
        $this->newUserSource = 'new';
        $this->isCreateUserModalOpen = true;
        $this->dispatch('open-modal', 'create-user-modal');
    }

    public function updatedNewUserSource(): void
    {
        $this->newUserProfileId = null;
        $this->newUserProfileSearch = '';
        $this->resetValidation('newUserProfileId');
    }

    public function updatingNewUserProfileSearch(): void
    {
        $this->newUserProfileId = null;
    }

    public function selectSourceProfile(int $profileId): void
    {
        $this->newUserProfileId = $profileId;
        $profile = $this->selectedSourceProfile();
        $this->newUserProfileSearch = $profile->user->name;
        $this->resetValidation('newUserProfileId');
    }

    public function clearSourceProfile(): void
    {
        $this->reset(['newUserProfileId', 'newUserProfileSearch']);
    }

    public function closeCreateUserModal()
    {
        $this->isCreateUserModalOpen = false;
        $this->dispatch('close-modal', 'create-user-modal');
    }

    public function createUser()
    {
        $this->validate([
            'newUserSource' => ['required', Rule::in(['new', 'guru', 'siswa'])],
            'newUserRole' => ['required', 'exists:roles,name', Rule::notIn(self::PROFILE_MANAGED_ROLES)],
            'newUserStaffPosition' => ['nullable', 'string', 'max:100', Rule::in(array_keys($this->staffPositionOptions()))],
        ]);

        if ($this->newUserSource === 'new') {
            $this->validate([
                'newUserName' => 'required|string|max:255',
                'newUserEmail' => 'required|email|unique:users,email',
                'newUserPassword' => 'nullable|min:6',
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
            $message = "Pengguna {$user->name} berhasil ditambahkan dengan kata sandi: {$password}";
        } else {
            $this->validate(['newUserProfileId' => 'required|integer']);
            $user = $this->sourceProfileUser();

            $user->update(['staff_position' => $this->newUserStaffPosition ?: null]);
            if (! $user->hasRole($this->newUserRole)) {
                $user->assignRole($this->newUserRole);
            }
            $message = "Akun {$user->name} berhasil ditambahkan sebagai staf tanpa mengubah profil asalnya.";
        }

        $this->dispatch('notify', 
            type: 'success', 
            message: $message
        );

        $this->closeCreateUserModal();
    }

    public function assignRole($userId, $roleName)
    {
        if (auth()->id() === $userId) {
            $this->addError('action', 'Penguncian diri (Self-lockout) dicegah. Anda tak bisa mengubah role milik sendiri.');

            return;
        }

        if (in_array(strtolower($roleName), self::PROFILE_MANAGED_ROLES, true)) {
            $this->addError('action', 'Role Guru dan Siswa dikelola dari Data Guru atau Data Siswa.');

            return;
        }

        if (! Role::where('name', $roleName)->exists()) {
            $this->addError('action', 'Role yang dipilih tidak tersedia.');

            return;
        }

        $user = $this->staffUsersQuery()->find($userId);
        if ($user) {
            $profileRoles = $user->getRoleNames()
                ->filter(fn (string $role) => in_array(strtolower($role), self::PROFILE_MANAGED_ROLES, true))
                ->all();
            $user->syncRoles([...$profileRoles, $roleName]);

            $this->dispatch('notify', 
                type: 'success', 
                message: "Role pengguna {$user->name} berhasil diubah menjadi: " . ucfirst($roleName)
            );
        }
    }

    public function updateStaffPosition(int $userId, string $position): void
    {
        abort_unless(array_key_exists($position, $this->staffPositionOptions()), 422);

        $user = $this->staffUsersQuery()->findOrFail($userId);
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
        // Hanya akun yang memiliki minimal satu role staf yang tampil di sini.
        $usersQuery = $this->staffUsersQuery()
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
            'staffRoles' => Role::whereNotIn('name', self::PROFILE_MANAGED_ROLES)->orderBy('name')->get(),
            'systemModules' => $modules,
            'systemActions' => $actions,
            'staffPositionOptions' => $this->staffPositionOptions(),
            'sourceProfiles' => $this->sourceProfiles(),
            'selectedSourceProfile' => $this->newUserProfileId ? $this->selectedSourceProfile() : null,
        ]);
    }

    private function staffUsersQuery()
    {
        return User::whereHas('roles', fn ($query) => $query->whereNotIn('name', self::PROFILE_MANAGED_ROLES));
    }

    private function sourceProfileUser(): User
    {
        $profile = $this->selectedSourceProfile();

        abort_unless($profile->user, 422);

        return $profile->user;
    }

    private function selectedSourceProfile()
    {
        return match ($this->newUserSource) {
            'guru' => Guru::with('user')->findOrFail($this->newUserProfileId),
            'siswa' => Siswa::with('user')->findOrFail($this->newUserProfileId),
            default => abort(422),
        };
    }

    private function sourceProfiles()
    {
        if ($this->newUserProfileSearch === '') {
            return collect();
        }

        return $this->newUserSource === 'guru'
            ? $this->guruSources()
            : $this->siswaSources();
    }

    private function guruSources()
    {
        return Guru::with('user:id,name,email')
            ->whereHas('user', function ($query) {
                $query->when($this->newUserProfileSearch, function ($userQuery) {
                    $search = '%'.$this->newUserProfileSearch.'%';
                    $userQuery->where(fn ($q) => $q->where('name', 'like', $search)->orWhere('email', 'like', $search));
                });
            })
            ->orderBy('nip')
            ->limit(100)
            ->get();
    }

    private function siswaSources()
    {
        return Siswa::with('user:id,name,email')
            ->whereHas('user', function ($query) {
                $query->when($this->newUserProfileSearch, function ($userQuery) {
                    $search = '%'.$this->newUserProfileSearch.'%';
                    $userQuery->where(fn ($q) => $q->where('name', 'like', $search)->orWhere('email', 'like', $search));
                });
            })
            ->orderBy('nis')
            ->limit(100)
            ->get();
    }

    private function staffPositionOptions(): array
    {
        return [
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
        ];
    }
}
