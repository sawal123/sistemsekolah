<div class="mx-auto flex h-full w-full max-w-6xl flex-col gap-5">
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <h1 class="text-2xl font-extrabold txt-primary">Bantuan AI</h1>
            <p class="mt-1 text-sm txt-muted">Tanyakan cara memakai sistem sekolah. Jawaban AI bersifat panduan dan tidak mengubah data.</p>
        </div>
        <x-ui.button wire:click="newConversation" variant="secondary">Percakapan Baru</x-ui.button>
    </div>

    @if(!config('ai-chat.enabled'))
        <div class="rounded-2xl border border-amber-400/40 bg-amber-50 p-4 text-sm text-amber-800 dark:bg-amber-950/30 dark:text-amber-200">
            Bantuan AI sedang dinonaktifkan oleh administrator.
        </div>
    @else
        <div class="grid min-h-0 flex-1 gap-5 lg:grid-cols-[280px_minmax(0,1fr)]">
            <aside class="modal-surface flex min-h-48 flex-col overflow-hidden p-3 lg:min-h-0">
                <p class="px-2 pb-2 text-xs font-bold uppercase tracking-wider txt-muted">Riwayat percakapan</p>
                <div class="min-h-0 space-y-1 overflow-y-auto">
                    @forelse($conversations as $conversation)
                        <div wire:key="conversation-{{ $conversation->id }}" class="group flex items-center gap-1 rounded-xl {{ $activeConversation?->id === $conversation->id ? 'bg-indigo-500/10' : 'hover:bg-slate-100 dark:hover:bg-white/5' }}">
                            <button wire:click="selectConversation({{ $conversation->id }})" class="min-w-0 flex-1 px-3 py-2.5 text-left">
                                <span class="block truncate text-sm font-medium txt-primary">{{ $conversation->title }}</span>
                                <span class="mt-0.5 block text-xs txt-muted">{{ $conversation->last_message_at?->diffForHumans() ?? 'Belum ada pesan' }}</span>
                            </button>
                            <button wire:click="deleteConversation({{ $conversation->id }})" wire:confirm="Hapus percakapan ini?" class="mr-2 hidden rounded-lg p-1.5 text-slate-400 hover:bg-red-500/10 hover:text-red-600 group-hover:inline-flex" aria-label="Hapus percakapan">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12m-9 0V4h6v3m-7 4v6m4-6v6m-7 3h10l1-13H4l1 13Z"/></svg>
                            </button>
                        </div>
                    @empty
                        <p class="px-3 py-6 text-center text-sm txt-muted">Mulai percakapan baru untuk melihat riwayat.</p>
                    @endforelse
                </div>
            </aside>

            <section class="modal-surface flex min-h-[560px] flex-col overflow-hidden">
                <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-700">
                    <p class="font-semibold txt-primary">{{ $activeConversation?->title ?? 'Percakapan baru' }}</p>
                    <p class="mt-1 text-xs txt-muted">Jangan masukkan kata sandi, API key, atau data pribadi sensitif.</p>
                </div>

                <div class="flex-1 space-y-4 overflow-y-auto p-5" aria-live="polite">
                    @forelse($messages as $chatMessage)
                        <div class="flex {{ $chatMessage->role === 'user' ? 'justify-end' : 'justify-start' }}">
                            <article class="max-w-[85%] rounded-2xl px-4 py-3 text-sm leading-6 {{ $chatMessage->role === 'user' ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white' : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-100' }}">
                                {!! nl2br(e($chatMessage->content)) !!}
                            </article>
                        </div>
                    @empty
                        <div class="flex h-full min-h-72 items-center justify-center text-center">
                            <div class="max-w-sm">
                                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-500/10 text-indigo-600">?
                                </div>
                                <p class="font-semibold txt-primary">Ada yang bisa dibantu?</p>
                                <p class="mt-1 text-sm txt-muted">Contoh: “Bagaimana cara mengatur kelas?” atau “Di mana menu PPDB?”</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <form wire:submit="send" class="border-t border-slate-200 p-4 dark:border-slate-700">
                    @if($chatError)
                        <p class="mb-3 rounded-xl bg-red-50 px-3 py-2 text-sm text-red-700 dark:bg-red-950/30 dark:text-red-200">{{ $chatError }}</p>
                    @endif
                    <div class="flex items-end gap-3">
                        <div class="flex-1">
                            <label for="ai-chat-message" class="sr-only">Pertanyaan bantuan</label>
                            <textarea id="ai-chat-message" wire:model="message" rows="3" maxlength="{{ config('ai-chat.max_message_length') }}" class="form-control-surface block w-full resize-none rounded-xl border-2 px-3 py-2 text-sm txt-primary outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/15" placeholder="Tulis pertanyaan Anda..."></textarea>
                            @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <x-ui.button type="submit" wire:loading.attr="disabled" wire:target="send">
                            <span wire:loading.remove wire:target="send">Kirim</span>
                            <span wire:loading wire:target="send">Memproses…</span>
                        </x-ui.button>
                    </div>
                </form>
            </section>
        </div>
    @endif
</div>
