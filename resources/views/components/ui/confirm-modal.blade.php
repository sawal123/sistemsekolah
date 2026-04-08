@props([
    'name',
    'title' => 'Konfirmasi Hapus',
    'message' => 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
    'onConfirm' => '',
])

<x-ui.modal :name="$name" maxWidth="sm">
    <div class="p-2 text-center">
        <!-- Icon -->
        <div class="w-16 h-16 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-500/20 shadow-lg shadow-red-500/10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>

        <h3 class="text-lg font-bold txt-primary mb-2">{{ $title }}</h3>
        <p class="text-sm txt-secondary mb-8 px-4 leading-relaxed">
            {{ $message }}
        </p>

        <div class="flex flex-col gap-2">
            <x-ui.button wire:click="{{ $onConfirm }}" variant="danger" class="w-full py-3 shadow-xl">
                Ya, Hapus Data
            </x-ui.button>
            <x-ui.button wire:click="closeModal" variant="secondary" class="w-full py-3 border border-slate-200 dark:border-white/5">
                Batalkan
            </x-ui.button>
        </div>
    </div>
</x-ui.modal>
