<main>
    <style>
        #hero {
            height: clamp(560px, calc(100svh - 80px), 620px);
            min-height: 560px;
        }

        #hero .hero-slide {
            overflow: hidden;
        }

        #hero .hero-slide img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            max-width: none;
            object-fit: cover;
            object-position: center;
        }

        #hero .hero-content {
            height: 100%;
            padding-top: 48px;
            padding-bottom: 96px;
        }

        #hero .hero-copy {
            text-shadow: 0 2px 20px rgba(15, 23, 42, .24);
        }

        #hero .hero-dot {
            border: 0;
            cursor: pointer;
        }

        @media (max-width: 767px) {
            #hero {
                height: calc(100svh - 60px);
                min-height: 560px;
                max-height: 680px;
            }

            #hero .hero-slide img {
                object-position: 55% center;
            }

            #hero .hero-content {
                padding: 48px 18px 82px;
                align-items: center;
            }

            #hero .hero-copy {
                width: 100%;
                max-width: 34rem;
            }

            #hero .hero-badge {
                margin-bottom: 14px;
                font-size: 12px;
            }

            #hero .hero-title {
                max-width: 19rem;
                margin-bottom: 14px;
                font-size: clamp(2rem, 10vw, 2.75rem);
                line-height: 1.12;
            }

            #hero .hero-description {
                max-width: 28rem;
                margin-bottom: 0;
                font-size: 15px;
                line-height: 1.65;
            }

            #hero .hero-dots {
                left: 18px;
                bottom: 46px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            #hero .slide.active {
                animation: none;
            }
        }
    </style>

    <section id="hero" class="relative gradient-hero overflow-hidden">
        <div class="absolute inset-0" aria-hidden="true">
            @foreach($heroSlides as $slide)
                <div class="slide hero-slide {{ $loop->first ? 'active' : '' }} absolute inset-0 w-full h-full">
                    @if($slide['image'])
                        <img src="{{ $slide['image'] }}" alt="">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-blue-900 via-blue-700 to-blue-400"></div>
                    @endif
                    <div class="absolute inset-0" style="background: linear-gradient(90deg, rgba(8, 30, 78, .72) 0%, rgba(19, 68, 145, .38) 48%, rgba(37, 99, 235, .10) 100%);"></div>
                    <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(15, 23, 42, .04) 0%, rgba(30, 64, 175, .08) 45%, rgba(29, 78, 216, .34) 100%);"></div>
                </div>
            @endforeach
        </div>

        <div class="hero-content relative z-10 max-w-7xl mx-auto px-6 lg:px-8 flex items-center">
            <div class="hero-copy max-w-2xl">
                @foreach($heroSlides as $slide)
                    <div data-slide-content class="{{ $loop->first ? '' : 'hidden' }}">
                        <span class="hero-badge inline-block bg-white/20 text-white text-sm font-semibold px-4 py-1.5 rounded-full mb-5 backdrop-blur-sm">{{ $slide['badge'] }}</span>
                        <h1 class="hero-title font-display text-4xl md:text-6xl font-bold text-white leading-tight mb-5">{{ $slide['title'] }}</h1>
                        <p class="hero-description text-blue-50 text-lg mb-8 max-w-lg">{{ $slide['description'] }}</p>
                        {{-- <div class="flex flex-wrap gap-4">
                            <a href="{{ $slide['primary_url'] }}" class="bg-white text-blue-700 px-7 py-3.5 rounded-full font-semibold shadow-lg hover:bg-blue-50 transition">{{ $slide['primary_label'] }}</a>
                            @if($slide['secondary_label'])
                                <a href="{{ $slide['secondary_url'] }}" class="border-2 border-white/70 text-white px-7 py-3.5 rounded-full font-semibold hover:bg-white/10 transition">{{ $slide['secondary_label'] }}</a>
                            @endif
                        </div> --}}
                    </div>
                @endforeach
            </div>

            <div class="hero-dots absolute bottom-8 left-6 lg:left-8 flex gap-2">
                @foreach($heroSlides as $slide)
                    <button
                        type="button"
                        onclick="changeSlide({{ $loop->index }})"
                        data-dot
                        class="hero-dot dot w-8 h-1.5 rounded-full {{ $loop->first ? 'bg-white' : 'bg-white/40' }} transition"
                        aria-label="Tampilkan slide {{ $loop->iteration }}"
                        aria-current="{{ $loop->first ? 'true' : 'false' }}"
                    ></button>
                @endforeach
            </div>
        </div>

        @if(count($heroSlides) > 1)
            <button type="button" onclick="prevSlide()" class="hidden lg:flex absolute z-20 left-8 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full border border-white/30 bg-white/10 text-white items-center justify-center backdrop-blur-sm hover:bg-white/20 transition" aria-label="Slide sebelumnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button type="button" onclick="nextSlide()" class="hidden lg:flex absolute z-20 right-8 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full border border-white/30 bg-white/10 text-white items-center justify-center backdrop-blur-sm hover:bg-white/20 transition" aria-label="Slide berikutnya">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
            </button>
        @endif

        <svg class="absolute -bottom-1 left-0 w-full text-white" viewBox="0 0 1440 80" fill="currentColor" preserveAspectRatio="none"><path d="M0,40 C360,90 1080,0 1440,40 L1440,80 L0,80 Z"/></svg>
    </section>

    <section class="bg-white py-14">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <p class="font-display text-4xl md:text-5xl font-extrabold text-blue-700">{{ $stats['students'] }}</p>
                <p class="text-slate-500 mt-2 font-medium">Siswa Aktif</p>
            </div>
            <div>
                <p class="font-display text-4xl md:text-5xl font-extrabold text-blue-700">{{ $stats['teachers'] }}</p>
                <p class="text-slate-500 mt-2 font-medium">Tenaga Pendidik</p>
            </div>
            <div>
                <p class="font-display text-4xl md:text-5xl font-extrabold text-blue-700">{{ $stats['programs'] }}</p>
                <p class="text-slate-500 mt-2 font-medium">Kelas / Program</p>
            </div>
            <div>
                <p class="font-display text-4xl md:text-5xl font-extrabold text-blue-700">{{ $stats['alumni'] }}</p>
                <p class="text-slate-500 mt-2 font-medium">Alumni Tersebar</p>
            </div>
        </div>
    </section>

    <section id="tentang" class="gradient-section py-20">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 grid md:grid-cols-2 gap-12 items-center">
            <div class="relative">
                <div class="aspect-[4/5] rounded-3xl bg-gradient-to-br from-blue-600 to-blue-300 shadow-xl flex items-center justify-center overflow-hidden">
                    @if($settings->get('principal_image'))
                        <img src="{{ asset('storage/' . $settings->get('principal_image')) }}" alt="Kepala Sekolah" class="w-full h-full" style="object-fit: cover;">
                    @else
                        <svg class="w-2/3 h-2/3 text-white/80" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a5 5 0 0 1 5 5v1a5 5 0 0 1-10 0V7a5 5 0 0 1 5-5zm0 12c4.42 0 8 2.24 8 5v3H4v-3c0-2.76 3.58-5 8-5z"/></svg>
                    @endif
                </div>
                <div class="absolute -bottom-6 -right-6 bg-white rounded-2xl shadow-lg px-6 py-4 hidden sm:block">
                    <p class="font-display font-bold text-blue-900">{{ $settings->get('principal_name') ?: 'Drs. Bambang Hartono, M.Pd.' }}</p>
                    <p class="text-sm text-slate-500">{{ $settings->get('principal_title') ?: 'Kepala Sekolah' }}</p>
                </div>
            </div>
            <div>
                <span class="text-blue-600 font-semibold tracking-wide uppercase text-sm">Sambutan</span>
                <h2 class="font-display text-3xl md:text-4xl font-bold text-blue-900 mt-2 mb-6">Pesan dari Kepala Sekolah</h2>
                @if($settings->get('principal_greeting'))
                    <div class="text-slate-600 leading-relaxed mb-6">{!! nl2br(e($settings->get('principal_greeting'))) !!}</div>
                @else
                    <p class="text-slate-600 leading-relaxed mb-4">Selamat datang di {{ $settings->get('school_name') ?: 'SMA Negeri 1 Cendekia' }}. Kami percaya bahwa setiap siswa memiliki potensi unik yang perlu dikembangkan melalui proses pendidikan yang holistik, tidak hanya unggul secara akademik tetapi juga berkarakter kuat.</p>
                    <p class="text-slate-600 leading-relaxed mb-6">Bersama guru dan tenaga kependidikan yang berdedikasi, kami berkomitmen menciptakan lingkungan belajar yang aman, inspiratif, dan relevan dengan tantangan zaman.</p>
                @endif
                <p class="font-display font-bold text-blue-900">{{ $settings->get('principal_name') ?: 'Drs. Bambang Hartono, M.Pd.' }}</p>
                <p class="text-sm text-slate-500">{{ $settings->get('principal_title') ?: 'Kepala Sekolah' }}</p>
            </div>
        </div>
    </section>

    <section id="fasilitas" class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-blue-600 font-semibold tracking-wide uppercase text-sm">Fasilitas Sekolah</span>
                <h2 class="font-display text-3xl md:text-4xl font-bold text-blue-900 mt-2">Sarana Penunjang Belajar yang Lengkap</h2>
                <p class="text-slate-500 mt-4">Kami menyediakan fasilitas modern untuk mendukung kegiatan akademik maupun non-akademik siswa.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($facilities as $item)
                    <div class="group p-7 rounded-2xl border border-blue-100 hover:shadow-xl hover:border-blue-200 transition">
                        <div class="w-14 h-14 rounded-xl bg-blue-50 flex items-center justify-center mb-5 group-hover:bg-blue-600 transition overflow-hidden">
                            @if($item->foto)
                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama_fasilitas }}" class="w-full h-full" style="object-fit: cover;">
                            @else
                                <svg class="w-7 h-7 text-blue-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21h2m0 0h10M9 7h1m0 4h1m4-4h1m-1 4h1m-6 4h6"/></svg>
                            @endif
                        </div>
                        <h3 class="font-display font-bold text-lg text-blue-900 mb-2">{{ $item->nama_fasilitas }}</h3>
                        <p class="text-slate-500 text-sm">{{ \Illuminate\Support\Str::limit($item->deskripsi ?: 'Fasilitas pembelajaran sekolah guna mendukung kegiatan belajar mengajar secara optimal.', 120) }}</p>
                    </div>
                @empty
                    @foreach([
                        ['Laboratorium Sains', 'Lab fisika, kimia, dan biologi lengkap dengan peralatan praktikum modern.'],
                        ['Perpustakaan Digital', 'Koleksi buku fisik dan digital dengan akses e-learning untuk semua siswa.'],
                        ['Lab Komputer', 'Komputer terkoneksi internet untuk mendukung pembelajaran berbasis teknologi.'],
                        ['Lapangan Olahraga', 'Lapangan basket, futsal, dan voli untuk kegiatan ekstrakurikuler olahraga.'],
                        ['Ruang Kelas Modern', 'Ruang kelas nyaman dengan media pembelajaran yang mendukung.'],
                        ['UKS & Kantin Sehat', 'Unit kesehatan sekolah dan kantin dengan makanan bergizi dan terjangkau.'],
                    ] as [$title, $description])
                        <div class="group p-7 rounded-2xl border border-blue-100 hover:shadow-xl hover:border-blue-200 transition">
                            <div class="w-14 h-14 rounded-xl bg-blue-50 flex items-center justify-center mb-5 group-hover:bg-blue-600 transition">
                                <svg class="w-7 h-7 text-blue-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m6 10V7M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="font-display font-bold text-lg text-blue-900 mb-2">{{ $title }}</h3>
                            <p class="text-slate-500 text-sm">{{ $description }}</p>
                        </div>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    <section id="blog" class="gradient-section py-20">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-wrap justify-between items-end gap-4 mb-12">
                <div>
                    <span class="text-blue-600 font-semibold tracking-wide uppercase text-sm">Blog & Berita</span>
                    <h2 class="font-display text-3xl md:text-4xl font-bold text-blue-900 mt-2">Kabar Terbaru Sekolah</h2>
                </div>
                <a href="{{ route('landing.blog') }}" class="text-blue-600 font-semibold hover:underline">Lihat semua berita &rarr;</a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($posts as $post)
                    <article class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition overflow-hidden group">
                        @if($post->thumbnail)
                            <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="{{ $post->judul }}" class="h-48 w-full" style="object-fit: cover;">
                        @else
                            <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-300"></div>
                        @endif
                        <div class="p-6">
                            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">{{ $post->kategori->nama ?? 'Berita' }}</span>
                            <h3 class="font-display font-bold text-lg text-blue-900 mt-2 mb-2 group-hover:text-blue-600 transition">
                                <a href="{{ route('landing.blog.show', $post->slug) }}">{{ $post->judul }}</a>
                            </h3>
                            <p class="text-slate-500 text-sm mb-4">{{ \Illuminate\Support\Str::limit(strip_tags($post->konten), 125) }}</p>
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>{{ $post->created_at?->translatedFormat('d F Y') }}</span>
                                <a href="{{ route('landing.blog.show', $post->slug) }}" class="text-blue-600 font-semibold">Baca selengkapnya &rarr;</a>
                            </div>
                        </div>
                    </article>
                @empty
                    @foreach([
                        ['Prestasi', 'Tim Olimpiade Sains Raih Prestasi Tingkat Provinsi', 'Siswa-siswi terbaik kami kembali mengukir prestasi membanggakan di kancah olimpiade sains tahun ini.'],
                        ['Kegiatan', 'Pekan Kreativitas Siswa Hadirkan Pameran Karya Inovatif', 'Acara tahunan ini menampilkan proyek sains, seni, dan teknologi hasil karya siswa dari berbagai jurusan.'],
                        ['Pengumuman', 'Jadwal dan Jalur Pendaftaran Siswa Baru 2026/2027', 'Simak informasi lengkap mengenai jalur, syarat, dan tahapan pendaftaran peserta didik baru.'],
                    ] as [$category, $title, $description])
                        <article class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition overflow-hidden group">
                            <div class="h-48 bg-gradient-to-br from-blue-500 to-blue-300"></div>
                            <div class="p-6">
                                <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">{{ $category }}</span>
                                <h3 class="font-display font-bold text-lg text-blue-900 mt-2 mb-2 group-hover:text-blue-600 transition">{{ $title }}</h3>
                                <p class="text-slate-500 text-sm mb-4">{{ $description }}</p>
                                <div class="flex items-center justify-between text-xs text-slate-400">
                                    <span>{{ now()->translatedFormat('d F Y') }}</span>
                                    <a href="#blog" class="text-blue-600 font-semibold">Baca selengkapnya &rarr;</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>
</main>
