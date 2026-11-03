<div class="space-y-6">
    <x-ui.toast />

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight txt-primary">Asset Media</h1>
            <p class="mt-1 text-sm txt-muted">Simpan gambar sekali dan gunakan kembali untuk konten website mana pun.</p>
        </div>
        <label class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
            Tambah gambar
            <input wire:model="uploads" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only">
        </label>
    </div>

    <x-ui.card class="p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <x-ui.input wire:model.live.debounce.300ms="search" type="search" placeholder="Cari nama atau teks alternatif gambar..." class="w-full sm:max-w-md" />
            <div class="text-xs txt-muted sm:ml-auto">JPG, PNG, WebP · maksimal 4 MB per gambar · hingga 20 gambar sekali unggah</div>
        </div>
        @error('uploads')<p class="mt-3 text-xs text-red-500">{{ $message }}</p>@enderror
        @error('uploads.*')<p class="mt-3 text-xs text-red-500">{{ $message }}</p>@enderror

        @if(count($uploads))
            <div class="mt-4 flex items-center justify-between gap-3 rounded-xl border border-indigo-500/15 bg-indigo-500/5 p-4">
                <span class="text-sm font-medium txt-primary">{{ count($uploads) }} gambar siap diunggah.</span>
                <x-ui.button wire:click="upload" wire:loading.attr="disabled" wire:target="upload,uploads" variant="primary">
                    <span wire:loading.remove wire:target="upload">Simpan ke Asset Media</span>
                    <span wire:loading wire:target="upload">Menyimpan...</span>
                </x-ui.button>
            </div>
        @endif
    </x-ui.card>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
        @forelse($assets as $asset)
            <x-ui.card padding="0" class="overflow-hidden">
                <img src="{{ $asset->url }}" alt="{{ $asset->alt_text ?: $asset->original_name }}" class="aspect-square w-full bg-slate-100 object-cover">
                <div class="space-y-2 p-4">
                    <p class="truncate text-sm font-bold txt-primary" title="{{ $asset->original_name }}">{{ $asset->original_name }}</p>
                    <p class="text-xs txt-muted">{{ $asset->width ? $asset->width . ' × ' . $asset->height . ' px · ' : '' }}{{ number_format($asset->size / 1024, 0) }} KB</p>
                    @if($asset->alt_text)<p class="truncate text-xs txt-secondary" title="{{ $asset->alt_text }}">{{ $asset->alt_text }}</p>@endif
                    <div class="flex gap-1 border-t border-indigo-500/10 pt-3">
                        <button x-data @click="navigator.clipboard.writeText('{{ $asset->url }}'); $dispatch('notify', { type: 'success', message: 'URL gambar disalin.' })" type="button" class="rounded-lg px-2 py-1.5 text-xs font-bold text-indigo-600 hover:bg-indigo-500/10">Salin URL</button>
                        <button wire:click="editAlt({{ $asset->id }})" type="button" class="rounded-lg px-2 py-1.5 text-xs font-bold text-indigo-600 hover:bg-indigo-500/10">Alt</button>
                        <button wire:click="confirmDelete({{ $asset->id }})" type="button" class="ml-auto rounded-lg px-2 py-1.5 text-xs font-bold text-red-500 hover:bg-red-500/10">Hapus</button>
                    </div>
                </div>
            </x-ui.card>
        @empty
            <x-ui.card class="col-span-full p-12 text-center">
                <p class="font-semibold txt-primary">Belum ada gambar di Asset Media.</p>
                <p class="mt-1 text-sm txt-muted">Unggah gambar agar dapat dipilih pada konten website.</p>
            </x-ui.card>
        @endforelse
    </div>

    <div>{{ $assets->links() }}</div>

    <x-ui.modal name="media-alt-form" maxWidth="md">
        <h2 class="text-lg font-bold txt-primary">Teks alternatif gambar</h2>
        <p class="mt-1 text-sm txt-muted">Deskripsi singkat untuk aksesibilitas dan SEO.</p>
        <form wire:submit="saveAlt" class="mt-5 space-y-4">
            <x-ui.input wire:model="altText" type="text" maxlength="180" class="w-full" placeholder="Contoh: Siswa belajar di laboratorium komputer" />
            @error('altText')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
            <div class="flex justify-end gap-3"><x-ui.button wire:click="closeAltModal" type="button" variant="secondary">Batal</x-ui.button><x-ui.button type="submit">Simpan</x-ui.button></div>
        </form>
    </x-ui.modal>

    <x-ui.confirm-modal name="delete-media-asset" title="Hapus Asset Media" message="Gambar yang tidak sedang digunakan akan dihapus permanen dari penyimpanan." onConfirm="delete" />
</div>
