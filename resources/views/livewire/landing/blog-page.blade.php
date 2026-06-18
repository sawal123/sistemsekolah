<main class="bg-white">
    <x-landing.page-hero
        eyebrow="Informasi Sekolah"
        title="Blog & Berita"
        description="Artikel, pengumuman, prestasi, dan kegiatan terbaru yang diterbitkan melalui pengelolaan blog sekolah."
    />

    <section class="py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mb-10 grid gap-4 md:grid-cols-[1fr_260px]">
                <input wire:model.live.debounce.400ms="search" type="search" placeholder="Cari judul berita..." class="w-full rounded-2xl border border-blue-100 bg-blue-50 px-5 py-3 text-sm outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100">
                <select wire:model.live="category" class="w-full rounded-2xl border border-blue-100 bg-white px-5 py-3 text-sm outline-none focus:border-blue-400">
                    <option value="">Semua kategori</option>
                    @foreach($categories as $item)
                        <option value="{{ $item->slug }}">{{ $item->nama_kategori }} ({{ $item->posts_count }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($posts as $post)
                    <article class="group overflow-hidden rounded-3xl border border-blue-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                        <a href="{{ route('landing.blog.show', $post->slug) }}">
                            @if($post->thumbnail)
                                <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->judul }}" class="aspect-[16/10] w-full object-cover transition duration-500 group-hover:scale-105">
                            @else
                                <div class="aspect-[16/10] bg-gradient-to-br from-blue-700 to-blue-300"></div>
                            @endif
                        </a>
                        <div class="p-6">
                            <div class="flex items-center justify-between gap-3 text-xs">
                                <span class="font-bold uppercase tracking-wide text-blue-600">{{ $post->kategori?->nama_kategori ?: 'Berita' }}</span>
                                <span class="text-slate-400">{{ $post->created_at->translatedFormat('d M Y') }}</span>
                            </div>
                            <h2 class="mt-3 font-display text-xl font-bold leading-snug text-blue-950">
                                <a href="{{ route('landing.blog.show', $post->slug) }}" class="transition hover:text-blue-600">{{ $post->judul }}</a>
                            </h2>
                            <p class="mt-3 text-sm leading-6 text-slate-500">{{ \Illuminate\Support\Str::limit(strip_tags($post->konten), 135) }}</p>
                            <a href="{{ route('landing.blog.show', $post->slug) }}" class="mt-5 inline-flex text-sm font-bold text-blue-600">Baca selengkapnya →</a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-3xl border border-dashed border-blue-200 bg-blue-50 p-12 text-center text-slate-500">Artikel yang dicari belum tersedia.</div>
                @endforelse
            </div>

            <div class="mt-10">{{ $posts->links() }}</div>
        </div>
    </section>
</main>
