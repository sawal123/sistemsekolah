<div style="display:flex;flex-direction:column;gap:24px;height:100%;">
    <x-ui.toast />
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="fu d1">
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Data Tahun Ajaran & Semester</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Kelola periode akademik aktif dan riwayat semester sekolah.</p>
        </div>
        
        <x-ui.button wire:click="openModal" variant="primary" class="shadow-lg">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Data
        </x-ui.button>
    </div>

    {{-- Main Table Section --}}
    <x-ui.card padding="0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="border-collapse: separate; border-spacing: 0;">
                <thead>
                    <tr class="bg-indigo-500/5 dark:bg-white/5 border-b border-indigo-500/10 dark:border-white/10">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted w-[80px]">No</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Semester</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-center w-[150px]">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right w-[250px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                    @forelse($tahunAjarans as $index => $item)
                        <tr class="hover:bg-indigo-500/[0.02] dark:hover:bg-white/[0.02] transition-colors group">
                            <td class="px-6 py-4 text-sm txt-primary font-medium">
                                {{ ($tahunAjarans->currentPage() - 1) * $tahunAjarans->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 text-sm txt-primary font-semibold">
                                {{ $item->tahun }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="txt-primary">{{ $item->semester }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->is_active)
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="secondary">Tidak Aktif</x-ui.badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$item->is_active)
                                        <button wire:click="toggleStatus({{ $item->id }})" 
                                            class="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-all cursor-pointer" 
                                            title="Set Aktif">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                    @endif
                                    
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
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center opacity-40">
                                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-sm">Belum ada data tahun ajaran.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <x-ui.pagination :links="$tahunAjarans" />
    </x-ui.card>

    {{-- Form Modal --}}
    <x-ui.modal name="tahun-ajaran-form" :show="$isModalOpen" maxWidth="lg">
        <div class="py-2">
            <h2 class="text-xl font-bold txt-primary mb-1">
                {{ $editId ? 'Edit Data Tahun Ajaran' : 'Tambah Tahun Ajaran' }}
            </h2>
            <p class="text-sm txt-muted mb-6">Lengkapi informasi periode akademik di bawah ini.</p>

            <form wire:submit.prevent="save">
                <div class="mb-6">
                    <x-ui.label for="tahun" value="Tahun Ajaran" class="mb-2 txt-secondary" />
                    <x-ui.input wire:model="tahun" id="tahun" type="text" placeholder="Contoh: 2025/2026" class="w-full" />
                </div>

                <div class="mb-6">
                    <x-ui.select 
                        label="Semester" 
                        wire:model="semester" 
                        :options="['Ganjil', 'Genap']" 
                        placeholder="Pilih Semester"
                    />
                </div>

                <div class="flex items-center gap-3 bg-indigo-500/[0.03] dark:bg-indigo-500/5 p-4 rounded-xl border border-indigo-500/10 mb-8">
                    <input wire:model="is_active" type="checkbox" id="is_active" class="w-5 h-5 rounded border-indigo-500/30 text-indigo-600 focus:ring-indigo-500 bg-white/50 dark:bg-white/10 cursor-pointer" />
                    <x-ui.label for="is_active" value="Set sebagai semester aktif" class="cursor-pointer txt-primary" />
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
        </div>
    </x-ui.modal>

    {{-- Confirm Delete Modal --}}
    <x-ui.confirm-modal 
        name="confirm-delete-modal" 
        title="Hapus Tahun Ajaran"
        message="Apakah Anda yakin ingin menghapus data tahun ajaran ini? Seluruh data yang terkait mungkin akan terpengaruh."
        onConfirm="delete"
    />
</div>