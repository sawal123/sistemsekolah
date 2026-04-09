<div>
    <x-ui.toast />
    <style>
        @media print {

            body * {
                visibility: hidden;
            }

            .print-area,
            .print-area * {
                visibility: visible;
            }

            .print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .print-hide {
                display: none !important;
            }

            .no-print {
                display: none !important;
            }

            table {
                width: 100%;
                border-collapse: collapse !important;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
            }
        }
    </style>
    {{-- Custom Style for Print --}}

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

        <div class="fu d1">
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Mata Pelajaran</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Kelola kurikulum dan daftar mata pelajaran
                sekolah.</p>
        </div>

        <div class="flex flex-wrap md:flex-nowrap items-center gap-3 w-full md:w-auto action-buttons-container">
            {{-- Group for Import & Export (Between on mobile) --}}
            <div class="flex flex-1 md:flex-none items-center justify-between md:justify-start gap-3 w-full md:w-auto">
                {{-- Import Button --}}
                <x-ui.button wire:click="openImportModal" variant="secondary"
                    class="shadow-sm flex-1 md:flex-none justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Import
                </x-ui.button>

                {{-- Export Dropdown --}}
                <div x-data="{ open: false }" class="relative flex-1 md:flex-none">
                    <x-ui.button @click="open = !open" @click.away="open = false" variant="secondary"
                        class="shadow-sm w-full md:w-auto justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export
                        <svg class="w-4 h-4 ml-2 transition-transform duration-200" :class="{'rotate-180': open}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </x-ui.button>
                    <div x-show="open" x-transition.origin.top.right
                        class="absolute right-0 z-50 w-48 mt-2 glass rounded-2xl border border-white/10 shadow-xl overflow-hidden backdrop-blur-xl"
                        style="display: none;">
                        <button wire:click="exportExcel" @click="open = false"
                            class="w-full text-left px-4 py-3 text-sm hover:bg-indigo-500/10 transition-colors flex items-center gap-2 txt-primary">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Excel (.xlsx)
                        </button>
                        <button wire:click="exportPdf" @click="open = false"
                            class="w-full text-left px-4 py-3 text-sm hover:bg-indigo-500/10 transition-colors flex items-center gap-2 txt-primary">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            PDF (.pdf)
                        </button>
                        <button onclick="window.print()" @click="open = false"
                            class="w-full text-left px-4 py-3 text-sm hover:bg-indigo-500/10 transition-colors flex items-center gap-2 txt-primary border-t border-white/5">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2" />
                            </svg>
                            Cetak Laporan
                        </button>
                    </div>
                </div>
            </div>

            {{-- Create Button (Full width on mobile) --}}
            <x-ui.button wire:click="openModal" variant="primary" class="shadow-lg w-full md:w-auto justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Mapel
            </x-ui.button>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 my-4">
        {{-- Search Box --}}
        <div class="lg:col-span-5 relative group">
            <div
                class="absolute inset-y-0 left-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <x-ui.input wire:model.live.debounce.300ms="search" id="tahun" type="text"
                placeholder="Cari Kode atau Nama Mapel..." class="w-full" />
        </div>

        {{-- Filters --}}
        <div class="lg:col-span-7 flex flex-wrap items-center gap-3">
            {{-- Jenjang Filter --}}
            <div class="flex-1 min-w-[140px]">
                <x-ui.select wire:model.live="filterJenjang" :options="['' => 'Semua Jenjang', 'SMP' => 'SMP', 'SMA' => 'SMA', 'Umum' => 'Umum']" />
            </div>

            {{-- Kelompok Filter --}}
            <div class="flex-1 min-w-[160px]">
                <x-ui.select wire:model.live="filterKelompok" :options="['' => 'Semua Kelompok', 'Nasional' => 'Nasional', 'Kewilayahan' => 'Kewilayahan', 'Peminatan' => 'Peminatan', 'Mulok' => 'Mulok']" />
            </div>


        </div>
    </div>

    {{-- Table Section --}}
    <div class="print-area">
        <div class="hidden print:block mb-4 text-center">
            <h2>Laporan Mata Pelajaran</h2>
            {{-- <p>Tanggal Cetak: {{ now()->format('d/m/Y') }}</p> --}}
        </div>
        <x-ui.card padding="0" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr class="bg-indigo-500/5 dark:bg-white/5 border-b border-indigo-500/10 dark:border-white/10">
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted w-[80px]">No
                            </th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Kode Mapel
                            </th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Nama Mata
                                Pelajaran</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Kelompok</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Jenjang</th>
                            <th
                                class="print-hide px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right w-[150px]">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                        @forelse($mapels as $index => $item)
                            <tr class="hover:bg-indigo-500/[0.02] dark:hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-4 text-sm txt-primary font-medium">
                                    {{ ($mapels->currentPage() - 1) * $mapels->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span
                                        class="px-2 py-1 bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg font-bold text-[12px] border border-indigo-500/20">
                                        {{ $item->kode_mapel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm txt-primary font-semibold">
                                    {{ $item->nama_mapel }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @php
                                        $kelompokColor = match ($item->kelompok) {
                                            'Nasional' => 'success',
                                            'Peminatan' => 'indigo',
                                            'Kewilayahan' => 'warning',
                                            'Mulok' => 'secondary',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <x-ui.badge :variant="$kelompokColor">{{ $item->kelompok }}</x-ui.badge>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="flex items-center gap-2 txt-primary">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full {{ $item->jenjang === 'SMA' ? 'bg-blue-500' : ($item->jenjang === 'SMP' ? 'bg-emerald-500' : 'bg-slate-500') }}"></span>
                                        {{ $item->jenjang }}
                                    </span>
                                </td>
                                <td class="print-hide px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="edit({{ $item->id }})"
                                            class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500 transition-all cursor-pointer"
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
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                        <p class="text-sm">Tidak ada mata pelajaran yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-ui.pagination :links="$mapels" />
        </x-ui.card>
    </div>

    {{-- Form Modal --}}
    <x-ui.modal name="mapel-form" :show="$isModalOpen" maxWidth="lg">
        <div class="py-2">
            <h2 class="text-xl font-bold txt-primary mb-1">
                {{ $editId ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}
            </h2>
            <p class="text-sm txt-muted mb-6">Lengkapi detail mata pelajaran di bawah ini.</p>

            <form wire:submit.prevent="save" class="space-y-6">
                {{-- Kode Mapel --}}
                <div>
                    <x-ui.label for="kode_mapel" value="Kode Mapel" class="mb-2" />
                    <x-ui.input wire:model="kode_mapel" id="kode_mapel" type="text" placeholder="Misal: MTK-SMA-10"
                        class="w-full" />
                    @error('kode_mapel') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Nama Mapel --}}
                <div>
                    <x-ui.label for="nama_mapel" value="Nama Mata Pelajaran" class="mb-2" />
                    <x-ui.input wire:model="nama_mapel" id="nama_mapel" type="text"
                        placeholder="Misal: Matematika Wajib" class="w-full" />
                    @error('nama_mapel') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Kelompok & Jenjang Grid --}}
                <div class="grid grid-cols-2 gap-4">
                    {{-- Kelompok --}}
                    <x-ui.select label="Kelompok" wire:model="kelompok" :options="['Nasional', 'Kewilayahan', 'Peminatan', 'Mulok']" />

                    {{-- Jenjang --}}
                    <x-ui.select label="Jenjang" wire:model="jenjang" :options="['SMP', 'SMA', 'Umum']" />
                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-indigo-500/10">
                    <x-ui.button wire:click="closeModal" variant="secondary" type="button">
                        Batal
                    </x-ui.button>
                    <x-ui.button variant="primary" type="submit" class="shadow-lg shadow-indigo-500/20">
                        {{ $editId ? 'Simpan Perubahan' : 'Tambah Mapel' }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>

    {{-- Confirm Delete Modal --}}
    <x-ui.confirm-modal name="confirm-delete-modal" title="Hapus Mata Pelajaran"
        message="Apakah Anda yakin ingin menghapus mata pelajaran ini? Data nilai dan jadwal terkait mungkin akan terpengaruh."
        onConfirm="delete" />

    {{-- Import Modal --}}
    <x-ui.modal name="import-modal" title="Import Mata Pelajaran">
        <div class="py-4" x-data="{ fileName: '' }">
            <div class="mb-6 p-4 bg-indigo-500/5 rounded-xl border border-indigo-500/10">
                <h4 class="text-sm font-bold txt-primary mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Petunjuk Import
                </h4>
                <ul class="text-xs txt-muted space-y-1 ml-6 list-disc">
                    <li>Gunakan file dengan format <strong>.xlsx</strong> atau <strong>.csv</strong></li>
                    <li>Pastikan baris pertama adalah judul kolom (Heading Row)</li>
                    <li>Kolom wajib: <strong>kode_mapel</strong>, <strong>nama_mata_pelajaran</strong></li>
                    <li>Kolom opsional: <strong>kelompok</strong>, <strong>jenjang</strong></li>
                </ul>
            </div>

            <form wire:submit.prevent="importExcel" class="space-y-4">
                <div class="relative">
                    <x-ui.label for="importFile" value="Pilih File Excel" class="mb-2" />
                    <div class="flex items-center justify-center w-full">
                        <label for="importFile"
                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-indigo-500/20 rounded-xl cursor-pointer hover:bg-indigo-500/5 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-3 text-indigo-500 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <p class="mb-2 text-sm txt-primary">
                                    <span class="font-semibold"
                                        x-text="fileName ? 'File Terpilih' : 'Klik untuk upload'"></span>
                                    atau drag and drop
                                </p>
                                <p class="text-xs txt-muted" x-text="fileName || 'XLSX, XLS atau CSV (Maks. 10MB)'"></p>
                            </div>
                            <input wire:model="importFile" id="importFile" type="file" class="hidden"
                                accept=".xlsx,.xls,.csv"
                                @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" />
                        </label>
                    </div>
                    @error('importFile') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 mt-6">
                    <x-ui.button type="button" @click="$dispatch('close-modal', 'import-modal'); fileName = ''"
                        variant="secondary">
                        Batal
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="importFile, importExcel">Mulai Import</span>
                        <span wire:loading wire:target="importFile, importExcel">Memproses...</span>
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>
</div>