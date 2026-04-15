<div style="display:flex;flex-direction:column;gap:24px;height:100%;">
    <x-ui.toast />

    {{-- ── Header ──────────────────────────────────────── --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Master Data SPP</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Kelola tarif dan kategori biaya sekolah per jenjang & tahun ajaran.</p>
        </div>
        <x-ui.button wire:click="openModal" variant="primary" class="shadow-lg">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Tarif SPP
        </x-ui.button>
    </div>

    {{-- ── Filter Bar ───────────────────────────────────── --}}
    <x-ui.card padding="16px" class="overflow-visible" style="position: relative; z-index: 100;">
        <div class="flex flex-col lg:flex-row gap-4">
            {{-- Search --}}
            <div class="flex-1">
                <x-ui.input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari kategori atau keterangan..."
                    class="w-full"
                >
                    <x-slot name="prepend">
                        <svg class="w-4 h-4 txt-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </x-slot>
                </x-ui.input>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 min-w-[400px]">
                {{-- Filter Jenjang --}}
                <div class="flex-1">
                    <x-ui.select 
                        wire:model.live="filterJenjang"
                        :options="[
                            '' => 'Semua Jenjang',
                            'SMP' => 'SMP',
                            'SMA' => 'SMA',
                            'Semua' => 'Semua (SMP & SMA)'
                        ]"
                    />
                </div>

                {{-- Filter Tahun Ajaran --}}
                <div class="flex-1">
                    @php
                        $taOptions = ['' => 'Semua Tahun Ajaran'];
                        foreach($tahunAjarans as $ta) {
                            $taOptions[$ta->id] = $ta->tahun . ' - ' . $ta->semester;
                        }
                    @endphp
                    <x-ui.select 
                        wire:model.live="filterTahunAjaran"
                        :options="$taOptions"
                    />
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- ── Table ────────────────────────────────────────── --}}
    <x-ui.card padding="0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="border-collapse:separate;border-spacing:0;">
                <thead>
                    <tr class="bg-indigo-500/5 dark:bg-white/5 border-b border-indigo-500/10 dark:border-white/10">
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted w-12">No</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Kategori Biaya</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-center w-28">Jenjang</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Nominal</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Tahun Ajaran</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                    @forelse($spps as $index => $item)
                        <tr class="hover:bg-indigo-500/[0.02] dark:hover:bg-white/[0.02] transition-colors group">
                            <td class="px-5 py-4 text-sm txt-muted font-medium">
                                {{ ($spps->currentPage() - 1) * $spps->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    {{-- Icon berdasarkan kategori --}}
                                    @php
                                        $icon = match($item->kategori) {
                                            'SPP Bulanan'    => '🗓️',
                                            'Uang Bangunan'  => '🏫',
                                            'Uang Seragam'   => '👕',
                                            'Uang Kegiatan'  => '🎯',
                                            default          => '💰',
                                        };
                                    @endphp
                                    <span class="text-xl">{{ $icon }}</span>
                                    <div>
                                        <p class="text-sm txt-primary font-semibold">{{ $item->kategori }}</p>
                                        @if($item->keterangan)
                                            <p class="text-xs txt-muted mt-0.5">{{ Str::limit($item->keterangan, 60) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @php
                                    $jenjangColor = match($item->jenjang) {
                                        'SMP'   => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                        'SMA'   => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
                                        default => 'bg-slate-500/10 text-slate-400 border-slate-500/20',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $jenjangColor }}">
                                    {{ $item->jenjang }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-sm txt-primary font-bold">
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm txt-muted">
                                {{ $item->tahunAjaran?->tahun }} — {{ $item->tahunAjaran?->semester }}
                                @if($item->tahunAjaran?->is_active)
                                    <span class="ml-2 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded-md">AKTIF</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button wire:click="edit({{ $item->id }})"
                                        class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-400 hover:text-indigo-300 transition-all cursor-pointer"
                                        title="Edit Tarif">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $item->id }})"
                                        class="p-2 rounded-lg hover:bg-red-500/10 text-red-400 hover:text-red-300 transition-all cursor-pointer"
                                        title="Hapus Tarif">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center opacity-40">
                                    <svg class="w-12 h-12 mb-4 txt-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm txt-muted font-medium">Belum ada tarif SPP.</p>
                                    <p class="text-xs txt-muted mt-1">Klik "Tambah Tarif SPP" untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-ui.pagination :links="$spps" />
    </x-ui.card>

    {{-- ── Form Modal ────────────────────────────────────── --}}
    <x-ui.modal name="spp-form" :show="$isModalOpen" maxWidth="lg">
        <div class="py-1">
            <h2 class="text-xl font-bold txt-primary mb-1">
                {{ $editId ? 'Edit Tarif SPP' : 'Tambah Tarif SPP Baru' }}
            </h2>
            <p class="text-sm txt-muted mb-6">
                {{ $editId ? 'Perbarui informasi tarif yang sudah ada.' : 'Tentukan kategori biaya, jenjang, dan nominal tarif.' }}
            </p>

            <form wire:submit.prevent="save" class="space-y-4">
                {{-- Tahun Ajaran --}}
                <div>
                    @php
                        $taFormOptions = ['' => '-- Pilih Tahun Ajaran --'];
                        foreach($tahunAjarans as $ta) {
                            $taFormOptions[$ta->id] = $ta->tahun . ' — ' . $ta->semester . ($ta->is_active ? ' (Aktif)' : '');
                        }
                    @endphp
                    <x-ui.select 
                        label="Tahun Ajaran"
                        wire:model="tahun_ajaran_id"
                        :options="$taFormOptions"
                    />
                    @error('tahun_ajaran_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Jenjang & Kategori row --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-ui.select 
                            label="Jenjang"
                            wire:model="jenjang"
                            :options="[
                                'Semua' => 'Semua (SMP & SMA)',
                                'SMP' => 'SMP',
                                'SMA' => 'SMA'
                            ]"
                        />
                        @error('jenjang') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        @php
                            $katOptions = [];
                            foreach(\App\Models\Spp::KATEGORIS as $kat) {
                                $katOptions[$kat] = $kat;
                            }
                        @endphp
                        <x-ui.select 
                            label="Kategori Biaya"
                            wire:model="kategori"
                            :options="$katOptions"
                        />
                        @error('kategori') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Nominal --}}
                <div class="mb-4">
                    <x-ui.input 
                        label="Nominal (Rp)" 
                        wire:model="nominal" 
                        type="number" 
                        min="0" 
                        step="1000"
                        placeholder="Masukkan nominal tarif..."
                    >
                        <x-slot name="prepend">
                            <span class="text-sm txt-muted font-bold">Rp</span>
                        </x-slot>
                    </x-ui.input>
                    @error('nominal') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Keterangan --}}
                <div class="mb-4">
                    <x-ui.label value="Keterangan (opsional)" class="mb-1.5" />
                    <textarea wire:model="keterangan" rows="2"
                        placeholder="Contoh: Tarif berlaku mulai Juli 2025"
                        class="w-full px-4 py-2.5 rounded-xl text-sm bg-white/5 border border-indigo-500/20 txt-primary focus:outline-none focus:ring-2 focus:ring-indigo-500/30 resize-none transition-all"></textarea>
                    @error('keterangan') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-indigo-500/10">
                    <x-ui.button wire:click="closeModal" variant="secondary" type="button">Batal</x-ui.button>
                    <x-ui.button variant="primary" type="submit">
                        {{ $editId ? 'Simpan Perubahan' : 'Tambah Tarif' }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>

    {{-- ── Confirm Delete Modal ─────────────────────────── --}}
    <x-ui.confirm-modal
        name="confirm-delete-modal"
        title="Hapus Tarif SPP"
        message="Apakah Anda yakin ingin menghapus tarif ini? Tindakan ini tidak dapat dibatalkan."
        onConfirm="delete"
    />
</div>
