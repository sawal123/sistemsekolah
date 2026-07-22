<div style="display:flex;flex-direction:column;gap:24px;height:100%;">
    <x-ui.toast />

    {{-- Header Section --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="fu d1">
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Data Fasilitas Sekolah</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Kelola inventaris sarana dan prasarana penunjang kegiatan belajar mengajar.</p>
        </div>
        
        <x-ui.button wire:click="openModal" variant="primary" class="shadow-lg w-full md:w-auto justify-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Fasilitas
        </x-ui.button>
    </div>

    {{-- Main Card containing Filter & Table --}}
    <x-ui.card padding="0" class="fu d2">
        {{-- Filter & Search Bar --}}
        <div class="flex flex-wrap items-center gap-3 my-4 p-3">
            {{-- Per Page --}}
            <div class="w-20 flex-shrink-0">
                <x-ui.select wire:model.live="perPage" :options="['10' => '10', '20' => '20', '50' => '50']" />
            </div>

            {{-- Divider --}}
            <div class="h-7 w-px bg-indigo-500/20 flex-shrink-0 hidden sm:block"></div>

            {{-- Search --}}
            <div class="flex-1 min-w-[180px]">
                <x-ui.search model="search" placeholder="Cari nama atau lokasi fasilitas..." />
            </div>

            {{-- Filter Kategori --}}
            <div class="w-48 flex-shrink-0">
                <x-ui.select 
                    wire:model.live="filterKategori" 
                    :options="$filterKategoriOptions" 
                    placeholder="Semua Kategori"
                />
            </div>

            {{-- Filter Kondisi --}}
            <div class="w-48 flex-shrink-0">
                <x-ui.select 
                    wire:model.live="filterKondisi" 
                    :options="$filterKondisiOptions" 
                    placeholder="Semua Kondisi"
                />
            </div>
        </div>

        {{-- Table Container with border and overflow-x-auto --}}
        <div class="rounded-xl overflow-hidden border border-indigo-500/10 dark:border-white/10 m-3 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr class="bg-indigo-500/5 dark:bg-white/5 border-b border-indigo-500/10 dark:border-white/10">
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted w-[80px]">No</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted w-[100px]">Foto</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Nama Fasilitas</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Kategori</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Lokasi</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-center w-[100px]">Jumlah</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-center w-[150px]">Kondisi</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right w-[180px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                        @forelse($fasilitas as $index => $item)
                            <tr class="hover:bg-indigo-500/[0.02] dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4 text-sm txt-primary font-medium">
                                    {{ ($fasilitas->currentPage() - 1) * $fasilitas->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden glass border border-indigo-500/10 flex items-center justify-center flex-shrink-0">
                                        @if($item->foto)
                                            <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_fasilitas }}" class="w-full h-full object-cover" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500/10 to-purple-500/10 text-indigo-500">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-sm txt-primary">{{ $item->nama_fasilitas }}</div>
                                    @if($item->deskripsi)
                                        <div class="text-xs txt-muted mt-0.5 line-clamp-1 max-w-xs">{{ $item->deskripsi }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm txt-secondary font-medium">
                                    {{ $item->kategori }}
                                </td>
                                <td class="px-6 py-4 text-sm txt-secondary">
                                    {{ $item->lokasi ?: '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm txt-primary font-bold text-center">
                                    {{ $item->jumlah }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($item->kondisi === 'Baik')
                                        <x-ui.badge variant="success">Baik</x-ui.badge>
                                    @elseif($item->kondisi === 'Rusak Ringan')
                                        <x-ui.badge variant="warning">Rusak Ringan</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="danger">Rusak Berat</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="edit({{ $item->id }})" 
                                            class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500 transition-all cursor-pointer" 
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        
                                        <button wire:click="confirmDelete({{ $item->id }})" 
                                            class="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-all cursor-pointer" 
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center opacity-40">
                                        <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <p class="text-sm font-medium">Belum ada data fasilitas sekolah.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <x-ui.pagination :links="$fasilitas" />
    </x-ui.card>

    {{-- Form Modal --}}
    <x-ui.modal name="fasilitas-form" :show="$isModalOpen" maxWidth="2xl">
        <div class="py-2">
            <h2 class="text-xl font-bold txt-primary mb-1">
                {{ $editId ? 'Edit Data Fasilitas' : 'Tambah Fasilitas Baru' }}
            </h2>
            <p class="text-sm txt-muted mb-6">Lengkapi informasi inventaris fasilitas sekolah di bawah ini.</p>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    {{-- Nama Fasilitas --}}
                    <div>
                        <x-ui.label for="nama_fasilitas" value="Nama Fasilitas *" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="nama_fasilitas" id="nama_fasilitas" type="text" placeholder="Contoh: AC Panasonic 2 PK, Kursi Siswa" class="w-full" />
                        @error('nama_fasilitas') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <x-ui.select 
                            label="Kategori *" 
                            wire:model="kategori" 
                            :options="$kategoriOptions" 
                            placeholder="Pilih Kategori"
                        />
                        @error('kategori') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Jumlah --}}
                    <div>
                        <x-ui.label for="jumlah" value="Jumlah Unit *" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="jumlah" id="jumlah" type="number" min="1" class="w-full" />
                        @error('jumlah') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Kondisi --}}
                    <div>
                        <x-ui.select 
                            label="Kondisi *" 
                            wire:model="kondisi" 
                            :options="$kondisiOptions" 
                            placeholder="Pilih Kondisi"
                        />
                        @error('kondisi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Lokasi --}}
                    <div class="md:col-span-2">
                        <x-ui.label for="lokasi" value="Lokasi / Ruangan" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="lokasi" id="lokasi" type="text" placeholder="Contoh: Gedung B Lantai 2, Ruang Lab Fisika" class="w-full" />
                        @error('lokasi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="mb-6">
                    <x-ui.label for="deskripsi" value="Deskripsi / Keterangan Tambahan" class="mb-2 txt-secondary" />
                    <textarea wire:model="deskripsi" id="deskripsi" rows="3" placeholder="Informasi tambahan mengenai spesifikasi, serial number, atau detail lainnya..." 
                        class="w-full px-4 py-2.5 glass border-2 border-transparent rounded-xl text-sm font-medium focus:border-indigo-500/50 transition-all txt-primary focus:outline-none focus:ring-4 focus:ring-indigo-500/15 placeholder-slate-400 dark:placeholder-slate-500 bg-white/50 dark:bg-slate-900/50"></textarea>
                    @error('deskripsi') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Upload Foto --}}
                <div class="mb-8">
                    <x-ui.label value="Foto Fasilitas" class="mb-2 txt-secondary" />
                    <div class="flex flex-col md:flex-row gap-6 items-center">
                        {{-- Foto Preview --}}
                        <div class="w-28 h-28 rounded-2xl overflow-hidden glass border border-indigo-500/10 flex items-center justify-center flex-shrink-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5">
                            @if($foto)
                                <img src="{{ $foto->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover" />
                            @elseif($existingFoto)
                                <img src="{{ asset('storage/' . $existingFoto) }}" alt="Preview" class="w-full h-full object-cover" />
                            @else
                                <div class="text-center txt-muted flex flex-col items-center gap-1.5">
                                    <svg class="w-8 h-8 opacity-40 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px]">No Photo</span>
                                </div>
                            @endif
                        </div>

                        {{-- Input Upload --}}
                        <div class="flex-1 w-full">
                            <div class="relative w-full flex items-center justify-center px-6 py-6 border-2 border-dashed border-indigo-500/20 hover:border-indigo-500/50 rounded-2xl cursor-pointer bg-white/30 dark:bg-white/[0.02] transition-colors group">
                                <input type="file" wire:model="foto" id="foto" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" />
                                <div class="text-center">
                                    <svg class="mx-auto h-8 w-8 text-indigo-500/60 group-hover:text-indigo-500 transition-colors mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4-4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="text-xs txt-primary font-medium">Klik untuk upload foto baru</p>
                                    <p class="text-[10px] txt-muted mt-1">PNG, JPG, JPEG (Max. 4MB)</p>
                                </div>
                            </div>
                            <div wire:loading wire:target="foto" class="mt-2 text-xs text-indigo-500 font-semibold flex items-center gap-1.5 animate-pulse">
                                <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sedang mengunggah berkas...
                            </div>
                            @error('foto') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-indigo-500/10">
                    <x-ui.button wire:click="closeModal" variant="secondary" type="button" class="cursor-pointer">
                        Batal
                    </x-ui.button>
                    <x-ui.button variant="primary" type="submit" class="cursor-pointer">
                        {{ $editId ? 'Simpan Perubahan' : 'Tambah Fasilitas' }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>

    {{-- Confirm Delete Modal --}}
    <x-ui.confirm-modal 
        name="confirm-delete-modal" 
        title="Hapus Fasilitas Sekolah"
        message="Apakah Anda yakin ingin menghapus data fasilitas ini? Tindakan ini tidak dapat dibatalkan dan foto terkait akan dihapus secara permanen."
        onConfirm="delete"
    />
</div>
