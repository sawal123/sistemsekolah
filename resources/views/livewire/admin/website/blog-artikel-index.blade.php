<div style="display:flex;flex-direction:column;gap:24px;height:100%;">
    <x-ui.toast />

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="fu d1">
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Blog</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Kelola artikel, media, SEO, tag, kategori, dan komentar website sekolah.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <x-ui.button wire:click="openCategoryModal" variant="secondary">Kategori</x-ui.button>
            <x-ui.button wire:click="openTagModal" variant="secondary">Tag</x-ui.button>
            <a href="{{ route('admin.website.blog-artikel.create') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2 font-semibold text-sm rounded-xl transition-all duration-200 bg-gradient-to-r from-indigo-500 to-purple-500 text-white hover:from-indigo-600 hover:to-purple-600 shadow-md shadow-indigo-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Tulis Artikel
            </a>
        </div>
    </div>

    <div class="fu d2 grid grid-cols-1 md:grid-cols-4 gap-3">
        <x-ui.card class="p-5"><p class="txt-muted text-xs font-bold uppercase tracking-wider">Artikel</p><p class="txt-primary mt-2 text-2xl font-extrabold">{{ $posts->total() }}</p></x-ui.card>
        <x-ui.card class="p-5"><p class="txt-muted text-xs font-bold uppercase tracking-wider">Tag</p><p class="txt-primary mt-2 text-2xl font-extrabold">{{ $tags->count() }}</p></x-ui.card>
        <x-ui.card class="p-5"><p class="txt-muted text-xs font-bold uppercase tracking-wider">Kategori</p><p class="txt-primary mt-2 text-2xl font-extrabold">{{ $categories->count() }}</p></x-ui.card>
        <x-ui.card class="p-5"><p class="txt-muted text-xs font-bold uppercase tracking-wider">Komentar</p><p class="txt-primary mt-2 text-2xl font-extrabold">{{ $comments->total() }}</p></x-ui.card>
    </div>

    <div class="fu d3 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="inline-flex flex-wrap gap-2 rounded-2xl border border-indigo-500/10 bg-white/35 p-1 dark:bg-white/5 dark:border-white/10">
            <button wire:click="setTab('posts')" class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $tab === 'posts' ? 'bg-indigo-500 text-white shadow-md shadow-indigo-500/25' : 'txt-secondary hover:bg-indigo-500/10' }}">Artikel</button>
            <button wire:click="setTab('taxonomy')" class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $tab === 'taxonomy' ? 'bg-indigo-500 text-white shadow-md shadow-indigo-500/25' : 'txt-secondary hover:bg-indigo-500/10' }}">Tag & Kategori</button>
            <button wire:click="setTab('comments')" class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $tab === 'comments' ? 'bg-indigo-500 text-white shadow-md shadow-indigo-500/25' : 'txt-secondary hover:bg-indigo-500/10' }}">Komentar</button>
        </div>

        <div class="flex flex-col gap-2 md:flex-row">
            <input wire:model.live.debounce.350ms="search" type="search" placeholder="Cari blog..." class="w-full md:w-72 rounded-xl border border-indigo-500/10 bg-white/60 px-4 py-2.5 text-sm txt-primary outline-none focus:border-indigo-500/50 dark:bg-white/5 dark:border-white/10" />
            @if($tab === 'posts')
                <select wire:model.live="statusFilter" class="rounded-xl border border-indigo-500/10 bg-white/60 px-3 py-2.5 text-sm txt-primary outline-none dark:bg-white/5 dark:border-white/10">
                    <option value="">Semua Status</option>
                    <option value="Draft">Draft</option>
                    <option value="Published">Published</option>
                </select>
                <select wire:model.live="kategoriFilter" class="rounded-xl border border-indigo-500/10 bg-white/60 px-3 py-2.5 text-sm txt-primary outline-none dark:bg-white/5 dark:border-white/10">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>

    @if($tab === 'posts')
        <x-ui.card padding="0" class="fu d4 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="border-collapse:separate;border-spacing:0;">
                    <thead>
                        <tr class="bg-indigo-500/5 dark:bg-white/5 border-b border-indigo-500/10 dark:border-white/10">
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Artikel</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Kategori & Tag</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-center">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-center">Komentar</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                        @forelse($posts as $post)
                            <tr class="hover:bg-indigo-500/[0.02] dark:hover:bg-white/[0.02] transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3 min-w-[340px]">
                                        @if($post->thumbnail)
                                            <img src="{{ asset('storage/'.$post->thumbnail) }}" alt="Thumbnail {{ $post->judul }}" class="w-16 h-12 rounded-xl object-cover border border-indigo-500/10">
                                        @else
                                            <div class="w-16 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/10 flex items-center justify-center text-indigo-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4-4 4 4 4-4 4 4M4 20h16M4 4h16v12H4z" /></svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="txt-primary text-sm font-bold">{{ $post->judul }}</p>
                                            <p class="txt-muted text-xs mt-1">/{{ $post->slug }} | {{ $post->user->name ?? 'Admin' }} | {{ $post->created_at->format('d M Y') }}</p>
                                            @if($post->meta_title)
                                                <p class="txt-muted text-xs mt-1">SEO: {{ $post->meta_title }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="txt-secondary text-sm font-semibold">{{ $post->kategori->nama_kategori ?? '-' }}</p>
                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        @forelse($post->tags as $tag)
                                            <span class="badge bg-indigo-500/10 text-indigo-600 dark:text-indigo-300">#{{ $tag->nama_tag }}</span>
                                        @empty
                                            <span class="txt-muted text-xs">Tanpa tag</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <x-ui.badge :variant="$post->status === 'Published' ? 'success' : 'secondary'">{{ $post->status }}</x-ui.badge>
                                </td>
                                <td class="px-6 py-4 text-center txt-primary text-sm font-semibold">{{ $post->comments_count }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.website.blog-artikel.edit', $post) }}" wire:navigate class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500 transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4.768L19.768 9a2.5 2.5 0 10-3.536-3.536L5.232 16.464 4 20z" /></svg>
                                        </a>
                                        <button wire:click="confirmDelete('post', {{ $post->id }})" class="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-16 text-center txt-muted text-sm">Belum ada artikel blog.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-ui.pagination :links="$posts" />
        </x-ui.card>
    @elseif($tab === 'taxonomy')
        <div class="fu d4 grid grid-cols-1 lg:grid-cols-2 gap-4">
            <x-ui.card padding="0" class="overflow-hidden">
                <div class="px-6 py-4 border-b border-indigo-500/10 dark:border-white/10 flex items-center justify-between">
                    <h2 class="txt-primary text-base font-bold">Tag</h2>
                    <button wire:click="openTagModal" class="text-sm font-semibold text-indigo-500 hover:text-indigo-600">Tambah Tag</button>
                </div>
                <div class="divide-y divide-indigo-500/10 dark:divide-white/10">
                    @forelse($tags as $tag)
                        <div class="px-6 py-4 flex items-center justify-between gap-3">
                            <div><p class="txt-primary text-sm font-bold">#{{ $tag->nama_tag }}</p><p class="txt-muted text-xs">{{ $tag->posts_count }} artikel</p></div>
                            <div class="flex items-center gap-2">
                                <button wire:click="editTag({{ $tag->id }})" class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500">Edit</button>
                                <button wire:click="confirmDelete('tag', {{ $tag->id }})" class="p-2 rounded-lg hover:bg-red-500/10 text-red-500">Hapus</button>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center txt-muted text-sm">Belum ada tag.</div>
                    @endforelse
                </div>
            </x-ui.card>

            <x-ui.card padding="0" class="overflow-hidden">
                <div class="px-6 py-4 border-b border-indigo-500/10 dark:border-white/10 flex items-center justify-between">
                    <h2 class="txt-primary text-base font-bold">Kategori</h2>
                    <button wire:click="openCategoryModal" class="text-sm font-semibold text-indigo-500 hover:text-indigo-600">Tambah Kategori</button>
                </div>
                <div class="divide-y divide-indigo-500/10 dark:divide-white/10">
                    @forelse($categories as $category)
                        <div class="px-6 py-4 flex items-center justify-between gap-3">
                            <div><p class="txt-primary text-sm font-bold">{{ $category->nama_kategori }}</p><p class="txt-muted text-xs">{{ $category->posts_count }} artikel</p></div>
                            <div class="flex items-center gap-2">
                                <button wire:click="editCategory({{ $category->id }})" class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500">Edit</button>
                                <button wire:click="confirmDelete('category', {{ $category->id }})" class="p-2 rounded-lg hover:bg-red-500/10 text-red-500">Hapus</button>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center txt-muted text-sm">Belum ada kategori.</div>
                    @endforelse
                </div>
            </x-ui.card>
        </div>
    @else
        <x-ui.card padding="0" class="fu d4 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left" style="border-collapse:separate;border-spacing:0;">
                    <thead>
                        <tr class="bg-indigo-500/5 dark:bg-white/5 border-b border-indigo-500/10 dark:border-white/10">
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Komentar</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Artikel</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-center">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                        @forelse($comments as $comment)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="txt-primary text-sm font-bold">{{ $comment->nama }}</p>
                                    <p class="txt-muted text-xs mt-1">{{ $comment->email ?: 'Email tidak diisi' }} | {{ $comment->created_at->format('d M Y H:i') }}</p>
                                    <p class="txt-secondary text-sm mt-2 max-w-xl">{{ $comment->komentar }}</p>
                                </td>
                                <td class="px-6 py-4 txt-primary text-sm font-semibold">{{ $comment->post->judul ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <select wire:change="updateCommentStatus({{ $comment->id }}, $event.target.value)" class="rounded-xl border border-indigo-500/10 bg-white/60 px-3 py-2 text-xs txt-primary outline-none dark:bg-white/5 dark:border-white/10">
                                        <option value="Pending" @selected($comment->status === 'Pending')>Pending</option>
                                        <option value="Approved" @selected($comment->status === 'Approved')>Approved</option>
                                        <option value="Rejected" @selected($comment->status === 'Rejected')>Rejected</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="confirmDelete('comment', {{ $comment->id }})" class="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-16 text-center txt-muted text-sm">Belum ada komentar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-ui.pagination :links="$comments" />
        </x-ui.card>
    @endif

    <x-ui.modal name="tag-form" :show="$isTagModalOpen" maxWidth="md">
        <h2 class="text-xl font-bold txt-primary mb-1">{{ $tagEditId ? 'Edit Tag' : 'Tambah Tag' }}</h2>
        <form wire:submit.prevent="saveTag" class="mt-5 space-y-5">
            <div>
                <x-ui.label for="tagName" value="Nama Tag" class="mb-2 txt-secondary" />
                <x-ui.input wire:model="tagName" id="tagName" type="text" class="w-full" placeholder="Contoh: Prestasi" />
                @error('tagName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-indigo-500/10">
                <x-ui.button wire:click="closeModals" variant="secondary" type="button">Batal</x-ui.button>
                <x-ui.button variant="primary" type="submit">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal name="category-form" :show="$isCategoryModalOpen" maxWidth="md">
        <h2 class="text-xl font-bold txt-primary mb-1">{{ $categoryEditId ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>
        <form wire:submit.prevent="saveCategory" class="mt-5 space-y-5">
            <div>
                <x-ui.label for="categoryName" value="Nama Kategori" class="mb-2 txt-secondary" />
                <x-ui.input wire:model="categoryName" id="categoryName" type="text" class="w-full" placeholder="Contoh: Berita Sekolah" />
                @error('categoryName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t border-indigo-500/10">
                <x-ui.button wire:click="closeModals" variant="secondary" type="button">Batal</x-ui.button>
                <x-ui.button variant="primary" type="submit">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.confirm-modal
        name="confirm-delete-modal"
        title="Hapus Data Blog"
        message="Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan."
        onConfirm="delete"
    />
</div>
