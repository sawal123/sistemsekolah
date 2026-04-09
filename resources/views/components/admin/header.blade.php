<header class="glass-header sticky top-0 z-40 flex items-center justify-between px-6" style="height:64px;gap:16px;">

    {{-- Left: Toggle + Page Title --}}
    <div style="display:flex;align-items:center;gap:14px;">
        <button @click="$store.sidebar.toggle()"
            style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;background:rgba(99,102,241,0.08);transition:background 0.15s;"
            onmouseover="this.style.background='rgba(99,102,241,0.16)'"
            onmouseout="this.style.background='rgba(99,102,241,0.08)'">
            <svg class="w-5 h-5" style="color:#6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <div>
            <p class="txt-muted" style="font-size:11px;">SMA Nusantara</p>
            <p class="txt-primary" style="font-size:14px;font-weight:700;line-height:1.2;">
                {{ $title ?? 'Dashboard' }}
            </p>
        </div>
    </div>

    {{-- Center: Tahun Ajaran --}}
    <div class="hidden md:flex"
        style="align-items:center;gap:8px;background:rgba(99,102,241,0.10);border:1px solid rgba(99,102,241,0.20);border-radius:999px;padding:6px 16px;">
        <span class="pulse-dot" style="width:7px;height:7px;border-radius:50%;background:#6366f1;flex-shrink:0;"></span>
        <span style="font-size:12px;font-weight:600;color:#6366f1;white-space:nowrap;">Tahun Ajaran:
            2025/2026 — Ganjil</span>
    </div>

    {{-- Right: Theme + Notif + Profile --}}
    <div style="display:flex;align-items:center;gap:10px;">

        {{-- Dark/Light Toggle --}}
        <div @click="$store.theme.toggle()"
            style="display:flex;align-items:center;gap:7px;cursor:pointer;padding:6px 12px;border-radius:999px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.15);transition:all 0.2s;">
            <svg x-show="!$store.theme.dark" class="w-4 h-4" style="color:#f59e0b;" fill="currentColor"
                viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                    clip-rule="evenodd" />
            </svg>
            <svg x-show="$store.theme.dark" x-cloak class="w-4 h-4" style="color:#a5b4fc;" fill="currentColor"
                viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
            </svg>
            <span style="font-size:12px;font-weight:600;"
                x-bind:style="$store.theme.dark ? 'color:#a5b4fc' : 'color:#6366f1'"
                x-text="$store.theme.dark ? 'Dark' : 'Light'"></span>
        </div>

        {{-- Notifikasi --}}
        <button
            style="position:relative;width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:rgba(99,102,241,0.08);border:none;cursor:pointer;transition:background 0.15s;"
            onmouseover="this.style.background='rgba(99,102,241,0.16)'"
            onmouseout="this.style.background='rgba(99,102,241,0.08)'">
            <svg class="w-4 h-4" style="color:#6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span
                style="position:absolute;top:6px;right:6px;width:8px;height:8px;border-radius:50%;background:#ef4444;border:2px solid white;"></span>
        </button>

        {{-- Profile Dropdown --}}
        <div style="position:relative;" x-data="{ profileOpen: false }">
            <button @click="profileOpen = !profileOpen"
                style="display:flex;align-items:center;gap:9px;padding:5px 12px 5px 5px;border-radius:999px;border:1px solid rgba(99,102,241,0.18);background:rgba(99,102,241,0.08);cursor:pointer;transition:background 0.15s;"
                onmouseover="this.style.background='rgba(99,102,241,0.14)'"
                onmouseout="this.style.background='rgba(99,102,241,0.08)'">
                <div
                    style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:11px;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </div>
                <div style="text-align:left;display:none;" class="sm:block">
                    <p class="txt-primary" style="font-size:12px;font-weight:700;line-height:1.2;white-space:nowrap;">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </p>
                    <p class="txt-muted" style="font-size:10px;white-space:nowrap;">Administrator</p>
                </div>
                <svg class="w-3 h-3 txt-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="profileOpen" x-cloak @click.away="profileOpen = false"
                x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class=" glass profile-dd" style="box-shadow:0 20px 60px rgba(0,0,0,0.15);">
                <a href="#" class="dd-item txt-secondary" style="font-weight:500;">
                    <svg class="w-4 h-4 txt-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil Saya
                </a>
                <a href="#" class="dd-item txt-secondary" style="font-weight:500;">
                    <svg class="w-4 h-4 txt-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <circle cx="12" cy="12" r="3" stroke-width="2" />
                    </svg>
                    Pengaturan
                </a>
                <div style="margin:4px 0;height:1px;background:rgba(99,102,241,0.10);"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dd-item w-full"
                        style="color:#ef4444;font-weight:500;border:none;background:none;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="color:#ef4444;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>