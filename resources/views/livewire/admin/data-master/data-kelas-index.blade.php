<div class="flex flex-col gap-6 pb-20">
    <x-ui.toast />

    {{-- Header Section --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="fu d1">
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Data Kelas</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Kelola pembagian kelas dan penempatan wali
                kelas.</p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button wire:click="openModal" variant="primary" class="shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kelas
            </x-ui.button>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-ui.card class="bg-indigo-500/5 border-indigo-500/10">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-500/10 rounded-xl text-indigo-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs txt-muted font-medium">Total Kelas</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['total_kelas'] }}</h3>
                </div>
            </div>
        </x-ui.card>
        <x-ui.card class="bg-emerald-500/5 border-emerald-500/10">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-500/10 rounded-xl text-emerald-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs txt-muted font-medium">Total Siswa</p>
                    <h3 class="text-xl font-bold txt-primary">{{ $stats['total_siswa'] }}</h3>
                </div>
            </div>
        </x-ui.card>
        @if($stats['sma_count'] > 0)
            <x-ui.card class="bg-blue-500/5 border-blue-500/10">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-500/10 rounded-xl text-blue-500 text-xs font-bold">SMA</div>
                    <div>
                        <p class="text-xs txt-muted font-medium">Kelas SMA</p>
                        <h3 class="text-xl font-bold txt-primary">{{ $stats['sma_count'] }}</h3>
                    </div>
                </div>
            </x-ui.card>
        @endif

        @if($stats['smp_count'] > 0)
            <x-ui.card class="bg-amber-500/5 border-amber-500/10">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-amber-500/10 rounded-xl text-amber-500 text-xs font-bold">SMP</div>
                    <div>
                        <p class="text-xs txt-muted font-medium">Kelas SMP</p>
                        <h3 class="text-xl font-bold txt-primary">{{ $stats['smp_count'] }}</h3>
                    </div>
                </div>
            </x-ui.card>
        @endif
    </div>

    {{-- Filter & Search Bar --}}
    

    {{-- Table Section --}}
    <x-ui.card padding="0">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 p-3">
            <div class="flex items-center gap-2 border-r border-indigo-500/10 pr-3">
                <x-ui.select wire:model.live="perPage" :options="['10' => '10', '20' => '20', '50' => '50']" />
            </div>
            <div class="lg:col-span-6 flex items-center gap-2">
                <x-ui.search model="search" placeholder="Cari Nama Kelas..." />
            </div>
        
            <div class="lg:col-span-4 flex items-center gap-2">
        
                <x-ui.select wire:model.live="filterJenjang" :options="['' => 'Semua Jenjang', 'SMP' => 'SMP', 'SMA' => 'SMA']"
                    placeholder="Filter Jenjang" />
        
            </div>
            </div>
        <div class="rounded-xl overflow-hidden border border-indigo-500/10 dark:border-white/10 m-3 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr class="bg-indigo-500/5 dark:bg-white/5 border-b border-indigo-500/10 dark:border-white/10">
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted w-[80px]">No
                            </th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Nama Kelas
                            </th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Jenjang</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Wali Kelas
                            </th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Jumlah Siswa
                            </th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                        @forelse($kelas as $index => $item)
                            <tr class="hover:bg-indigo-500/[0.02] dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4 text-sm txt-primary font-medium">
                                    {{ ($kelas->currentPage() - 1) * $kelas->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 text-sm txt-primary font-bold">
                                    {{ $item->nama_kelas }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="px-2 py-1 {{ $item->jenjang === 'SMA' ? 'bg-blue-500/10 text-blue-500' : 'bg-emerald-500/10 text-emerald-500' }} rounded-lg font-bold text-[11px] border border-current opacity-70">
                                        {{ $item->jenjang }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm txt-primary">
                                    @if($item->wali_kelas)
                                        <div class="flex flex-col">
                                            <span class="font-semibold">{{ $item->wali_kelas->user->name }}</span>
                                            <span class="text-[11px] txt-muted">NIP: {{ $item->wali_kelas->nip }}</span>
                                        </div>
                                    @else
                                        <span class="text-red-400 text-xs italic">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm txt-primary">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold">{{ $item->siswas_count }}</span>
                                        <span class="txt-muted">Siswa</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="viewStudents({{ $item->id }})"
                                            class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500 transition-all cursor-pointer"
                                            title="Lihat Siswa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
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
                                        <button wire:click="confirmDelete({{ $item->id }})"
                                            class="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-all cursor-pointer"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-40">
                                        <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <p class="text-sm">Tidak ada data kelas ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <x-ui.pagination :links="$kelas" />
    </x-ui.card>

    {{-- Form Modal --}}
    <x-ui.modal name="kelas-form" :show="$isModalOpen" maxWidth="lg"
        title="{{ $editId ? 'Edit Kelas' : 'Tambah Kelas' }}">
        <form wire:submit.prevent="save" class="space-y-6">
            <div>
                <x-ui.label for="nama_kelas" value="Nama Kelas" class="mb-2" />
                <x-ui.input wire:model="nama_kelas" id="nama_kelas" placeholder="Misal: X IPA 1, 9A, dll"
                    class="w-full" />
                @error('nama_kelas') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-ui.label value="Jenjang" class="mb-2" />
                    <x-ui.select wire:model="jenjang" :options="['SMP' => 'SMP', 'SMA' => 'SMA']"
                        placeholder="Pilih Jenjang" />
                    @error('jenjang') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-ui.label value="Wali Kelas" class="mb-2" />
                    <x-ui.select wire:model="wali_kelas_id" :options="$gurus" placeholder="Pilih Wali Kelas" />
                    @error('wali_kelas_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-indigo-500/10">
                <x-ui.button wire:click="closeModal" variant="secondary" type="button">
                    Batal
                </x-ui.button>
                <x-ui.button variant="primary" type="submit">
                    {{ $editId ? 'Simpan Perubahan' : 'Tambah Data' }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    {{-- View Students Modal --}}
    <x-ui.modal name="view-students-modal" maxWidth="2xl" title="Daftar Siswa: {{ $selectedKelas?->nama_kelas }}">
        @if($selectedKelas)
            <div class="overflow-hidden bg-indigo-500/5 rounded-2xl border border-indigo-500/10 mb-4 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-indigo-500/60 mb-0.5">Wali Kelas</p>
                        <p class="txt-primary font-bold">{{ $selectedKelas->wali_kelas?->user->name ?? '-' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold tracking-widest text-indigo-500/60 mb-0.5">Total Siswa</p>
                        <p class="txt-primary font-bold">{{ count($selectedKelas->siswas) }} Orang</p>
                    </div>
                </div>
            </div>

            <div
                class="overflow-x-auto max-h-[400px] overflow-y-auto custom-scrollbar rounded-xl border border-white/10 shadow-inner">
                <table class="w-full text-left">
                    <thead
                        class="sticky top-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md z-10 border-b border-white/10">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider txt-muted w-[50px]">No</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider txt-muted">NIS / NISN</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider txt-muted">Nama Lengkap</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider txt-muted">L/P</th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wider txt-muted text-right">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($selectedKelas->siswas as $siswa)
                            <tr class="hover:bg-indigo-500/[0.03] transition-colors">
                                <td class="px-4 py-3 text-sm txt-primary">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-xs txt-primary">
                                    <div class="flex flex-col">
                                        <span class="font-bold">{{ $siswa->nis }}</span>
                                        <span class="text-[10px] opacity-60">{{ $siswa->nisn }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm txt-primary font-medium">{{ $siswa->user->name }}</td>
                                <td class="px-4 py-3 text-sm">
                                    {{-- Assuming there's a gender or JK field. If not available yet, just dash --}}
                                    {{ $siswa->jenis_kelamin ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="removeStudent({{ $siswa->id }})"
                                        wire:confirm="Anda yakin ingin mengeluarkan siswa ini dari kelas?"
                                        class="p-1.5 rounded-lg hover:bg-red-500/10 text-red-500/60 hover:text-red-500 transition-all"
                                        title="Keluarkan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center txt-muted italic text-sm">Belum ada siswa di kelas
                                    ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-6 flex justify-end pt-4 border-t border-white/5">
            <x-ui.button @click="$dispatch('close-modal', 'view-students-modal')" variant="secondary">
                Tutup
            </x-ui.button>
        </div>
    </x-ui.modal>

    {{-- Confirm Delete Modal --}}
    <x-ui.confirm-modal name="confirm-delete-modal" title="Hapus Kelas"
        message="Apakah Anda yakin ingin menghapus data kelas ini? Wali kelas akan dilepaskan dan siswa akan kehilangan kelasnya."
        onConfirm="delete" />
</div>