<div class="flex flex-col gap-6">
    <x-ui.toast />

    <div class="fu d1 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="txt-primary text-2xl font-extrabold">Struktur Organisasi</h1>
            <p class="txt-muted mt-1 text-sm">Upload gambar bagan organisasi yang akan ditampilkan pada halaman publik Kepegawaian.</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
        <form wire:submit="save" class="glass-card fu d2 h-fit rounded-2xl p-6">
            <h2 class="txt-primary text-base font-bold">Upload Gambar</h2>
            <p class="txt-muted mt-2 text-xs leading-5">Gunakan PNG, JPG, atau WebP. Disarankan format landscape dengan tulisan yang cukup besar.</p>

            <label class="mt-6 block cursor-pointer rounded-2xl border border-dashed border-indigo-500/30 bg-indigo-500/5 p-5 text-center transition hover:bg-indigo-500/10">
                <input wire:model="organizationImage" type="file" accept="image/*" class="sr-only">
                <svg class="mx-auto h-9 w-9 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="txt-primary mt-3 text-sm font-bold">Pilih gambar struktur</p>
                <p class="txt-muted mt-1 text-xs">Maksimal 5 MB</p>
            </label>
            @error('organizationImage')<p class="mt-2 text-xs text-red-500">{{ $message }}</p>@enderror

            @if($organizationImage)
                <img src="{{ $organizationImage->temporaryUrl() }}" alt="Preview struktur organisasi" class="mt-5 w-full rounded-xl border border-indigo-500/10">
            @endif

            <div class="mt-6 flex flex-col gap-3">
                <x-ui.button type="submit" variant="primary" class="w-full">
                    <span wire:loading.remove wire:target="save">Simpan Gambar</span>
                    <span wire:loading wire:target="save">Mengunggah...</span>
                </x-ui.button>
                <button type="button" wire:click="resetToDefault" wire:confirm="Kembalikan ke gambar struktur organisasi dummy?" class="w-full rounded-xl border border-indigo-500/20 px-4 py-2.5 text-sm font-semibold text-indigo-600 hover:bg-indigo-500/10">
                    Gunakan Gambar Dummy
                </button>
            </div>
        </form>

        <section class="glass-card fu d3 overflow-hidden rounded-2xl p-6">
            <div class="mb-5">
                <h2 class="txt-primary text-base font-bold">Gambar yang Sedang Ditampilkan</h2>
                <p class="txt-muted mt-1 text-xs">Perubahan akan langsung tampil pada halaman publik.</p>
            </div>
            @if($existingImage)
                <div class="overflow-auto rounded-2xl border border-indigo-500/10 bg-white p-3">
                    <img src="{{ asset('storage/' . $existingImage) }}" alt="Struktur organisasi saat ini" class="mx-auto max-h-[720px] w-auto rounded-xl">
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-indigo-500/20 p-16 text-center text-sm txt-muted">Belum ada gambar struktur organisasi.</div>
            @endif
        </section>
    </div>
</div>
