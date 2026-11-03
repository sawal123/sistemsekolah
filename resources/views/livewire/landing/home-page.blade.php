<main>
    {{-- HeroSlider: struktur dan proporsi mengikuti school-frontend/index.html. --}}
    <section data-hero-slider class="relative flex min-h-[560px] items-center overflow-hidden bg-[#0F172A] text-white lg:min-h-[640px]">
        @forelse($heroSlides as $slide)
            <div class="hero-slide {{ $loop->first ? 'active' : '' }} absolute inset-0 flex items-center" aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                @if($slide['image'])
                    <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="absolute inset-0 h-full w-full object-cover object-center">
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-900 via-slate-950 to-blue-700"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-r from-[#0F172A]/95 via-[#0F172A]/80 to-[#0F172A]/35"></div>
                <div class="relative z-10 mx-auto w-full max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
                    <div class="max-w-2xl space-y-6">
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-1.5 text-xs font-semibold text-[#0EA5E9] backdrop-blur-xs">
                            <span class="h-2 w-2 rounded-full bg-[#F59E0B]"></span>{{ $slide['badge'] }}
                        </span>
                        @if($loop->first)
                            <h1 class="font-heading text-3xl font-extrabold leading-[1.15] tracking-tight text-white sm:text-5xl lg:text-[52px]">{{ $slide['title'] }}</h1>
                        @else
                            <h2 class="font-heading text-3xl font-extrabold leading-[1.15] tracking-tight text-white sm:text-5xl lg:text-[52px]">{{ $slide['title'] }}</h2>
                        @endif
                        <p class="text-base leading-relaxed text-slate-200 sm:text-lg">{{ $slide['description'] }}</p>
                        <div class="flex flex-wrap items-center gap-3.5 pt-2">
                            <a href="{{ $slide['primary_url'] }}" class="inline-flex items-center gap-2 rounded-xl bg-[#2563EB] px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-900/30 transition-all hover:bg-blue-500">
                                <span>{{ $slide['primary_label'] }}</span><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m17 8 4 4m0 0-4 4m4-4H3"/></svg>
                            </a>
                            @if($slide['secondary_label'])
                                <a href="{{ $slide['secondary_url'] }}" class="rounded-xl border border-white/20 bg-white/10 px-6 py-3.5 text-sm font-semibold text-white backdrop-blur-xs transition-all hover:bg-white/20">{{ $slide['secondary_label'] }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="relative z-10 mx-auto w-full max-w-7xl px-6 py-24"><h1 class="font-heading text-4xl font-extrabold">{{ $settings->get('school_name') }}</h1></div>
        @endforelse

        @if(count($heroSlides) > 1)
            <div class="absolute bottom-6 left-0 right-0 z-20 mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-2" role="tablist" aria-label="Indikator slide hero">
                    @foreach($heroSlides as $slide)
                        <button type="button" data-hero-dot aria-label="Slide {{ $loop->iteration }}" @if($loop->first) aria-current="true" @endif class="h-2.5 {{ $loop->first ? 'w-8 bg-[#F59E0B]' : 'w-2.5 bg-white/50 hover:bg-white' }} rounded-full transition-all duration-300"></button>
                    @endforeach
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" data-hero-prev aria-label="Slide sebelumnya" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white transition-colors hover:bg-[#2563EB]"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m15 19-7-7 7-7"/></svg></button>
                    <button type="button" data-hero-next aria-label="Slide selanjutnya" class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/20 bg-white/10 text-white transition-colors hover:bg-[#2563EB]"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 5 7 7-7 7"/></svg></button>
                </div>
            </div>
        @endif
    </section>

    <section class="relative z-20 -mt-10 pb-12" aria-label="Statistik utama sekolah">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"><div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                [$stats['students'], 'Total Siswa Aktif', 'bg-blue-50 text-[#2563EB]', 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                [$stats['teachers'], 'Pendidik & Tenaga Kependidikan', 'bg-sky-50 text-[#0EA5E9]', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                [$stats['programs'], 'Kelas & Program Pendidikan', 'bg-amber-50 text-[#F59E0B]', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                [$stats['alumni'], 'Jejaring Alumni Terdaftar', 'bg-indigo-50 text-[#2563EB]', 'M12 14l9-5-9-5-9 5 9 5zm0 0 6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
            ] as [$value, $label, $color, $path])
                <div class="card-elevated flex items-center gap-4 rounded-2xl border border-slate-200/90 bg-white p-6 shadow-md"><div class="flex h-13 w-13 shrink-0 items-center justify-center rounded-2xl {{ $color }}"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="{{ $path }}"/></svg></div><div><span class="block font-heading text-2xl font-extrabold text-[#0F172A] sm:text-3xl">{{ $value }}</span><span class="block text-xs font-medium text-[#64748B]">{{ $label }}</span></div></div>
            @endforeach
        </div></div>
    </section>

    <section class="py-20"><div class="mx-auto max-w-7xl px-6 lg:px-8"><div class="mb-10 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="text-sm font-bold uppercase tracking-[.16em] text-blue-600">Sarana & Prasarana</p><h2 class="mt-2 font-heading text-3xl font-bold text-slate-950">Fasilitas yang mendukung pembelajaran</h2></div><a href="{{ route('landing.facilities') }}" class="text-sm font-bold text-blue-600 hover:underline">Lihat semua fasilitas →</a></div><div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($facilities as $facility)
            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">@if($facility->foto)<img src="{{ \App\Support\PublicImage::url($facility->foto) }}" alt="{{ $facility->nama_fasilitas }}" class="aspect-[16/10] w-full object-cover">@endif<div class="p-6"><p class="text-xs font-bold uppercase tracking-wide text-blue-600">{{ $facility->kategori }}</p><h3 class="mt-2 font-heading text-xl font-bold">{{ $facility->nama_fasilitas }}</h3><p class="mt-3 text-sm leading-6 text-slate-600">{{ $facility->deskripsi }}</p><div class="mt-5 flex justify-between border-t border-slate-100 pt-4 text-xs font-semibold"><span>{{ $facility->lokasi }}</span><span class="text-blue-700">{{ $facility->jumlah }} unit · {{ $facility->kondisi }}</span></div></div></article>
        @empty <p class="col-span-full rounded-2xl bg-slate-50 p-8 text-center text-slate-500">Data fasilitas akan muncul setelah ditambahkan oleh admin.</p>
        @endforelse
    </div></div></section>

    <section class="bg-slate-50 py-20"><div class="mx-auto max-w-7xl px-6 lg:px-8"><div class="mb-10 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="text-sm font-bold uppercase tracking-[.16em] text-blue-600">Informasi Terkini</p><h2 class="mt-2 font-heading text-3xl font-bold text-slate-950">Berita & pengumuman sekolah</h2></div><a href="{{ route('landing.blog') }}" class="text-sm font-bold text-blue-600 hover:underline">Semua berita →</a></div><div class="grid gap-6 md:grid-cols-3">
        @forelse($posts as $post)
            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">@if($post->thumbnail)<img src="{{ \App\Support\PublicImage::url($post->thumbnail) }}" alt="{{ $post->judul }}" class="aspect-[16/10] w-full object-cover">@endif<div class="p-6"><p class="text-xs font-bold uppercase tracking-wide text-blue-600">{{ $post->kategori?->nama_kategori }} · {{ $post->created_at->translatedFormat('d M Y') }}</p><h3 class="mt-3 font-heading text-xl font-bold leading-snug"><a href="{{ route('landing.blog.show', $post->slug) }}" class="hover:text-blue-600">{{ $post->judul }}</a></h3><p class="mt-3 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags($post->konten), 135) }}</p><a href="{{ route('landing.blog.show', $post->slug) }}" class="mt-5 inline-flex text-sm font-bold text-blue-600">Baca selengkapnya →</a></div></article>
        @empty <p class="col-span-full rounded-2xl bg-white p-8 text-center text-slate-500">Belum ada berita yang dipublikasikan.</p>
        @endforelse
    </div></div></section>

    <section class="py-20"><div class="mx-auto max-w-7xl px-6 lg:px-8"><div class="mb-10 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="text-sm font-bold uppercase tracking-[.16em] text-blue-600">Dokumentasi</p><h2 class="mt-2 font-heading text-3xl font-bold text-slate-950">Kehidupan di sekolah</h2></div><a href="{{ route('landing.galeri') }}" class="text-sm font-bold text-blue-600 hover:underline">Buka galeri →</a></div><div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($galleries as $gallery)
            <a href="{{ route('landing.galeri') }}" class="group overflow-hidden rounded-3xl bg-slate-100">@if($gallery->foto)<img src="{{ \App\Support\PublicImage::url($gallery->foto) }}" alt="{{ $gallery->judul }}" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105">@endif<div class="p-5"><h3 class="font-heading font-bold">{{ $gallery->judul }}</h3><p class="mt-1 text-sm text-slate-500">{{ $gallery->deskripsi }}</p></div></a>
        @empty <p class="col-span-full rounded-2xl bg-slate-50 p-8 text-center text-slate-500">Galeri kegiatan belum tersedia.</p>
        @endforelse
    </div></div></section>

    <section class="bg-blue-950 py-20 text-white"><div class="mx-auto grid max-w-7xl gap-8 px-6 lg:grid-cols-[1fr_auto] lg:items-center lg:px-8"><div><p class="text-sm font-bold uppercase tracking-[.16em] text-blue-200">Penerimaan Peserta Didik Baru</p><h2 class="mt-3 font-heading text-3xl font-bold">Siap menjadi bagian dari {{ $settings->get('school_name') }}?</h2><p class="mt-4 max-w-2xl leading-7 text-blue-100">Lihat jadwal gelombang, persyaratan, biaya aktif, dan isi formulir pendaftaran awal secara online.</p></div><a href="{{ route('landing.ppdb.section', 'daftar') }}#daftar" class="w-fit rounded-xl bg-white px-6 py-3.5 text-sm font-bold text-blue-800 hover:bg-blue-50">Informasi & Daftar PPDB</a></div></section>
</main>
