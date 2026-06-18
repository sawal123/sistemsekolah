<main class="bg-white">
    @php
        $pageMeta = match($section) {
            'guru' => [
                'eyebrow' => 'Tenaga Pendidik',
                'title' => 'Guru',
                'description' => 'Daftar tenaga pendidik aktif yang dikelola melalui menu Data Guru pada halaman admin.',
            ],
            'tata-usaha' => [
                'eyebrow' => 'Tenaga Kependidikan',
                'title' => 'Tata Usaha',
                'description' => 'Staf administrasi dan operasional yang mendukung layanan sekolah.',
            ],
            default => [
                'eyebrow' => 'Kepegawaian',
                'title' => 'Struktur Organisasi',
                'description' => 'Bagan struktur organisasi sekolah yang dikelola oleh admin.',
            ],
        };
    @endphp

    <x-landing.page-hero
        :eyebrow="$pageMeta['eyebrow']"
        :title="$pageMeta['title']"
        :description="$pageMeta['description']"
    />

    <nav class="border-b border-blue-100 bg-white">
        <div class="mx-auto flex max-w-7xl gap-2 overflow-x-auto px-6 py-4 lg:px-8">
            @foreach([
                'guru' => 'Guru',
                'tata-usaha' => 'Tata Usaha',
                'struktur-organisasi' => 'Struktur Organisasi',
            ] as $slug => $label)
                <a href="{{ route('landing.staffing', $slug) }}" class="whitespace-nowrap rounded-full px-5 py-2.5 text-sm font-semibold transition {{ $section === $slug ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>

    @if($section === 'guru')
        <section class="py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mb-10">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Civitas Akademik</p>
                    <h2 class="mt-2 font-display text-3xl font-bold text-blue-950">{{ $teachers->count() }} tenaga pendidik aktif</h2>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse($teachers as $teacher)
                        <article class="overflow-hidden rounded-3xl border border-blue-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                            <div class="aspect-square overflow-hidden bg-gradient-to-br from-blue-100 to-blue-50">
                                @if($teacher->foto)
                                    <img src="{{ asset('storage/' . $teacher->foto) }}" alt="{{ $teacher->user?->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center">
                                        <span class="flex h-24 w-24 items-center justify-center rounded-full bg-blue-600 font-display text-3xl font-bold text-white">
                                            {{ collect(explode(' ', $teacher->user?->name ?: 'G'))->take(2)->map(fn ($word) => mb_substr($word, 0, 1))->implode('') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <h3 class="font-display text-lg font-bold leading-snug text-blue-950">{{ $teacher->user?->name ?: 'Nama belum tersedia' }}</h3>
                                @if($teacher->kelas)
                                    <p class="mt-3 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Wali Kelas {{ $teacher->kelas->nama_kelas }}</p>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-3xl border border-dashed border-blue-200 bg-blue-50 p-12 text-center text-slate-500">Data guru aktif belum tersedia.</div>
                    @endforelse
                </div>
            </div>
        </section>
    @elseif($section === 'tata-usaha')
        <section class="py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[0.75fr_1.25fr]">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Layanan Administrasi</p>
                        <h2 class="mt-2 font-display text-3xl font-bold text-blue-950">Tata usaha dan staf operasional</h2>
                        <p class="mt-5 leading-7 text-slate-600">Tenaga kependidikan mendukung layanan administrasi siswa, surat-menyurat, keuangan, pendataan, dan operasional sekolah.</p>
                        <div class="mt-7 rounded-2xl bg-blue-50 p-5 text-sm leading-6 text-slate-600">Jabatan pegawai dikelola melalui kolom Jabatan Pegawai pada menu Data Pengguna.</div>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        @forelse($staff as $employee)
                            <article class="rounded-3xl border border-blue-100 p-6">
                                <div class="flex items-center gap-4">
                                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-600 font-display text-lg font-bold text-white">
                                        {{ collect(explode(' ', $employee->name))->take(2)->map(fn ($word) => mb_substr($word, 0, 1))->implode('') }}
                                    </span>
                                    <div class="min-w-0">
                                        <h3 class="font-display font-bold text-blue-950">{{ $employee->name }}</h3>
                                        <p class="mt-1 text-sm font-semibold text-blue-600">{{ $employee->staff_position }}</p>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full rounded-3xl border border-dashed border-blue-200 bg-blue-50 p-12 text-center text-slate-500">Belum ada pegawai dengan jabatan tata usaha.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class="bg-slate-50 py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="text-center">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Bagan Organisasi</p>
                    <h2 class="mt-2 font-display text-3xl font-bold text-blue-950">{{ $settings->get('school_name') ?: 'Sekolah' }}</h2>
                </div>

                @if($settings->get('organization_structure_image'))
                    <div class="mt-10 overflow-auto rounded-3xl border border-blue-100 bg-white p-3 shadow-lg sm:p-6">
                        <img
                            src="{{ asset('storage/' . $settings->get('organization_structure_image')) }}"
                            alt="Struktur organisasi {{ $settings->get('school_name') ?: 'sekolah' }}"
                            class="mx-auto h-auto max-h-[1000px] max-w-full rounded-2xl object-contain"
                        >
                    </div>
                @else
                    <div class="mt-10 rounded-3xl border border-dashed border-blue-200 bg-blue-50 p-12 text-center text-slate-500">Gambar struktur organisasi belum diunggah oleh admin.</div>
                @endif
            </div>
        </section>
    @endif
</main>
