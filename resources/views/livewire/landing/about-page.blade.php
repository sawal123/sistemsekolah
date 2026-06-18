<main class="bg-white">
    <x-landing.page-hero
        eyebrow="Profil Sekolah"
        title="Tentang {{ $settings->get('school_name') ?: 'Sekolah Kami' }}"
        description="{{ $settings->get('site_tagline') ?: 'Mengenal identitas, arah pendidikan, dan komitmen sekolah dalam mendampingi setiap peserta didik.' }}"
    />

    <section class="py-20">
        <div class="mx-auto grid max-w-7xl gap-12 px-6 lg:grid-cols-[0.85fr_1.15fr] lg:px-8">
            <div class="relative">
                <div class="aspect-[4/5] overflow-hidden rounded-3xl bg-gradient-to-br from-blue-700 to-blue-300 shadow-xl">
                    @if($settings->get('principal_image'))
                        <img src="{{ asset('storage/' . $settings->get('principal_image')) }}" alt="{{ $settings->get('principal_name') }}" class="h-full w-full object-cover">
                    @endif
                </div>
                <div class="absolute -bottom-5 left-5 right-5 rounded-2xl bg-white p-5 shadow-xl">
                    <p class="font-display font-bold text-blue-950">{{ $settings->get('principal_name') ?: 'Kepala Sekolah' }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $settings->get('principal_title') ?: 'Kepala Sekolah' }}</p>
                    @if($settings->get('principal_nip'))
                        <p class="text-xs text-slate-400">NIP {{ $settings->get('principal_nip') }}</p>
                    @endif
                </div>
            </div>

            <div class="lg:py-8">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Sambutan Kepala Sekolah</p>
                <h2 class="mt-3 font-display text-3xl font-bold text-blue-950">Pendidikan yang tumbuh bersama karakter</h2>
                <div class="mt-6 space-y-4 text-slate-600 leading-8">
                    @foreach(preg_split('/\r\n|\r|\n/', $settings->get('principal_greeting') ?: 'Selamat datang di sekolah kami. Kami berkomitmen menghadirkan pembelajaran yang aman, bermakna, dan relevan.') as $paragraph)
                        @if(trim($paragraph))
                            <p>{{ $paragraph }}</p>
                        @endif
                    @endforeach
                </div>

                <div class="mt-10 grid grid-cols-3 gap-3">
                    @foreach([[$stats['students'], 'Siswa Aktif'], [$stats['teachers'], 'Pendidik'], [$stats['alumni'], 'Jejak Alumni']] as [$value, $label])
                        <div class="rounded-2xl bg-blue-50 p-4 text-center">
                            <p class="font-display text-2xl font-extrabold text-blue-700">{{ number_format($value, 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-blue-50 py-20">
        <div class="mx-auto grid max-w-7xl gap-8 px-6 lg:grid-cols-2 lg:px-8">
            <article class="rounded-3xl bg-white p-8 shadow-sm">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Visi</p>
                <blockquote class="mt-5 font-display text-2xl font-semibold leading-relaxed text-blue-950">
                    “{{ $settings->get('school_vision') ?: 'Menjadi sekolah unggul yang membentuk generasi cerdas, berkarakter, dan berdaya saing.' }}”
                </blockquote>
            </article>

            <article class="rounded-3xl bg-blue-900 p-8 text-white shadow-sm">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-200">Misi</p>
                <ol class="mt-5 space-y-4">
                    @foreach(array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $settings->get('school_mission') ?: 'Menyelenggarakan pembelajaran berkualitas.')))) as $mission)
                        <li class="flex gap-4 text-blue-50">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/15 text-xs font-bold">{{ $loop->iteration }}</span>
                            <span class="leading-7">{{ preg_replace('/^\d+[\.\)]\s*/', '', $mission) }}</span>
                        </li>
                    @endforeach
                </ol>
            </article>
        </div>
    </section>

    @if($galleries->isNotEmpty())
        <section class="py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mb-10">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Galeri Sekolah</p>
                    <h2 class="mt-2 font-display text-3xl font-bold text-blue-950">Aktivitas dan lingkungan belajar</h2>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($galleries as $gallery)
                        <figure class="group overflow-hidden rounded-3xl bg-slate-100">
                            @if($gallery->foto)
                                <img src="{{ asset('storage/' . $gallery->foto) }}" alt="{{ $gallery->judul }}" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105">
                            @endif
                            <figcaption class="p-5">
                                <p class="font-display font-bold text-blue-950">{{ $gallery->judul }}</p>
                                @if($gallery->deskripsi)<p class="mt-2 text-sm text-slate-500">{{ $gallery->deskripsi }}</p>@endif
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</main>
