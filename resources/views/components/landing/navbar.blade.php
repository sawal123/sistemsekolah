@props(['settings', 'openWave' => null])
@php
    $schoolName = $settings->get('school_name') ?: 'Nama Sekolah';
    $tagline = $settings->get('site_tagline') ?: 'Institusi Pendidikan Menengah Modern';
    $logo = $settings->get('app_logo') ?: $settings->get('logo');
    $logoUrl = $logo ? asset('storage/'.$logo) : asset('assets/images/logo-sekolah.svg');
    $portalUrl = auth()->check() ? route('dashboard') : route('login');
    $portalLabel = auth()->check() ? 'Buka Portal' : 'Login Portal';
    $groups = [
        'Profil Sekolah' => [
            ['Tentang & Sejarah', route('landing.about')],
            ['Guru & Pendidik', route('landing.staffing', 'guru')],
            ['Tata Usaha', route('landing.staffing', 'tata-usaha')],
            ['Struktur Organisasi', route('landing.staffing', 'struktur-organisasi')],
        ],
        'Akademik' => [
            ['Kurikulum & Program', route('landing.academic', 'kurikulum')],
            ['Program IPA', route('landing.academic', 'ipa')],
            ['Program IPS', route('landing.academic', 'ips')],
            ['Program Bahasa', route('landing.academic', 'bahasa')],
            ['Kalender Akademik', route('landing.kalender-akademik')],
        ],
        'Kesiswaan' => [
            ['Prestasi Sekolah', route('landing.prestasi')],
            ['Galeri Kegiatan', route('landing.galeri')],
            ['Jejaring Alumni', route('landing.alumni')],
        ],
        'Informasi' => [
            ['Berita & Artikel', route('landing.blog')],
            ['Pengumuman Resmi', route('landing.pengumuman')],
            ['Agenda Kegiatan', route('landing.kalender-akademik')],
        ],
    ];
@endphp

<div class="bg-[#0F172A] text-slate-300 text-xs border-b border-slate-800">
    <div class="landing-nav-container flex min-h-10 items-center justify-between gap-4 py-2">
        <a href="{{ route('landing.ppdb') }}" class="flex min-w-0 items-center gap-2 hover:text-white">
            <span class="h-2 w-2 shrink-0 rounded-full {{ $openWave ? 'bg-emerald-400' : 'bg-amber-400' }}" aria-hidden="true"></span>
            <span class="truncate font-medium">{{ $openWave ? 'PPDB '.$openWave->tahun_pendaftaran.' · '.$openWave->nama_gelombang.' Dibuka' : 'Informasi jadwal & pendaftaran PPDB' }}</span>
        </a>
        <div class="hidden shrink-0 items-center gap-5 md:flex">
            @if($settings->get('school_email'))<a href="mailto:{{ $settings->get('school_email') }}" class="hidden text-slate-400 hover:text-white lg:inline">{{ $settings->get('school_email') }}</a>@endif
            <a href="{{ route('landing.pengumuman') }}" class="hover:text-white">Pusat Pengumuman</a>
            <a href="{{ route('landing.kalender-akademik') }}" class="hover:text-white">Kalender Akademik</a>
        </div>
    </div>
</div>

<header id="site-header" class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur-md">
    <div class="landing-nav-container flex h-20 items-center justify-between gap-5">
        <a href="{{ route('home') }}" class="landing-brand flex min-w-0 items-center gap-3">
            <img src="{{ $logoUrl }}" alt="Logo {{ $schoolName }}" class="h-11 w-11 shrink-0 rounded-xl object-contain">
            <div class="min-w-0">
                <span class="block truncate font-heading text-base font-bold tracking-tight text-slate-900 sm:text-lg">{{ $schoolName }}</span>
                <span class="hidden truncate text-xs text-slate-500 sm:block">{{ $tagline }}</span>
            </div>
        </a>
        <nav class="landing-desktop-nav items-center gap-0.5" aria-label="Navigasi Utama">
            <a href="{{ route('home') }}" class="landing-nav-link" @if(request()->routeIs('home')) aria-current="page" @endif>Beranda</a>
            @foreach($groups as $label => $links)
                @if($label === 'Informasi')<a href="{{ route('landing.facilities') }}" class="landing-nav-link" @if(request()->routeIs('landing.facilities')) aria-current="page" @endif>Fasilitas</a>@endif
                @php($groupActive = collect($links)->contains(fn ($link) => $link[1] === url()->current()))
                <div class="relative nav-dropdown">
                    <button type="button" data-dropdown-toggle aria-expanded="false" aria-controls="desktop-group-{{ $loop->index }}" class="landing-nav-link inline-flex items-center gap-1.5 {{ $groupActive ? 'is-active' : '' }}">
                        {{ $label }}
                        <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="desktop-group-{{ $loop->index }}" class="nav-dropdown-menu absolute left-0 top-full z-50 w-60 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl">
                        @foreach($links as [$text, $url])
                            <a href="{{ $url }}" class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700" @if($url === url()->current()) aria-current="page" @endif>{{ $text }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <a href="{{ route('landing.contact') }}" class="landing-nav-link" @if(request()->routeIs('landing.contact')) aria-current="page" @endif>Kontak</a>
        </nav>
        <div class="landing-desktop-nav shrink-0 items-center gap-3">
            <a href="{{ route('landing.ppdb') }}" class="landing-nav-action bg-blue-50 text-blue-700 hover:bg-blue-100" @if(request()->routeIs('landing.ppdb*')) aria-current="page" @endif>Info PPDB</a>
            <a href="{{ $portalUrl }}" class="landing-nav-action gap-2 bg-[#0F172A] text-white shadow-sm hover:bg-blue-700">
                <svg class="h-4 w-4 shrink-0" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5m5 5H3m10-9h6a2 2 0 012 2v14a2 2 0 01-2 2h-6"/></svg>
                <span>{{ $portalLabel }}</span>
            </a>
        </div>
        <div class="landing-mobile-nav shrink-0 items-center gap-2">
            <a href="{{ route('landing.ppdb') }}" class="hidden whitespace-nowrap rounded-xl bg-blue-50 px-3 py-2.5 text-xs font-semibold text-blue-700 sm:inline-flex">Info PPDB</a>
            <a href="{{ $portalUrl }}" class="whitespace-nowrap rounded-xl bg-[#0F172A] px-3 py-2.5 text-xs font-semibold text-white hover:bg-blue-700">{{ $portalLabel }}</a>
            <button id="mobile-menu-open" type="button" aria-expanded="false" aria-controls="mobile-menu-drawer" aria-label="Buka menu navigasi" class="rounded-xl p-2.5 text-slate-900 hover:bg-slate-100">
                <svg class="h-6 w-6" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
</header>

<div id="mobile-menu-backdrop" class="landing-mobile-surface fixed inset-0 z-50 bg-slate-950/60 opacity-0 pointer-events-none transition-opacity" aria-hidden="true"></div>
<aside id="mobile-menu-drawer" class="landing-mobile-surface fixed inset-y-0 right-0 z-50 flex w-full max-w-sm translate-x-full flex-col bg-white shadow-2xl transition-transform" role="dialog" aria-modal="true" aria-labelledby="mobile-menu-title" inert>
    <div class="flex items-center justify-between gap-3 border-b border-slate-200 p-5">
        <span id="mobile-menu-title" class="min-w-0 truncate font-heading font-bold">{{ $schoolName }}</span>
        <button id="mobile-menu-close" type="button" aria-label="Tutup menu navigasi" class="shrink-0 rounded-lg p-2 hover:bg-slate-100"><svg class="h-6 w-6" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="m6 6 12 12M6 18 18 6"/></svg></button>
    </div>
    <nav class="flex-1 space-y-2 overflow-y-auto p-5" aria-label="Navigasi Seluler">
        <a href="{{ route('home') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-blue-50">Beranda</a>
        @foreach($groups as $label => $links)
            @if($label === 'Informasi')<a href="{{ route('landing.facilities') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-blue-50">Fasilitas</a>@endif
            <div>
                <button type="button" data-mobile-accordion="mobile-group-{{ $loop->index }}" aria-controls="mobile-group-{{ $loop->index }}" aria-expanded="false" class="flex w-full items-center justify-between rounded-xl px-4 py-3 text-sm font-semibold hover:bg-blue-50">
                    {{ $label }}<svg data-accordion-icon class="h-4 w-4 transition-transform" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div id="mobile-group-{{ $loop->index }}" class="hidden space-y-1 pl-4">
                    @foreach($links as [$text, $url])<a href="{{ $url }}" class="block rounded-lg px-4 py-2.5 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700" @if($url === url()->current()) aria-current="page" @endif>{{ $text }}</a>@endforeach
                </div>
            </div>
        @endforeach
        <a href="{{ route('landing.ppdb') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-blue-50">Info PPDB</a>
        <a href="{{ route('landing.contact') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold hover:bg-blue-50">Kontak Kami</a>
    </nav>
    <div class="space-y-3 border-t border-slate-200 bg-slate-50 p-5">
        <a href="{{ route('landing.ppdb.section', 'daftar') }}#daftar" class="flex justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Daftar PPDB</a>
        <a href="{{ $portalUrl }}" class="flex justify-center rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold">{{ $portalLabel }}</a>
    </div>
</aside>
