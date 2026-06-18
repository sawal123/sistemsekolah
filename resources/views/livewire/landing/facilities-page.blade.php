<main class="bg-white">
    <x-landing.page-hero
        eyebrow="Sarana & Prasarana"
        title="Fasilitas Sekolah"
        description="Data fasilitas di halaman ini tersinkron dengan pengelolaan sarana dan prasarana pada halaman admin."
    />

    <section class="py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mb-10 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-blue-50 p-6">
                    <p class="font-display text-3xl font-extrabold text-blue-700">{{ number_format($totalUnits, 0, ',', '.') }}</p>
                    <p class="mt-2 text-sm text-slate-500">Total unit fasilitas</p>
                </div>
                <div class="rounded-2xl bg-emerald-50 p-6">
                    <p class="font-display text-3xl font-extrabold text-emerald-600">{{ number_format($goodUnits, 0, ',', '.') }}</p>
                    <p class="mt-2 text-sm text-slate-500">Unit berkondisi baik</p>
                </div>
            </div>

            <div class="mb-10 flex flex-wrap gap-2">
                <button wire:click="$set('category', '')" class="rounded-full px-5 py-2.5 text-sm font-semibold {{ $category === '' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700' }}">Semua</button>
                @foreach($categories as $item)
                    <button wire:click="$set('category', '{{ $item }}')" class="rounded-full px-5 py-2.5 text-sm font-semibold {{ $category === $item ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700' }}">{{ $item }}</button>
                @endforeach
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($facilities as $facility)
                    <article class="overflow-hidden rounded-3xl border border-blue-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                        @if($facility->foto)
                            <img src="{{ asset('storage/' . $facility->foto) }}" alt="{{ $facility->nama_fasilitas }}" class="aspect-[16/10] w-full object-cover">
                        @else
                            <div class="flex aspect-[16/10] items-center justify-center bg-gradient-to-br from-blue-700 to-blue-300 text-white">
                                <svg class="h-14 w-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M3 21h18M9 7h1m-1 4h1m4-4h1m-1 4h1m-6 4h6"/></svg>
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-wide text-blue-500">{{ $facility->kategori }}</p>
                                    <h2 class="mt-2 font-display text-xl font-bold text-blue-950">{{ $facility->nama_fasilitas }}</h2>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $facility->kondisi === 'Baik' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">{{ $facility->kondisi }}</span>
                            </div>
                            <p class="mt-4 text-sm leading-6 text-slate-500">{{ $facility->deskripsi ?: 'Fasilitas pendukung kegiatan belajar dan aktivitas sekolah.' }}</p>
                            <div class="mt-5 flex justify-between border-t border-slate-100 pt-4 text-sm">
                                <span class="text-slate-500">{{ $facility->lokasi ?: 'Area sekolah' }}</span>
                                <span class="font-bold text-blue-700">{{ $facility->jumlah }} unit</span>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-3xl border border-dashed border-blue-200 bg-blue-50 p-12 text-center text-slate-500">Belum ada fasilitas pada kategori ini.</div>
                @endforelse
            </div>
        </div>
    </section>
</main>
