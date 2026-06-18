<main class="bg-white">
    <x-landing.page-hero eyebrow="Akademik" :title="$meta['label']" :description="$meta['subtitle']" />

    <section class="border-b border-blue-100 bg-white">
        <div class="mx-auto flex max-w-7xl gap-2 overflow-x-auto px-6 py-4 lg:px-8">
            @foreach($programs as $slug => $item)
                <a href="{{ route('landing.academic', $slug) }}" class="whitespace-nowrap rounded-full px-5 py-2.5 text-sm font-semibold transition {{ $program === $slug ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </section>

    <section class="py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    [$activeYear?->tahun ?: 'Belum diatur', 'Tahun Ajaran'],
                    [$classes->count(), 'Kelas SMA'],
                    [$allMapels->count(), 'Mata Pelajaran'],
                    [$scheduleCount, 'Jadwal Aktif'],
                ] as [$value, $label])
                    <div class="rounded-2xl border border-blue-100 p-6">
                        <p class="font-display text-2xl font-extrabold text-blue-700">{{ $value }}</p>
                        <p class="mt-2 text-sm text-slate-500">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-14 grid gap-10 lg:grid-cols-[1.3fr_0.7fr]">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Struktur Pembelajaran</p>
                    <h2 class="mt-2 font-display text-3xl font-bold text-blue-950">Mata pelajaran {{ strtolower($meta['label']) }}</h2>

                    @if($mapels->isNotEmpty())
                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            @foreach($mapels as $mapel)
                                <article class="rounded-2xl border border-blue-100 p-5 transition hover:-translate-y-1 hover:shadow-lg">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="font-display font-bold text-blue-950">{{ $mapel->nama_mapel }}</p>
                                            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-blue-500">{{ $mapel->kode_mapel }}</p>
                                        </div>
                                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">{{ $mapel->kelompok }}</span>
                                    </div>
                                    <p class="mt-4 text-sm text-slate-500">KKM {{ $mapel->kkm }} · Jenjang {{ $mapel->jenjang }}</p>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-8 rounded-2xl border border-dashed border-blue-200 bg-blue-50 p-8 text-slate-600">
                            Mata pelajaran khusus program ini belum ditambahkan pada menu admin Mata Pelajaran.
                        </div>
                    @endif
                </div>

                <aside>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Kelas SMA</p>
                    <div class="mt-5 space-y-3">
                        @forelse($classes as $class)
                            <div class="rounded-2xl bg-slate-50 p-5">
                                <div class="flex items-center justify-between gap-4">
                                    <p class="font-display font-bold text-blue-950">{{ $class->nama_kelas }}</p>
                                    <span class="text-sm font-semibold text-blue-600">{{ $class->siswas_count }} siswa</span>
                                </div>
                                <p class="mt-2 text-sm text-slate-500">Wali kelas: {{ $class->wali_kelas?->user?->name ?: 'Belum ditentukan' }}</p>
                            </div>
                        @empty
                            <p class="rounded-2xl bg-slate-50 p-5 text-sm text-slate-500">Belum ada kelas SMA.</p>
                        @endforelse
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @if($events->isNotEmpty())
        <section class="bg-blue-50 py-16">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <h2 class="font-display text-2xl font-bold text-blue-950">Kalender akademik terdekat</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($events as $event)
                        <article class="rounded-2xl bg-white p-5">
                            <p class="text-sm font-bold text-blue-600">{{ $event->tanggal->translatedFormat('d F Y') }}</p>
                            <p class="mt-2 font-semibold text-slate-800">{{ $event->keterangan }}</p>
                            <p class="mt-1 text-xs uppercase tracking-wide text-slate-400">{{ $event->jenis_libur }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</main>
