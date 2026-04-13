<div x-data="{ tab: @entangle('tab') }" class="flex flex-col gap-5 h-full">

    {{-- Title and Tabs header --}}
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 fu d1">
        <div>
            <h1 class="txt-primary text-[22px] font-extrabold">Data Pengguna & Hak Akses</h1>
            <p class="txt-muted text-[13px] mt-1">Kelola staf, operator, dan izin akses dalam sistem.</p>
        </div>

        <div class="flex p-1 glass-card rounded-xl w-max border border-indigo-500/10">
            <button @click="tab = 'pengguna'"
                :class="{'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold': tab === 'pengguna', 'text-gray-500 hover:text-indigo-500': tab !== 'pengguna'}"
                class="px-5 py-2 text-[13px] rounded-lg transition-all cursor-pointer">
                Daftar Staf
            </button>
            <button @click="tab = 'roles'"
                :class="{'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold': tab === 'roles', 'text-gray-500 hover:text-indigo-500': tab !== 'roles'}"
                class="px-5 py-2 text-[13px] rounded-lg transition-all cursor-pointer">
                Manajemen Role
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div
            class="glass-card bg-emerald-500/10 border-emerald-500/20 text-emerald-600 p-4 rounded-xl text-sm font-semibold">
            {{ session('message') }}
        </div>
    @endif

    @error('action')
        <div class="glass-card bg-red-500/10 border-red-500/20 text-red-600 p-4 rounded-xl text-sm font-semibold">
            {{ $message }}
        </div>
    @enderror

    {{-- ============================== TAB 1: PENGGUNA ============================== --}}
    <div x-show="tab === 'pengguna'" x-transition.opacity.duration.300ms class="flex flex-col gap-4 fu d2">
        <x-ui.card>
            <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
                <div class="flex-1 max-w-sm relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <x-ui.input wire:model.live.debounce.300ms="search" type="text" class="pl-10 text-sm font-normal"
                        placeholder="Cari nama atau email..." autocomplete="off" />
                </div>
                <div class="flex gap-3 w-48">
                    @php
                        $roleOptions = ['' => 'Semua Role'];
                        foreach ($roles as $role) {
                            $roleOptions[$role->name] = ucfirst($role->name);
                        }
                    @endphp
                    <x-ui.select wire:model.live="roleFilter" :options="$roleOptions" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-white/5">
                            <th class="py-3 px-4 txt-muted text-xs font-semibold uppercase tracking-wider">Pengguna</th>
                            <th class="py-3 px-4 txt-muted text-xs font-semibold uppercase tracking-wider">Role</th>
                            <th class="py-3 px-4 txt-muted text-xs font-semibold uppercase tracking-wider">Status &
                                Login</th>
                            <th class="py-3 px-4 txt-muted text-xs font-semibold uppercase tracking-wider text-right">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @forelse($users as $user)
                            <tr class="hover:bg-indigo-50/50 dark:hover:bg-white/5 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="txt-primary text-sm font-semibold">{{ $user->name }}</p>
                                            <p class="txt-muted text-xs">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-1 flex-wrap">
                                        @forelse($user->roles as $role)
                                            <x-ui.badge variant="info">{{ ucfirst($role->name) }}</x-ui.badge>
                                        @empty
                                            <span class="text-xs txt-muted italic">Tidak ada</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex flex-col gap-1 items-start">
                                        @if($user->is_active)
                                            <x-ui.badge variant="success">Aktif</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="danger">Suspended</x-ui.badge>
                                        @endif
                                        <span class="text-[10px] txt-muted">
                                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Belum pernah' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <x-ui.dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button
                                                class="cursor-pointer text-gray-400 hover:text-indigo-500 transition-colors p-1">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                </svg>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            <div
                                                class="cursor-pointer  px-3 py-2 text-xs font-semibold txt-muted uppercase tracking-wider mb-1">
                                                Ubah Role</div>
                                            @foreach($roles as $role)
                                                <button wire:click="assignRole({{ $user->id }}, '{{ $role->name }}')"
                                                    class="cursor-pointer  block w-full px-4 py-2 text-left text-sm font-medium txt-secondary hover:bg-indigo-500/10 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                    Jadikan {{ ucfirst($role->name) }}
                                                </button>
                                            @endforeach
                                            <div class="cursor-pointer  border-t border-gray-200 dark:border-white/5 my-1">
                                            </div>
                                            <button wire:click="confirmReset({{ $user->id }})"
                                                class="cursor-pointer  block w-full px-4 py-2 text-left text-sm font-medium text-amber-600 hover:bg-amber-500/10 transition-colors">
                                                Reset Password
                                            </button>
                                            <button wire:click="confirmSuspend({{ $user->id }})"
                                                class="cursor-pointer  block w-full px-4 py-2 text-left text-sm font-medium {{ $user->is_active ? 'text-red-600 hover:bg-red-500/10' : 'text-emerald-600 hover:bg-emerald-500/10' }}  transition-colors">
                                                {{ $user->is_active ? 'Suspend Akun' : 'Aktifkan Akun' }}
                                            </button>
                                        </x-slot>
                                    </x-ui.dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center txt-muted text-sm italic">
                                    Tidak ada pengguna staf yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :links="$users" />
        </x-ui.card>
    </div>

    {{-- ============================== TAB 2: ROLES ============================== --}}
    <div x-show="tab === 'roles'" x-cloak x-transition.opacity.duration.300ms class="flex flex-col gap-4 fu d3">
        @if (session()->has('role_message'))
            <div
                class="glass-card bg-indigo-500/10 border-indigo-500/20 text-indigo-600 p-4 rounded-xl text-sm font-semibold">
                {{ session('role_message') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Buat Role Baru --}}
            <div class="lg:col-span-1">
                <x-ui.card title="Buat Peran Baru">
                    <form wire:submit="createRole" class="flex flex-col gap-4">
                        <div>
                            <x-ui.label for="roleName" value="Nama Role (Tanpa spasi)" />
                            <x-ui.input wire:model="newRoleName" id="roleName" type="text"
                                placeholder="contoh: bendahara" required class="mt-1" />
                            @error('newRoleName') <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <x-ui.button type="submit" class="w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Role
                        </x-ui.button>
                        <p class="text-xs txt-muted mt-2">Buat role baru untuk memberikan grup hak akses spesifik ke
                            sekumpulan pengguna.</p>
                    </form>
                </x-ui.card>
            </div>

            {{-- Matrix Permissions --}}
            <div class="lg:col-span-2">
                <x-ui.card title="Matriks Hak Akses (Permissions)">
                    <div class="overflow-x-auto pb-4">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="py-2 px-3 txt-muted text-xs font-semibold uppercase">Modul / Aksi</th>
                                    @foreach($roles as $role)
                                        <th class="py-2 px-3 txt-primary text-xs font-bold text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                {{ ucfirst($role->name) }}
                                                @if(!in_array($role->name, ['admin', 'operator', 'guru']))
                                                    <button wire:click="deleteRole({{ $role->id }})"
                                                        wire:confirm="Hapus role ini permanen?"
                                                        class="text-red-400 hover:text-red-600">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                                @foreach($systemModules as $module)
                                    <tr class="bg-indigo-50/30 dark:bg-black/10">
                                        <td colspan="{{ count($roles) + 1 }}"
                                            class="py-2 px-3 text-xs font-bold text-indigo-500 uppercase">
                                            Modul: {{ $module }}
                                        </td>
                                    </tr>
                                    @foreach($systemActions as $action)
                                        @php $permName = $action . ' ' . strtolower($module); @endphp
                                        <tr class="hover:bg-indigo-50/50 dark:hover:bg-white/5 transition-colors">
                                            <td class="py-2 px-3 txt-primary text-[13px] pl-6 align-middle">
                                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-gray-300 mr-2"></span>
                                                Dapat {{ ucfirst($action) }}
                                            </td>
                                            @foreach($roles as $role)
                                                <td class="py-2 px-3 text-center align-middle">
                                                    <label class="inline-flex items-center cursor-pointer">
                                                        <x-ui.checkbox
                                                            wire:click="togglePermission('{{ $role->name }}', '{{ $permName }}')"
                                                            :checked="$role->permissions->contains('name', $permName)"
                                                            class="w-4 h-4" />
                                                    </label>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>

    {{-- ============================== MODALS ============================== --}}

    {{-- Suspend Modal --}}
    <x-ui.modal name="suspend-modal" :show="$isSuspendModalOpen" maxWidth="md">
        <div class="text-center">
            <div
                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-500/20 mb-4">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-bold txt-primary mb-2">Konfirmasi Status Akun</h3>
            <p class="text-sm txt-muted mb-6">
                Apakah Anda yakin ingin mengubah status aktif pengguna ini? Pengguna yang disuspend tidak akan bisa
                login ke dalam sistem.
            </p>
            <div class="flex justify-center gap-3">
                <x-ui.button variant="secondary" wire:click="$set('isSuspendModalOpen', false)">Batal</x-ui.button>
                <x-ui.button variant="danger" wire:click="toggleSuspend">Ya, Lanjutkan</x-ui.button>
            </div>
        </div>
    </x-ui.modal>

    {{-- Reset Password Modal --}}
    <x-ui.modal name="reset-modal" :show="$isResetModalOpen" maxWidth="md">
        <div class="text-center">
            <div
                class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-amber-100 dark:bg-amber-500/20 mb-4">
                <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-bold txt-primary mb-2">Reset Sandi</h3>
            <p class="text-sm txt-muted mb-6">
                Sandi pengguna ini akan dikembalikan ke default: <strong
                    class="text-amber-600 dark:text-amber-400">password123</strong>. Tindakan ini tidak dapat
                dibatalkan.
            </p>
            <div class="flex justify-center gap-3">
                <x-ui.button variant="secondary" wire:click="$set('isResetModalOpen', false)">Batal</x-ui.button>
                <x-ui.button variant="primary" wire:click="resetPassword"
                    class="bg-gradient-to-r from-amber-500 to-orange-500 shadow-amber-500/30">Reset
                    Sekarang</x-ui.button>
            </div>
        </div>
    </x-ui.modal>

</div>