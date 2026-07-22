<main class="bg-white">
    <section class="bg-gradient-to-br from-blue-950 via-blue-800 to-blue-500 text-white">
        <div class="mx-auto max-w-5xl px-6 py-20 lg:px-8">
            <a href="{{ route('landing.blog') }}" class="text-sm font-semibold text-blue-200 hover:text-white">← Kembali ke blog</a>
            <p class="mt-8 text-sm font-bold uppercase tracking-[0.18em] text-blue-200">{{ $post->kategori?->nama_kategori ?: 'Berita' }}</p>
            <h1 class="mt-3 font-display text-4xl font-bold leading-tight md:text-5xl">{{ $post->judul }}</h1>
            <div class="mt-6 flex flex-wrap gap-4 text-sm text-blue-100">
                <span>{{ $post->created_at->translatedFormat('d F Y') }}</span>
                <span>•</span>
                <span>{{ $post->user?->name ?: 'Admin Sekolah' }}</span>
                <span>•</span>
                <span>{{ number_format($post->views) }} dilihat</span>
            </div>
        </div>
    </section>

    <article class="mx-auto max-w-4xl px-6 py-16 lg:px-8">
        @if($post->thumbnail)
            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->judul }}" class="mb-10 aspect-[16/9] w-full rounded-3xl object-cover shadow-lg">
        @endif
        <div class="prose prose-lg max-w-none prose-headings:font-display prose-headings:text-blue-950 prose-a:text-blue-600">
            {!! $post->konten !!}
        </div>
        @if($post->tags->isNotEmpty())
            <div class="mt-10 flex flex-wrap gap-2 border-t border-slate-100 pt-6">
                @foreach($post->tags as $tag)
                    <span class="rounded-full bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-700">#{{ $tag->nama_tag }}</span>
                @endforeach
            </div>
        @endif
    </article>

    <section class="bg-blue-50 py-16">
        <div class="mx-auto grid max-w-5xl gap-10 px-6 lg:grid-cols-2 lg:px-8">
            <div>
                <h2 class="font-display text-2xl font-bold text-blue-950">Komentar pembaca</h2>
                <div class="mt-6 space-y-4">
                    @forelse($comments as $item)
                        <div class="rounded-2xl bg-white p-5">
                            <div class="flex justify-between gap-4">
                                <p class="font-bold text-slate-800">{{ $item->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $item->created_at->diffForHumans() }}</p>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-slate-600">{{ $item->komentar }}</p>
                        </div>
                    @empty
                        <p class="rounded-2xl bg-white p-5 text-sm text-slate-500">Belum ada komentar yang ditampilkan.</p>
                    @endforelse
                </div>
            </div>

            <form wire:submit="submitComment" class="rounded-3xl bg-white p-7 shadow-sm">
                <h2 class="font-display text-2xl font-bold text-blue-950">Kirim komentar</h2>
                <p class="mt-2 text-sm text-slate-500">Komentar akan tampil setelah disetujui admin.</p>
                @if(session('comment_success'))
                    <div class="mt-5 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-700">{{ session('comment_success') }}</div>
                @endif
                <div class="mt-6 space-y-4">
                    <div><input wire:model="name" type="text" placeholder="Nama *" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-blue-400">@error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                    <div><input wire:model="email" type="email" placeholder="Email (opsional)" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-blue-400">@error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                    <div><textarea wire:model="comment" rows="5" placeholder="Komentar *" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-blue-400"></textarea>@error('comment')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                    <button type="submit" class="w-full rounded-xl bg-blue-600 px-5 py-3 font-bold text-white transition hover:bg-blue-700">Kirim Komentar</button>
                </div>
            </form>
        </div>
    </section>
</main>
