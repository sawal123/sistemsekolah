<aside id="sidebar" class="glass-sidebar h-full flex flex-col z-50 relative overflow-y-auto"
    x-bind:class="{ 'collapsed': !$store.sidebar.open }" style="flex-shrink:0;">

    {{-- Logo --}}
    <div class="px-5 py-5 flex items-center gap-3 border-b" style="border-color:rgba(99,102,241,0.12);">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
            style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M12 14l9-5-9-5-9 5 9 5z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
        </div>
        <div style="white-space:nowrap;">
            <p class="txt-primary font-bold text-sm leading-tight">SMA Nusantara</p>
            <p class="txt-muted" style="font-size:11px;">Admin Panel</p>
        </div>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto" style="min-width:242px;">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" wire:navigate
            class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                    <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                    <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                    <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                </svg></span>
            Dashboard
        </a>

        {{-- ── Data Master ── --}}
        <p class="txt-muted px-3 pt-4 pb-1"
            style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
            Data Master</p>

        <div x-data="{ open: false }">
            <button @click="open = !open" class="nav-item w-full" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg></span>
                    Data Master
                </div>
                <svg class="chevron w-3.5 h-3.5 txt-muted flex-shrink-0" :class="{ 'open': open }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div class="submenu" :class="{ 'open': open }">
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Tahun
                    Ajaran & Semester</a>
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Mata
                    Pelajaran</a>
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Data
                    Kelas</a>
            </div>
        </div>

        {{-- ── Civitas Akademik ── --}}
        <p class="txt-muted px-3 pt-4 pb-1"
            style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
            Civitas Akademik</p>

        <div x-data="{ open: false }">
            <button @click="open = !open" class="nav-item w-full" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg></span>
                    Civitas Akademik
                </div>
                <svg class="chevron w-3.5 h-3.5 txt-muted flex-shrink-0" :class="{ 'open': open }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div class="submenu" :class="{ 'open': open }">
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Data
                    Siswa</a>
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Data
                    Guru</a>
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Data
                    Pengguna</a>
            </div>
        </div>

        {{-- ── KBM & Laporan ── --}}
        <p class="txt-muted px-3 pt-4 pb-1"
            style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
            KBM & Laporan</p>

        <div x-data="{ open: false }">
            <button @click="open = !open" class="nav-item w-full" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg></span>
                    KBM & Laporan
                </div>
                <svg class="chevron w-3.5 h-3.5 txt-muted flex-shrink-0" :class="{ 'open': open }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div class="submenu" :class="{ 'open': open }">
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Jadwal
                    Pelajaran</a>
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Rekap
                    Absensi</a>
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Manajemen
                    Nilai</a>
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>e-Rapor</a>
            </div>
        </div>

        {{-- ── Keuangan ── --}}
        <p class="txt-muted px-3 pt-4 pb-1"
            style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
            Keuangan</p>

        <div x-data="{ open: false }">
            <button @click="open = !open" class="nav-item w-full" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg></span>
                    Keuangan
                </div>
                <svg class="chevron w-3.5 h-3.5 txt-muted flex-shrink-0" :class="{ 'open': open }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div class="submenu" :class="{ 'open': open }">
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Master
                    Data SPP</a>
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Transaksi
                    Pembayaran</a>
            </div>
        </div>

        {{-- ── Kelulusan ── --}}
        <p class="txt-muted px-3 pt-4 pb-1"
            style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
            Kelulusan & Alumni</p>
        <a href="#" wire:navigate class="nav-item">
            <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg></span>
            Jejak Alumni
        </a>

        {{-- ── Website ── --}}
        <p class="txt-muted px-3 pt-4 pb-1"
            style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
            Pengaturan Website</p>

        <div x-data="{ open: false }">
            <button @click="open = !open" class="nav-item w-full" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg></span>
                    Pengaturan Website
                </div>
                <svg class="chevron w-3.5 h-3.5 txt-muted flex-shrink-0" :class="{ 'open': open }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div class="submenu" :class="{ 'open': open }">
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Blog
                    / Artikel</a>
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Galeri
                    & Slider</a>
                <a href="#" wire:navigate class="sub-item"><span
                        style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Pengaturan
                    Umum</a>
            </div>
        </div>
    </nav>

    {{-- User bottom --}}
    <div class="px-4 py-4" style="border-top:1px solid rgba(99,102,241,0.10);">
        <div style="display:flex;align-items:center;gap:10px;white-space:nowrap;">
            <div
                style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:12px;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</div>
            <div style="flex:1;min-width:0;">
                <p class="txt-primary"
                    style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="txt-muted"
                    style="font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ auth()->user()->email ?? '' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0;">
                @csrf
                <button type="submit" title="Logout"
                    style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.18);border-radius:8px;padding:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                    onmouseover="this.style.background='rgba(239,68,68,0.16)'"
                    onmouseout="this.style.background='rgba(239,68,68,0.08)'">
                    <svg style="width:15px;height:15px;color:#ef4444;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
