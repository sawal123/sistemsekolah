<div class="flex flex-col gap-6 pb-20">
    <x-ui.toast />

    {{-- Header Section --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="fu d1">
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Data Siswa</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Kelola informasi profil, akademik, dan akun
                login siswa.</p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button wire:click="openModal" variant="primary" class="shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Siswa
            </x-ui.button>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.card class="bg-indigo-500/5 border-indigo-500/10 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                </svg>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="p-3 bg-indigo-500/10 rounded-xl text-indigo-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs txt-muted font-medium">Total Siswa</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card class="bg-emerald-500/5 border-emerald-500/10 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                </svg>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-500 text-xs font-bold">AKTIF</div>
                <div>
                    <p class="text-xs txt-muted font-medium">Siswa Aktif</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['aktif'] }}</h3>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card class="bg-blue-500/5 border-blue-500/10 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z" />
                </svg>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="p-3 bg-blue-500/10 rounded-xl text-blue-500 text-xs font-bold">LULUS</div>
                <div>
                    <p class="text-xs txt-muted font-medium">Alumni</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['lulus'] }}</h3>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card class="bg-red-500/5 border-red-500/10 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                </svg>
            </div>
            <div class="flex items-center gap-4 relative z-10">
                <div class="p-3 bg-red-500/10 rounded-xl text-red-500 text-xs font-bold">NON</div>
                <div>
                    <p class="text-xs txt-muted font-medium">Pindah/Keluar</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['nonaktif'] }}</h3>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Advanced Filter Bar --}}
    {{-- <x-ui.card class="dark:bg-white/[0.02]"> --}}
        
        
        {{--
    </x-ui.card> --}}

    {{-- Table Section --}}
    <x-ui.card padding="0">
        <div class="flex flex-wrap items-center gap-3 p-3">
        
            {{-- Per Page --}}
            <div class="w-20 flex-shrink-0">
                <x-ui.select wire:model.live="perPage" :options="['10' => '10', '20' => '20', '50' => '50']" />
            </div>
        
            {{-- Divider --}}
            <div class="h-7 w-px bg-indigo-500/20 flex-shrink-0 hidden sm:block"></div>
        
            {{-- Search --}}
            <div class="flex-1 min-w-[180px]">
                <x-ui.search model="search" placeholder="Cari Nama, NISN atau NIS..." />
            </div>
        
            {{-- Filter Jenjang --}}
            <div class="w-40 flex-shrink-0">
                <x-ui.select wire:model.live="filterJenjang" :options="['' => 'Semua Jenjang', 'SMP' => 'SMP', 'SMA' => 'SMA']" />
            </div>
        
            {{-- Filter Kelas --}}
            <div class="w-40 flex-shrink-0">
                <x-ui.select wire:model.live="filterKelas" :options="['' => 'Semua Kelas'] + $availableClasses->pluck('nama_kelas', 'id')->toArray()" />
            </div>
        
            {{-- Filter Status --}}
            <div class="w-40 flex-shrink-0">
                <x-ui.select wire:model.live="filterStatus" :options="['' => 'Semua Status', 'Aktif' => 'Aktif', 'Lulus' => 'Lulus', 'Pindah' => 'Pindah', 'Dikeluarkan' => 'Dikeluarkan']" />
            </div>
        </div>
        <div class=" rounded-xl overflow-hidden border border-indigo-500/10 dark:border-white/10 m-3 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr class="bg-indigo-500/5 dark:bg-white/5 border-b border-indigo-500/10 dark:border-white/10">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted w-[80px]">No</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Siswa</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">NIS / NISN</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Kelas</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Jenjang</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                    @forelse($siswas as $index => $item)
                        <tr class="hover:bg-indigo-500/[0.02] dark:hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-4 text-sm txt-primary font-medium">
                                {{ ($siswas->currentPage() - 1) * $siswas->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl overflow-hidden glass border-2 border-white/20 flex-shrink-0 shadow-sm">
                                        @if($item->foto)
                                            <img src="{{ Storage::url($item->foto) }}" class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full flex items-center justify-center bg-indigo-500/10 text-indigo-500 font-bold text-xs">
                                                {{ substr($item->user->name, 0, 2) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold txt-primary leading-tight">{{ $item->user->name }}</span>
                                        <span class="text-[11px] txt-muted lowercase">{{ $item->user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium txt-primary">{{ $item->nis }}</span>
                                    <span class="text-[11px] txt-muted">NISN: {{ $item->nisn }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-sm font-semibold txt-primary">{{ $item->kelas?->nama_kelas ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 {{ $item->jenjang === 'SMA' ? 'bg-blue-500/10 text-blue-500' : 'bg-emerald-500/10 text-emerald-500' }} rounded-lg font-bold text-[10px] border border-current opacity-70">
                                    {{ $item->jenjang }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
    $statusClasses = [
        'Aktif' => 'bg-emerald-500/10 text-emerald-500',
        'Lulus' => 'bg-blue-500/10 text-blue-500',
        'Pindah' => 'bg-amber-500/10 text-amber-500',
        'Dikeluarkan' => 'bg-red-500/10 text-red-500',
    ];
                                @endphp
                                <span
                                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClasses[$item->status] ?? 'bg-slate-500/10' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="showDetail({{ $item->id }})"
                                        class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500 transition-all cursor-pointer"
                                        title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="edit({{ $item->id }})"
                                        class="p-2 rounded-lg hover:bg-amber-500/10 text-amber-500 transition-all cursor-pointer"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $item->id }})"
                                        wire:confirm="Ubah status siswa menjadi Pindah (Nonaktif)?"
                                        class="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-all cursor-pointer"
                                        title="Nonaktifkan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center opacity-40">
                                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p class="text-sm">Tidak ada data siswa ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>

        <x-ui.pagination :links="$siswas" />
    </x-ui.card>

    {{-- Mega Form Modal --}}
    <x-ui.modal name="siswa-form" :show="$isModalOpen" maxWidth="2xl"
        title="{{ $editId ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}">
        <div x-data="{ tab: 'akun' }" class="flex flex-col min-h-[500px]">
            {{-- Tabs Navigation --}}
            <div class="flex border-b border-indigo-500/10 mb-8 bg-indigo-500/[0.03] p-1 rounded-xl mx-1">
                <button type="button" @click="tab = 'akun'"
                    class="flex-1 py-3 px-4 rounded-lg text-xs font-bold transition-all duration-200 flex items-center justify-center gap-2"
                    :class="tab === 'akun' ? 'bg-indigo-500 text-white shadow-lg' : 'txt-muted hover:bg-white/5'">
                    <svg class="w-4 h-4" :class="tab === 'akun' ? 'opacity-100' : 'opacity-40'" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Akun & Akademik
                </button>
                <button type="button" @click="tab = 'diri'"
                    class="flex-1 py-3 px-4 rounded-lg text-xs font-bold transition-all duration-200 flex items-center justify-center gap-2"
                    :class="tab === 'diri' ? 'bg-indigo-500 text-white shadow-lg' : 'txt-muted hover:bg-white/5'">
                    <svg class="w-4 h-4" :class="tab === 'diri' ? 'opacity-100' : 'opacity-40'" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Data Diri
                </button>
                <button type="button" @click="tab = 'ortu'"
                    class="flex-1 py-3 px-4 rounded-lg text-xs font-bold transition-all duration-200 flex items-center justify-center gap-2"
                    :class="tab === 'ortu' ? 'bg-indigo-500 text-white shadow-lg' : 'txt-muted hover:bg-white/5'">
                    <svg class="w-4 h-4" :class="tab === 'ortu' ? 'opacity-100' : 'opacity-40'" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Orang Tua
                </button>
            </div>

            {{-- Form Content --}}
            <form wire:submit.prevent="save" class="flex-1 flex flex-col px-1">
                {{-- TAB: AKUN & AKADEMIK --}}
                <div x-show="tab === 'akun'" x-transition class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-ui.label for="name" value="Nama Lengkap Siswa" class="mb-2" />
                            <x-ui.input wire:model="name" id="name" placeholder="Masukkan nama sesuai ijazah"
                                class="w-full" />
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <x-ui.label for="email" value="Alamat Email (Opsional)" class="mb-2" />
                            <x-ui.input wire:model="email" id="email" type="email"
                                placeholder="Kosongkan untuk auto: NIS@sekolah.sch.id" class="w-full" />
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <x-ui.label for="password" value="Password Login" class="mb-2" />
                            <x-ui.input wire:model="password" id="password" type="text"
                                placeholder="{{ $editId ? 'Kosongkan jika tidak diubah' : 'Kosongkan tel standar: Tgl Lahir' }}"
                                class="w-full" />
                        </div>
                        <div>
                            <x-ui.label for="nisn" value="NISN" class="mb-2" />
                            <x-ui.input wire:model="nisn" id="nisn" placeholder="10 digit nomor NISN" class="w-full" />
                            @error('nisn') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <x-ui.label for="nis" value="NIS (Nomor Induk)" class="mb-2" />
                            <x-ui.input wire:model="nis" id="nis" placeholder="Nomor Induk Siswa" class="w-full" />
                            @error('nis') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <x-ui.label value="Jenjang" class="mb-2" />
                            <x-ui.select wire:model.live="jenjang" :options="['SMP' => 'SMP', 'SMA' => 'SMA']"
                                placeholder="Pilih Jenjang" />
                        </div>
                        <div>
                            <x-ui.label value="Kelas" class="mb-2" />
                            <x-ui.select wire:model="kelas_id" :options="['' => 'Pilih Kelas'] + $formClasses->pluck('nama_kelas', 'id')->toArray()" placeholder="Pilih Kelas" />
                            @error('kelas_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- TAB: DATA DIRI --}}
                <div x-show="tab === 'diri'" x-transition class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            class="md:col-span-2 flex items-center gap-6 p-4 glass rounded-2xl border border-white/10 shadow-inner">
                            <div
                                class="w-24 h-24 rounded-2xl overflow-hidden glass border-4 border-white/20 flex-shrink-0 relative group">
                                @if($foto)
                                    <img src="{{ $foto->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif($existingFoto)
                                    <img src="{{ Storage::url($existingFoto) }}" class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-indigo-500/10 text-indigo-500">
                                        <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div wire:loading wire:target="foto"
                                    class="absolute inset-0 bg-black/50 flex items-center justify-center z-10">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold txt-primary mb-1">Foto Profil</h4>
                                <p class="text-[11px] txt-muted mb-3">Upload foto format JPG/PNG, maksimal 1MB.</p>
                                <input type="file" wire:model="foto" id="foto-input" class="hidden">
                                <label for="foto-input"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-500/10 text-indigo-500 rounded-lg text-xs font-bold hover:bg-indigo-500/20 cursor-pointer transition-all border border-indigo-500/30">
                                    Pilih File Foto
                                </label>
                            </div>
                        </div>
                        <div>
                            <x-ui.label for="tempat_lahir" value="Tempat Lahir" class="mb-2" />
                            <x-ui.input wire:model="tempat_lahir" id="tempat_lahir" placeholder="Kota/Kab"
                                class="w-full" />
                        </div>
                        <div>
                            <x-ui.label for="tanggal_lahir" value="Tanggal Lahir" class="mb-2" />
                            <x-ui.input wire:model="tanggal_lahir" id="tanggal_lahir" type="date" class="w-full" />
                        </div>
                        <div>
                            <x-ui.label value="Agama" class="mb-2" />
                            <x-ui.select wire:model="agama" :options="['' => 'Pilih Agama', 'Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu', 'Lainnya' => 'Lainnya']" placeholder="Pilih Agama" />
                        </div>
                        <div>
                            <x-ui.label value="Status Kepegawaian (Status Siswa)" class="mb-2" />
                            <x-ui.select wire:model="status" :options="['Aktif' => 'Aktif', 'Lulus' => 'Lulus', 'Pindah' => 'Pindah', 'Dikeluarkan' => 'Dikeluarkan']" placeholder="Pilih Status" />
                        </div>
                        <div class="md:col-span-2">
                            <x-ui.label for="alamat" value="Alamat Lengkap" class="mb-2" />
                            <textarea wire:model="alamat" id="alamat" rows="3"
                                placeholder="Jl. Nama Jalan No. XX, RT/RW ..."
                                class="w-full px-4 py-3 glass border-2 border-transparent rounded-xl text-sm txt-primary outline-none focus:border-indigo-500/50 transition-all"></textarea>
                        </div>
                    </div>
                </div>

                {{-- TAB: DATA ORANG TUA --}}
                <div x-show="tab === 'ortu'" x-transition class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-ui.label for="nama_ayah" value="Nama Ayah" class="mb-2" />
                            <x-ui.input wire:model="nama_ayah" id="nama_ayah" placeholder="Nama ayah kandung"
                                class="w-full" />
                        </div>
                        <div>
                            <x-ui.label for="pekerjaan_ayah" value="Pekerjaan Ayah" class="mb-2" />
                            <x-ui.input wire:model="pekerjaan_ayah" id="pekerjaan_ayah"
                                placeholder="Misal: Pegawai Swasta" class="w-full" />
                        </div>
                        <div class="mt-4 border-t border-indigo-500/10 pt-6 md:col-span-2"></div>
                        <div>
                            <x-ui.label for="nama_ibu" value="Nama Ibu" class="mb-2" />
                            <x-ui.input wire:model="nama_ibu" id="nama_ibu" placeholder="Nama ibu kandung"
                                class="w-full" />
                        </div>
                        <div>
                            <x-ui.label for="pekerjaan_ibu" value="Pekerjaan Ibu" class="mb-2" />
                            <x-ui.input wire:model="pekerjaan_ibu" id="pekerjaan_ibu"
                                placeholder="Misal: Ibu Rumah Tangga" class="w-full" />
                        </div>
                        <div class="mt-4 border-t border-indigo-500/10 pt-6 md:col-span-2"></div>
                        <div class="md:col-span-2">
                            <x-ui.label for="no_telp_ortu" value="Nomor Telepon Orang Tua (WhatsApp Aktif)"
                                class="mb-2" />
                            <x-ui.input wire:model="no_telp_ortu" id="no_telp_ortu" placeholder="Contoh: 0812345678"
                                class="w-full" />
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="mt-auto pt-8 border-t border-indigo-500/10 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] txt-muted uppercase font-bold tracking-widest" x-show="tab === 'akun'">
                            Langkah 1 dari 3</p>
                        <p class="text-[10px] txt-muted uppercase font-bold tracking-widest" x-show="tab === 'diri'">
                            Langkah 2 dari 3</p>
                        <p class="text-[10px] txt-muted uppercase font-bold tracking-widest" x-show="tab === 'ortu'">
                            Langkah 3 dari 3</p>
                    </div>
                    <div class="flex gap-3">
                        <x-ui.button wire:click="closeModal" variant="secondary" type="button" class="btn-sm">
                            Batal
                        </x-ui.button>

                        <button type="button" x-show="tab !== 'ortu'" @click="tab = (tab === 'akun' ? 'diri' : 'ortu')"
                            class="px-6 py-2 bg-indigo-500/10 text-indigo-500 rounded-xl text-sm font-bold hover:bg-indigo-500/20 transition-all border border-indigo-500/30">
                            Selanjutnya
                        </button>

                        <x-ui.button x-show="tab === 'ortu'" variant="primary" type="submit"
                            class="shadow-lg shadow-indigo-500/20">
                            {{ $editId ? 'Simpan Perubahan' : 'Selesai & Simpan' }}
                        </x-ui.button>
                    </div>
                </div>
            </form>
        </div>
    </x-ui.modal>

    {{-- Detailed View Modal (A4 Print Ready) --}}
    <x-ui.modal name="view-detail-modal" maxWidth="5xl">
        @if($detailSiswa)

            {{-- ── MODAL HEADER ── --}}
            <div
                class="px-7 py-4 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between no-print">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-none">Detail Profil Siswa</h3>
                        <p class="text-[11px] text-slate-400 uppercase tracking-widest font-semibold mt-0.5">
                            Document ID: #{{ $detailSiswa->nis }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="downloadPdf({{ $detailSiswa->id }})"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Unduh PDF
                    </button>
                    <button onclick="window.print()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold rounded-lg border border-slate-200 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2" />
                        </svg>
                        Cetak
                    </button>
                    <x-ui.button @click="$dispatch('close-modal', 'view-detail-modal')" variant="secondary">Tutup</x-ui.button>
                </div>
            </div>

            {{-- ── PRINTABLE DOCUMENT ── --}}
            <div class="bg-slate-50 dark:bg-slate-800/40 px-6 py-6" id="printable-area">

                <style>
                    @media print {
                        body * {
                            visibility: hidden;
                        }

                        #printable-area,
                        #printable-area * {
                            visibility: visible;
                        }

                        #printable-area {
                            position: absolute !important;
                            left: 0 !important;
                            top: 0 !important;
                            width: 100% !important;
                            padding: 0 !important;
                            margin: 0 !important;
                            background: #fff !important;
                        }

                        .no-print {
                            display: none !important;
                        }

                        .doc-card {
                            box-shadow: none !important;
                        }
                    }
                </style>

                {{-- White document card --}}
                <div class="doc-card bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                    {{-- ── KOP SURAT ── --}}
                    <div class="flex items-center gap-6 px-10 py-6 border-b-2 border-slate-900">
                        <div class="w-24 h-24 flex-shrink-0 flex items-center justify-center">
                            @if(isset($schoolSettings['app_logo']))
                                <img src="{{ Storage::url($schoolSettings['app_logo']) }}" class="w-full h-full object-contain">
                            @else
                                <div
                                    class="w-full h-full bg-slate-100 rounded-full flex items-center justify-center text-xs font-bold text-slate-400">
                                    LOGO</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight leading-tight">
                                {{ $schoolSettings['school_name'] ?? 'SEKOLAH ANDA' }}
                            </h1>
                            <p class="text-sm text-slate-600 font-semibold mt-1">
                                {{ $schoolSettings['school_address'] ?? 'Alamat Sekolah Belum Diatur' }}
                            </p>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-400 mt-2">
                                <span>Telp: {{ $schoolSettings['school_phone'] ?? '-' }}</span>
                                <span class="text-slate-300">|</span>
                                <span>Email: {{ $schoolSettings['school_email'] ?? '-' }}</span>
                                <span class="text-slate-300">|</span>
                                <span>Web: {{ $schoolSettings['school_website'] ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- ── JUDUL DOKUMEN ── --}}
                    <div class="px-10 py-5 border-b border-slate-100">
                        <h2 class="text-center text-base font-black text-slate-900 uppercase tracking-widest">
                            Bio Data Siswa
                        </h2>
                    </div>

                    {{-- ── BODY ── --}}
                    <div class="px-10 py-8">
                        <div class="flex gap-8">

                            {{-- Foto --}}
                            <div class="flex-shrink-0 flex flex-col items-center gap-2">
                                <div
                                    class="w-[130px] h-[170px] border-2 border-slate-300 rounded overflow-hidden bg-slate-50 flex items-center justify-center">
                                    @if($detailSiswa->foto)
                                        <img src="{{ Storage::url($detailSiswa->foto) }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-14 h-14 text-slate-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                        </svg>
                                    @endif
                                </div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Pas Foto 3×4</p>
                            </div>

                            {{-- Data --}}
                            <div class="flex-1 min-w-0 space-y-7">

                                {{-- SECTION MACRO --}}
                                @php
    $sectionClass = 'text-[10px] font-black text-slate-700 uppercase tracking-widest px-3 py-1.5 bg-slate-50 border-l-[3px] border-slate-900 rounded-r';
                                @endphp

                                {{-- A. Informasi Akademik --}}
                                <div>
                                    <p class="{{ $sectionClass }}">A. Informasi Akademik</p>
                                    <table class="mt-3 w-full text-sm border-collapse">
                                        @php
    $rows = [
        ['Nama Lengkap', $detailSiswa->user->name],
        ['NIS (Nomor Induk)', $detailSiswa->nis],
        ['NISN', $detailSiswa->nisn],
        ['Jenjang / Kelas', $detailSiswa->jenjang . ' / ' . ($detailSiswa->kelas?->nama_kelas ?? '-')],
        ['Email Utama', $detailSiswa->user->email],
        ['Status Aktif', $detailSiswa->status],
    ];
                                        @endphp
                                        @foreach($rows as $row)
                                            <tr class="border-b border-slate-100 last:border-0">
                                                <td class="py-2 pr-4 text-slate-500 w-44 align-top whitespace-nowrap">{{ $row[0] }}
                                                </td>
                                                <td class="py-2 text-slate-900 font-semibold">
                                                    <span class="text-slate-400 mr-1">:</span>{{ $row[1] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>

                                {{-- B. Keterangan Pribadi --}}
                                <div>
                                    <p class="{{ $sectionClass }}">B. Keterangan Pribadi</p>
                                    <table class="mt-3 w-full text-sm border-collapse">
                                        @php
    $rows = [
        ['Tempat, Tgl Lahir', ($detailSiswa->tempat_lahir ?? '-') . ', ' . ($detailSiswa->tanggal_lahir ? \Carbon\Carbon::parse($detailSiswa->tanggal_lahir)->format('d F Y') : '-')],
        ['Agama', $detailSiswa->agama ?? '-'],
        ['Alamat Domisili', $detailSiswa->alamat ?? '-'],
    ];
                                        @endphp
                                        @foreach($rows as $row)
                                            <tr class="border-b border-slate-100 last:border-0">
                                                <td class="py-2 pr-4 text-slate-500 w-44 align-top whitespace-nowrap">{{ $row[0] }}
                                                </td>
                                                <td class="py-2 text-slate-900 font-semibold">
                                                    <span class="text-slate-400 mr-1">:</span>{{ $row[1] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>

                                {{-- C. Data Orang Tua --}}
                                <div>
                                    <p class="{{ $sectionClass }}">C. Data Orang Tua</p>
                                    <table class="mt-3 w-full text-sm border-collapse">
                                        @php
    $rows = [
        ['Nama Ayah', ($detailSiswa->nama_ayah ?? '-') . ' (' . ($detailSiswa->pekerjaan_ayah ?? '-') . ')'],
        ['Nama Ibu', ($detailSiswa->nama_ibu ?? '-') . ' (' . ($detailSiswa->pekerjaan_ibu ?? '-') . ')'],
        ['No. Telp / WA', $detailSiswa->no_telp_ortu ?? '-'],
    ];
                                        @endphp
                                        @foreach($rows as [$label, $value])
                                            <tr class="border-b border-slate-100 last:border-0">
                                                <td class="py-2 pr-4 text-slate-500 w-44 align-top whitespace-nowrap">{{ $label }}
                                                </td>
                                                <td
                                                    class="py-2 text-slate-900 font-semibold {{ $label === 'No. Telp / WA' ? 'text-emerald-600' : '' }}">
                                                    <span class="text-slate-400 mr-1">:</span>{{ $value }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>

                            </div>{{-- /data --}}
                        </div>{{-- /flex --}}

                        {{-- ── TANDA TANGAN ── --}}
                        @if(($schoolSettings['show_signature_on_print'] ?? '1') == '1')
                            <div class="mt-16 flex justify-end">
                                <div class="text-center w-56">
                                    <p class="text-xs text-slate-600">Dicetak Pada, {{ now()->format('d F Y') }}</p>
                                    <p class="text-xs font-bold text-slate-700 mt-0.5">
                                        {{ $schoolSettings['admin_signature_role'] ?? 'Admin Sekolah' }}
                                    </p>
                                    <div class="my-12 border-b border-dashed border-slate-300"></div>
                                    <p class="text-sm font-black text-slate-900 uppercase tracking-wide">
                                        {{ $schoolSettings['admin_signature_name'] ?? '( ................................ )' }}
                                    </p>
                                    <p class="text-[9px] text-slate-400 mt-1 uppercase tracking-widest italic">E-Document Verified
                                    </p>
                                </div>
                            </div>
                        @endif

                    </div>{{-- /body --}}
                </div>{{-- /doc-card --}}
            </div>{{-- /printable-area --}}

        @endif
        </x-ui.modal>
</div>
