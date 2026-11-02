<div class="space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold txt-primary">Modal Pengunjung</h1>
            <p class="mt-2 text-sm txt-muted">Tampilkan gambar informasi dan tautan kepada pengunjung website.</p>
        </div>
        <x-ui.button wire:click="create" variant="primary">Tambah Modal</x-ui.button>
    </div>

    <div class="rounded-2xl border border-indigo-500/15 bg-indigo-500/5 p-5 text-sm txt-secondary">
        Durasi tampil ulang diatur pada setiap modal. Setelah durasi tersebut, riwayat penayangan dibersihkan otomatis sehingga modal dapat tampil kembali untuk kombinasi IP dan browser/perangkat yang sama. Modal aktif terbaru diprioritaskan, maksimal satu modal per kunjungan halaman.
    </div>

    <x-ui.card padding="0" class="overflow-hidden">
        <div class="divide-y divide-indigo-500/10">
            @forelse($popups as $popup)
                <div wire:key="popup-{{ $popup->id }}" class="flex flex-col gap-4 p-6 lg:flex-row lg:items-center">
                    <img src="{{ asset('storage/'.$popup->gambar) }}" alt="{{ $popup->judul }}" class="h-28 w-44 shrink-0 rounded-xl border border-indigo-500/10 object-contain bg-slate-100">
                    <div class="min-w-0 flex-1 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-semibold txt-primary">{{ $popup->judul }}</h2>
                            <x-ui.badge :variant="$popup->is_active ? 'success' : 'secondary'">{{ $popup->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge>
                        </div>
                        <a href="{{ $popup->link }}" target="_blank" rel="noopener noreferrer" class="block break-all text-sm text-indigo-500 hover:underline">{{ $popup->link }}</a>
                        <p class="text-xs txt-muted">Tampil ulang setelah {{ $popup->display_interval_hours }} jam · {{ number_format($popup->impressions_count, 0, ',', '.') }} pengunjung sudah melihat</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <x-ui.button wire:click="edit({{ $popup->id }})" variant="secondary">Ubah</x-ui.button>
                        <x-ui.button wire:click="toggleStatus({{ $popup->id }})" variant="secondary">{{ $popup->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</x-ui.button>
                        <x-ui.button wire:click="delete({{ $popup->id }})" wire:confirm="Hapus modal ini beserta riwayat penayangannya?" variant="danger">Hapus</x-ui.button>
                    </div>
                </div>
            @empty
                <p class="p-12 text-center text-sm txt-muted">Belum ada modal pengunjung. Tambahkan gambar dan tautan informasi pertama.</p>
            @endforelse
        </div>
        <div class="px-6 py-4">{{ $popups->links() }}</div>
    </x-ui.card>

    <x-ui.modal name="website-popup-form" :show="$isModalOpen" maxWidth="2xl">
        <h2 class="mb-6 text-xl font-bold txt-primary">{{ $editId ? 'Ubah Modal Pengunjung' : 'Tambah Modal Pengunjung' }}</h2>
        <form wire:submit="save" class="space-y-5">
            <div>
                <x-ui.label for="popup-title" value="Judul / teks alternatif gambar" />
                <x-ui.input id="popup-title" wire:model="judul" type="text" class="mt-2 w-full" maxlength="150" />
                @error('judul')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <x-ui.label for="popup-link" value="Tautan tujuan" />
                <x-ui.input id="popup-link" wire:model="link" type="url" class="mt-2 w-full" placeholder="https://sekolah.sch.id/ppdb" />
                <p class="mt-1 text-xs txt-muted">Gunakan alamat lengkap dengan https:// atau http://.</p>
                @error('link')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <x-ui.label for="popup-display-interval" value="Tampilkan kembali setelah" />
                <div class="mt-2 flex max-w-xs items-center gap-3">
                    <x-ui.input id="popup-display-interval" wire:model="displayIntervalHours" type="number" min="1" max="8760" step="1" class="w-28" />
                    <span class="text-sm font-medium txt-primary">jam</span>
                </div>
                <p class="mt-1 text-xs txt-muted">Setelah durasi ini, riwayat pengunjung dihapus dan modal boleh ditampilkan kembali. Minimal 1 jam.</p>
                @error('displayIntervalHours')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <div>
                <x-ui.label for="popup-image" value="Gambar modal" />
                @php
                    $isPreviewImage = $gambar && in_array($gambar->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true);
                    $previewUrl = $isPreviewImage
                        ? $gambar->temporaryUrl()
                        : ($existingImage ? asset('storage/'.$existingImage) : null);
                    $previewName = $gambar?->getClientOriginalName() ?? ($existingImage ? basename($existingImage) : null);
                @endphp
                <input id="popup-image" wire:model="gambar" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only">

                <div class="mt-2 rounded-2xl border border-dashed border-indigo-300 bg-indigo-50/50 p-4 transition hover:border-indigo-500 hover:bg-indigo-50">
                    @if($previewUrl)
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <div class="flex h-28 w-full shrink-0 items-center justify-center overflow-hidden rounded-xl border border-indigo-500/10 bg-white sm:w-40">
                                <img src="{{ $previewUrl }}" alt="Pratinjau gambar modal" class="h-full w-full object-contain">
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium txt-primary">Gambar siap digunakan</p>
                                <p class="mt-1 truncate text-xs txt-muted">{{ $previewName }}</p>
                                <label for="popup-image" class="mt-3 inline-flex cursor-pointer items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-medium text-indigo-600 shadow-sm ring-1 ring-inset ring-indigo-200 transition hover:bg-indigo-50">
                                    Ganti gambar
                                </label>
                            </div>
                        </div>
                    @else
                        <label for="popup-image" class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl px-4 py-7 text-center">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-indigo-600 shadow-sm ring-1 ring-indigo-100" aria-hidden="true">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 16V4m0 0L8 8m4-4 4 4M5 20h14" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                            <span class="text-sm font-medium text-indigo-600">Pilih gambar dari perangkat</span>
                            <span class="text-xs txt-muted">Klik untuk memilih gambar yang akan ditampilkan pada modal.</span>
                        </label>
                    @endif
                </div>
                <p class="mt-2 text-xs txt-muted">JPG, PNG, atau WebP maksimal 4 MB. Gambar ditampilkan utuh sesuai proporsinya.</p>
                <p wire:loading wire:target="gambar" class="mt-2 text-xs text-indigo-600">Menyiapkan pratinjau gambar…</p>
                @error('gambar')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-3 text-sm txt-primary"><input wire:model="isActive" type="checkbox" class="rounded border-indigo-300 text-indigo-600"> Aktifkan modal ini</label>
            <div class="flex justify-end gap-3 border-t border-indigo-500/10 pt-5">
                <x-ui.button wire:click="closeModal" variant="secondary" type="button">Batal</x-ui.button>
                <x-ui.button type="submit" wire:loading.attr="disabled" wire:target="save,gambar">Simpan Modal</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
