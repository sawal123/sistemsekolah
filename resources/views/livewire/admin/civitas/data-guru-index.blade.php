<div style="display:flex;flex-direction:column;gap:20px;height:100%;">
    <div class="fu d1" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1 class="txt-primary" style="font-size:22px;font-weight:800;">Data Guru</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:3px;">Kelola profil, akun, dan peran guru.</p>
        </div>
        <a href="{{ route('admin.civitas.data-guru.create') }}" wire:navigate
            class="inline-flex items-center justify-center gap-2 px-4 py-2 font-semibold text-sm rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white hover:from-indigo-600 hover:to-purple-600 focus:ring-indigo-500 shadow-md shadow-indigo-500/30">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Guru
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-4">
        <x-ui.card class="bg-indigo-500/5 border-indigo-500/10 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="p-3 bg-indigo-500/10 rounded-xl text-indigo-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs txt-muted font-medium">Total Guru</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card class="bg-amber-500/5 border-amber-500/10 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                    </path>
                </svg>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="p-3 bg-amber-500/10 rounded-xl text-amber-500 text-xs font-bold">HON</div>
                <div>
                    <p class="text-xs txt-muted font-medium">Guru Honorer</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['honorer'] }}</h3>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card class="bg-blue-500/5 border-blue-500/10 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                    </path>
                </svg>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="p-3 bg-blue-500/10 rounded-xl text-blue-500 text-xs font-bold">TTP</div>
                <div>
                    <p class="text-xs txt-muted font-medium">Guru Tetap</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['tetap'] }}</h3>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card class="bg-emerald-500/5 border-emerald-500/10 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                    </path>
                </svg>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-500 text-xs font-bold">WAL</div>
                <div>
                    <p class="text-xs txt-muted font-medium">Wali Kelas</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['wali_kelas'] }}</h3>
                </div>
            </div>
        </x-ui.card>
    </div>

    <x-ui.card padding="0">
        <div class="flex flex-wrap items-center gap-3 p-3">
            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                <div class="w-20 flex-shrink-0">
                    <x-ui.select wire:model.live="perPage" :options="['10' => '10', '20' => '20', '50' => '50']" />
                </div>
                <div style="flex:1;min-width:200px;">
                    <x-ui.search wire:model="search" placeholder="Cari Nama atau NIP..." />
                </div>
                <div style="min-width:180px;">
                    <x-ui.select wire:model.live="filterJabatan" :options="['' => 'Semua Jabatan', 'Guru Tetap' => 'Guru Tetap', 'Guru Honorer' => 'Guru Honorer', 'Wakasek' => 'Wakasek']" />
                </div>
                <div style="min-width:180px;">
                    <x-ui.select wire:model.live="filterWaliKelas" :options="['' => 'Semua Status', 'ya' => 'Wali Kelas', 'tidak' => 'Bukan Wali Kelas']" />
                </div>
            </div>
        </div>
        <div class="rounded-xl overflow-hidden border border-indigo-500/10 dark:border-white/10 m-3 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr class="bg-indigo-500/5 dark:bg-white/5 border-b border-indigo-500/10 dark:border-white/10">
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted w-[80px]">No
                            </th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Profil Guru
                            </th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">NIP</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Jabatan</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Wali Kelas
                            </th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted"
                                style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                        @forelse($gurus as $index => $item)
                            <tr class="hover:bg-indigo-500/5 dark:hover:bg-white/5">
                                <td class="px-6 py-4 text-sm txt-primary font-medium">{{ $gurus->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 text-sm txt-primary font-medium">
                                    <div style="display:flex;align-items:center;gap:10px;">
                                        @if($item->foto)
                                            <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto"
                                                style="width:36px;height:36px;border-radius:50%;object-fit:cover;">
                                        @else
                                            <div class="av" style="background:rgba(99,102,241,0.15);color:#6366f1;">
                                                {{ strtoupper(substr($item->user->name ?? 'G', 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="txt-primary" style="font-size:13px;font-weight:600;">
                                                {{ $item->user->name ?? 'Tanpa Nama' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="txt-secondary" style="font-size:13px;">{{ $item->nip }}</td>
                                <td class="px-6 py-4 text-sm txt-primary font-medium">
                                    <span class="badge"
                                        style="background:rgba(139,92,246,0.12);color:#7c3aed;">{{ $item->jabatan ?: '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm txt-primary font-medium">
                                    @if($item->kelas)
                                        <span class="badge" style="background:rgba(16,185,129,0.12);color:#059669;">Wali Kelas
                                            {{ $item->kelas->nama_kelas ?? '' }}</span>
                                    @else
                                        <span style="font-size:12px;color:gray;">-</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                        <button wire:click="resetPassword({{ $item->id }})"
                                            wire:confirm="Reset password menjadi ddmmyyyy dari tanggal lahir?"
                                            title="Reset Password"
                                            style="padding:6px;border-radius:6px;background:rgba(245,158,11,0.1);color:#d97706;border:none;cursor:pointer;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4v-3.252a1 1 0 01.293-.707l8.196-8.196A6 6 0 0115 7z">
                                                </path>
                                            </svg>
                                        </button>
                                        <a href="{{ route('admin.civitas.data-guru.detail', $item->id) }}" wire:navigate
                                            style="padding:6px;border-radius:6px;background:rgba(59,130,246,0.1);color:#3b82f6;border:none;cursor:pointer;display:inline-block;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12A3 3 0 119 12a3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.civitas.data-guru.edit', $item->id) }}" wire:navigate
                                            style="padding:6px;border-radius:6px;background:rgba(99,102,241,0.1);color:#6366f1;border:none;cursor:pointer;display:inline-block;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <button wire:click="confirmDelete('{{ $item->id }}')"
                                            style="padding:6px;border-radius:6px;background:rgba(239,68,68,0.1);color:#ef4444;border:none;cursor:pointer;">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="txt-muted" style="text-align:center;padding:20px;">Tidak ada data
                                    guru
                                    ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
        <x-ui.pagination :links="$gurus" />
    </x-ui.card>

    <x-ui.confirm-modal name="confirm-delete-guru" onConfirm="delete" />
</div>