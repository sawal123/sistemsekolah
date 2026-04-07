<!DOCTYPE html>
<html lang="id" class="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard — SMA Nusantara</title>

    {{--
    <script src="https://cdn.tailwindcss.com"></script> --}}

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{--
        <script src="https://cdn.tailwindcss.com"></script> --}}
    @else

        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    backdropBlur: { xs: '2px' },
                    colors: {
                        brand: {
                            50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe',
                            300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1',
                            600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 10;
        }

        html,
        body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ── THEME BACKGROUNDS ─────────────────────────────────────── */
        body {
            background: #dde4f5;
            background-image:
                radial-gradient(ellipse 80% 60% at 15% 10%, rgba(99, 102, 241, 0.28) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 85% 85%, rgba(139, 92, 246, 0.22) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 50% 50%, rgba(59, 130, 246, 0.12) 0%, transparent 60%);
            background-attachment: fixed;
            transition: background 0.4s ease;
        }

        .dark body,
        html.dark {
            background: #0c0e1a !important;
            background-image:
                radial-gradient(ellipse 80% 60% at 15% 10%, rgba(99, 102, 241, 0.20) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 85% 85%, rgba(139, 92, 246, 0.16) 0%, transparent 55%),
                radial-gradient(ellipse 50% 40% at 50% 50%, rgba(59, 130, 246, 0.08) 0%, transparent 60%) !important;
            background-attachment: fixed !important;
        }

        html.dark {
            background: #0c0e1a;
        }

        /* ── GLASS MIXIN ───────────────────────────────────────────── */
        .glass {
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.7);
        }

        .glass-dark {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px) saturate(160%);
            -webkit-backdrop-filter: blur(20px) saturate(160%);
            border: 1px solid rgba(255, 255, 255, 0.10);
        }

        html.dark .glass {
            background: rgba(15, 17, 35, 0.65);
            backdrop-filter: blur(20px) saturate(160%);
            -webkit-backdrop-filter: blur(20px) saturate(160%);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-sidebar {
            background: rgba(255, 255, 255, 0.60);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border-right: 1px solid rgba(255, 255, 255, 0.75);
        }

        html.dark .glass-sidebar {
            background: rgba(12, 14, 26, 0.80);
            border-right: 1px solid rgba(255, 255, 255, 0.07);
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(20px) saturate(200%);
            -webkit-backdrop-filter: blur(20px) saturate(200%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.80);
        }

        html.dark .glass-header {
            background: rgba(12, 14, 26, 0.75);
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.60);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 18px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        html.dark .glass-card {
            background: rgba(255, 255, 255, 0.055);
            border: 1px solid rgba(255, 255, 255, 0.09);
        }

        .glass-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.12);
        }

        html.dark .glass-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        /* ── SIDEBAR ───────────────────────────────────────────────── */
        #sidebar {
            width: 268px;
            transition: width 0.3s cubic-bezier(.4, 0, .2, 1), transform 0.3s cubic-bezier(.4, 0, .2, 1);
            overflow: hidden;
            flex-shrink: 0;
        }

        #sidebar.collapsed {
            width: 0;
        }

        /* ── MENU ──────────────────────────────────────────────────── */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.18s ease;
            font-size: 13.5px;
            font-weight: 500;
            color: #4b5563;
            white-space: nowrap;
            text-decoration: none;
        }

        html.dark .nav-item {
            color: rgba(255, 255, 255, 0.6);
        }

        .nav-item:hover {
            background: rgba(99, 102, 241, 0.10);
            color: #4f46e5;
        }

        html.dark .nav-item:hover {
            background: rgba(99, 102, 241, 0.18);
            color: #a5b4fc;
        }

        .nav-item.active {
            background: rgba(99, 102, 241, 0.14);
            color: #4f46e5;
            font-weight: 600;
        }

        html.dark .nav-item.active {
            background: rgba(99, 102, 241, 0.25);
            color: #a5b4fc;
        }

        .nav-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .nav-item.active .nav-icon {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }

        .nav-item.active .nav-icon svg {
            color: white;
        }

        .nav-item:not(.active) .nav-icon {
            background: rgba(99, 102, 241, 0.08);
        }

        html.dark .nav-item:not(.active) .nav-icon {
            background: rgba(99, 102, 241, 0.12);
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s ease;
        }

        .submenu.open {
            max-height: 320px;
        }

        .chevron {
            transition: transform 0.28s ease;
        }

        .chevron.open {
            transform: rotate(180deg);
        }

        .sub-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px 7px 44px;
            border-radius: 10px;
            font-size: 13px;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
            text-decoration: none;
        }

        html.dark .sub-item {
            color: rgba(255, 255, 255, 0.45);
        }

        .sub-item:hover {
            background: rgba(99, 102, 241, 0.08);
            color: #4f46e5;
        }

        html.dark .sub-item:hover {
            background: rgba(99, 102, 241, 0.15);
            color: #a5b4fc;
        }

        /* ── LAYOUT ─────────────────────────────────────────────────── */
        #layout {
            display: flex;
            height: 100vh;
        }

        #mainArea {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
        }

        #content {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* ── TEXT COLORS ─────────────────────────────────────────────── */
        .txt-primary {
            color: #111827;
        }

        html.dark .txt-primary {
            color: rgba(255, 255, 255, 0.92);
        }

        .txt-secondary {
            color: #6b7280;
        }

        html.dark .txt-secondary {
            color: rgba(255, 255, 255, 0.45);
        }

        .txt-muted {
            color: #9ca3af;
        }

        html.dark .txt-muted {
            color: rgba(255, 255, 255, 0.28);
        }

        /* ── DONUT ───────────────────────────────────────────────────── */
        .donut {
            width: 128px;
            height: 128px;
            border-radius: 50%;
            flex-shrink: 0;
            background: conic-gradient(#6366f1 0% 72%, #ef4444 72% 82%, #f59e0b 82% 91%, #10b981 91% 100%);
            position: relative;
        }

        .donut::after {
            content: '';
            position: absolute;
            inset: 22px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.85);
        }

        html.dark .donut::after {
            background: rgba(12, 14, 26, 0.85);
        }

        /* ── BAR CHART ───────────────────────────────────────────────── */
        .bar {
            border-radius: 5px 5px 0 0;
            transition: height 0.6s ease;
        }

        /* ── BADGE ───────────────────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 999px;
        }

        /* ── AVATAR ──────────────────────────────────────────────────── */
        .av {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            flex-shrink: 0;
        }

        /* ── TOGGLE SWITCH ────────────────────────────────────────────── */
        .toggle-track {
            width: 40px;
            height: 22px;
            border-radius: 999px;
            position: relative;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-thumb {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: white;
            position: absolute;
            top: 3px;
            left: 3px;
            transition: transform 0.3s;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
        }

        /* ── PROGRESS ────────────────────────────────────────────────── */
        .prog-track {
            height: 6px;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.07);
            overflow: hidden;
        }

        html.dark .prog-track {
            background: rgba(255, 255, 255, 0.08);
        }

        .prog-bar {
            height: 100%;
            border-radius: 999px;
        }

        /* ── SCROLLBAR ───────────────────────────────────────────────── */
        #content::-webkit-scrollbar,
        #sidebar::-webkit-scrollbar {
            width: 4px;
        }

        #content::-webkit-scrollbar-track,
        #sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        #content::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.3);
            border-radius: 2px;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 2px;
        }

        html.dark #sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
        }

        /* ── ANIMATIONS ──────────────────────────────────────────────── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .fu {
            opacity: 0;
            animation: fadeUp 0.45s ease forwards;
        }

        .d1 {
            animation-delay: .05s
        }

        .d2 {
            animation-delay: .10s
        }

        .d3 {
            animation-delay: .15s
        }

        .d4 {
            animation-delay: .20s
        }

        .d5 {
            animation-delay: .25s
        }

        .d6 {
            animation-delay: .30s
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

        .pulse-dot {
            animation: pulse 1.8s ease infinite;
        }

        /* ── TABLE ───────────────────────────────────────────────────── */
        .tbl th {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding-bottom: 10px;
        }

        .tbl td {
            padding: 12px 0;
            vertical-align: middle;
        }

        .tbl tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        html.dark .tbl tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .tbl tr:last-child {
            border-bottom: none;
        }

        /* ── DROPDOWN ─────────────────────────────────────────────────── */
        .profile-dd {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            z-index: 200;
            min-width: 190px;
            border-radius: 16px;
            padding: 8px;
            display: none;
        }

        .profile-dd.open {
            display: block;
        }

        .dd-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 13px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .dd-item:hover {
            background: rgba(99, 102, 241, 0.08);
        }

        html.dark .dd-item:hover {
            background: rgba(99, 102, 241, 0.15);
        }

        html.dark .dd-item {
            color: rgba(255, 255, 255, 0.7);
        }
    </style>
</head>

<body>
    <div id="layout">

        <!-- ════════════════════════ SIDEBAR ════════════════════════ -->
        <aside id="sidebar" class="glass-sidebar h-full flex flex-col z-50 relative overflow-y-auto"
            style="flex-shrink:0;">

            <!-- Logo -->
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

            <!-- Menu -->
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto" style="min-width:242px;">

                <!-- Dashboard -->
                <a href="#" class="nav-item active">
                    <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                            <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                            <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                            <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                        </svg></span>
                    Dashboard
                </a>

                <!-- ── Data Master ── -->
                <p class="txt-muted px-3 pt-4 pb-1"
                    style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
                    Data Master</p>

                <div>
                    <button onclick="tm('dm')" class="nav-item w-full" style="justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                </svg></span>
                            Data Master
                        </div>
                        <svg id="cv-dm" class="chevron w-3.5 h-3.5 txt-muted flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="sm-dm" class="submenu">
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Tahun
                            Ajaran & Semester</a>
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Mata
                            Pelajaran</a>
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Data
                            Kelas</a>
                    </div>
                </div>

                <!-- ── Civitas Akademik ── -->
                <p class="txt-muted px-3 pt-4 pb-1"
                    style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
                    Civitas Akademik</p>

                <div>
                    <button onclick="tm('ca')" class="nav-item w-full" style="justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg></span>
                            Civitas Akademik
                        </div>
                        <svg id="cv-ca" class="chevron w-3.5 h-3.5 txt-muted flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="sm-ca" class="submenu">
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Data
                            Siswa</a>
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Data
                            Guru</a>
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Data
                            Pengguna</a>
                    </div>
                </div>

                <!-- ── KBM & Laporan ── -->
                <p class="txt-muted px-3 pt-4 pb-1"
                    style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
                    KBM & Laporan</p>

                <div>
                    <button onclick="tm('kbm')" class="nav-item w-full" style="justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg></span>
                            KBM & Laporan
                        </div>
                        <svg id="cv-kbm" class="chevron w-3.5 h-3.5 txt-muted flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="sm-kbm" class="submenu">
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Jadwal
                            Pelajaran</a>
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Rekap
                            Absensi</a>
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Manajemen
                            Nilai</a>
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>e-Rapor</a>
                    </div>
                </div>

                <!-- ── Keuangan ── -->
                <p class="txt-muted px-3 pt-4 pb-1"
                    style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
                    Keuangan</p>

                <div>
                    <button onclick="tm('keu')" class="nav-item w-full" style="justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg></span>
                            Keuangan
                        </div>
                        <svg id="cv-keu" class="chevron w-3.5 h-3.5 txt-muted flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="sm-keu" class="submenu">
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Master
                            Data SPP</a>
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Transaksi
                            Pembayaran</a>
                    </div>
                </div>

                <!-- ── Kelulusan ── -->
                <p class="txt-muted px-3 pt-4 pb-1"
                    style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
                    Kelulusan & Alumni</p>
                <a href="#" class="nav-item">
                    <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg></span>
                    Jejak Alumni
                </a>

                <!-- ── Website ── -->
                <p class="txt-muted px-3 pt-4 pb-1"
                    style="font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;white-space:nowrap;">
                    Pengaturan Website</p>

                <div>
                    <button onclick="tm('web')" class="nav-item w-full" style="justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <span class="nav-icon"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg></span>
                            Pengaturan Website
                        </div>
                        <svg id="cv-web" class="chevron w-3.5 h-3.5 txt-muted flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div id="sm-web" class="submenu">
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Blog
                            / Artikel</a>
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Galeri
                            & Slider</a>
                        <a href="#" class="sub-item"><span
                                style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;opacity:.4;"></span>Pengaturan
                            Umum</a>
                    </div>
                </div>
            </nav>

            <!-- User bottom -->
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
                    {{-- Logout button --}}
                    <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0;">
                        @csrf
                        <button type="submit" title="Logout"
                            style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.18);border-radius:8px;padding:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;"
                            onmouseover="this.style.background='rgba(239,68,68,0.16)'"
                            onmouseout="this.style.background='rgba(239,68,68,0.08)'"
                        >
                            <svg style="width:15px;height:15px;color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ════════════════════════ MAIN AREA ════════════════════════ -->
        <div id="mainArea" style="flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;">

            <!-- ══════════ HEADER ══════════ -->
            <header class="glass-header sticky top-0 z-40 flex items-center justify-between px-6"
                style="height:64px;gap:16px;">

                <!-- Left: Toggle -->
                <div style="display:flex;align-items:center;gap:14px;">
                    <button onclick="toggleSidebar()"
                        style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;background:rgba(99,102,241,0.08);transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(99,102,241,0.16)'"
                        onmouseout="this.style.background='rgba(99,102,241,0.08)'">
                        <svg class="w-5 h-5" style="color:#6366f1;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        <p class="txt-muted" style="font-size:11px;">SMA Nusantara</p>
                        <p class="txt-primary" style="font-size:14px;font-weight:700;line-height:1.2;">Dashboard</p>
                    </div>
                </div>

                <!-- Center: Tahun Ajaran -->
                <div
                    style="display:flex;align-items:center;gap:8px;background:rgba(99,102,241,0.10);border:1px solid rgba(99,102,241,0.20);border-radius:999px;padding:6px 16px;">
                    <span class="pulse-dot"
                        style="width:7px;height:7px;border-radius:50%;background:#6366f1;flex-shrink:0;"></span>
                    <span style="font-size:12px;font-weight:600;color:#6366f1;white-space:nowrap;">Tahun Ajaran:
                        2025/2026 — Ganjil</span>
                </div>

                <!-- Right: Theme + Notif + Profile -->
                <div style="display:flex;align-items:center;gap:10px;">

                    <!-- Dark/Light Toggle -->
                    <div onclick="toggleTheme()"
                        style="display:flex;align-items:center;gap:7px;cursor:pointer;padding:6px 12px;border-radius:999px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.15);transition:all 0.2s;">
                        <svg id="icon-sun" class="w-4 h-4" style="color:#f59e0b;" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                clip-rule="evenodd" />
                        </svg>
                        <svg id="icon-moon" class="w-4 h-4" style="color:#a5b4fc;display:none;" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                        <span id="theme-label" style="font-size:12px;font-weight:600;color:#6366f1;">Light</span>
                    </div>

                    <!-- Notifikasi -->
                    <button
                        style="position:relative;width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:rgba(99,102,241,0.08);border:none;cursor:pointer;transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(99,102,241,0.16)'"
                        onmouseout="this.style.background='rgba(99,102,241,0.08)'">
                        <svg class="w-4 h-4" style="color:#6366f1;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span
                            style="position:absolute;top:6px;right:6px;width:8px;height:8px;border-radius:50%;background:#ef4444;border:2px solid white;"></span>
                    </button>

                    <!-- Profile -->
                    <div style="position:relative;">
                        <button onclick="toggleProfile()"
                            style="display:flex;align-items:center;gap:9px;padding:5px 12px 5px 5px;border-radius:999px;border:1px solid rgba(99,102,241,0.18);background:rgba(99,102,241,0.08);cursor:pointer;transition:background 0.15s;"
                            onmouseover="this.style.background='rgba(99,102,241,0.14)'"
                            onmouseout="this.style.background='rgba(99,102,241,0.08)'">
                            <div
                                style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:11px;">
                                SA</div>
                            <div style="text-align:left;display:none;" class="sm:block">
                                <p class="txt-primary"
                                    style="font-size:12px;font-weight:700;line-height:1.2;white-space:nowrap;">Super
                                    Admin</p>
                                <p class="txt-muted" style="font-size:10px;white-space:nowrap;">Administrator</p>
                            </div>
                            <svg class="w-3 h-3 txt-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="profileDD" class="glass profile-dd" style="box-shadow:0 20px 60px rgba(0,0,0,0.15);">
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
                            <a href="#" class="dd-item" style="color:#ef4444;font-weight:500;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    style="color:#ef4444;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ══════════ CONTENT ══════════ -->
            <div id="content">

                <!-- Welcome -->
                <div class="fu d1">
                    <h1 class="txt-primary" style="font-size:22px;font-weight:800;">Selamat Pagi, Super Admin 👋</h1>
                    <p class="txt-muted" style="font-size:13px;margin-top:3px;">Selasa, 8 Juli 2025 &mdash; Semester
                        Ganjil 2025/2026</p>
                </div>

                <!-- ──── ROW 1: STAT CARDS ──── -->
                <div class="fu d2" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">

                    <!-- Siswa -->
                    <div class="glass-card" style="padding:22px;">
                        <div
                            style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                            <div
                                style="width:44px;height:44px;border-radius:14px;background:rgba(99,102,241,0.12);display:flex;align-items:center;justify-content:center;">
                                <svg class="w-5 h-5" style="color:#6366f1;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <span class="badge"
                                style="background:rgba(16,185,129,0.12);color:#059669;font-size:11px;">+12</span>
                        </div>
                        <p class="txt-primary" style="font-size:28px;font-weight:800;line-height:1;">450</p>
                        <p class="txt-muted" style="font-size:12px;margin-top:4px;">Total Siswa Aktif</p>
                        <div class="prog-track" style="margin-top:14px;">
                            <div class="prog-bar" style="width:72%;background:linear-gradient(90deg,#6366f1,#8b5cf6);">
                            </div>
                        </div>
                    </div>

                    <!-- Guru -->
                    <div class="glass-card" style="padding:22px;">
                        <div
                            style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                            <div
                                style="width:44px;height:44px;border-radius:14px;background:rgba(139,92,246,0.12);display:flex;align-items:center;justify-content:center;">
                                <svg class="w-5 h-5" style="color:#8b5cf6;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span class="badge" style="background:rgba(139,92,246,0.12);color:#7c3aed;font-size:11px;">2
                                Baru</span>
                        </div>
                        <p class="txt-primary" style="font-size:28px;font-weight:800;line-height:1;">35</p>
                        <p class="txt-muted" style="font-size:12px;margin-top:4px;">Total Guru</p>
                        <div class="prog-track" style="margin-top:14px;">
                            <div class="prog-bar" style="width:58%;background:linear-gradient(90deg,#8b5cf6,#a78bfa);">
                            </div>
                        </div>
                    </div>

                    <!-- Rombel -->
                    <div class="glass-card" style="padding:22px;">
                        <div
                            style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                            <div
                                style="width:44px;height:44px;border-radius:14px;background:rgba(245,158,11,0.12);display:flex;align-items:center;justify-content:center;">
                                <svg class="w-5 h-5" style="color:#f59e0b;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="14" y="3" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="3" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                                    <rect x="14" y="14" width="7" height="7" rx="1.5" stroke-width="2" />
                                </svg>
                            </div>
                            <span class="badge" style="background:rgba(245,158,11,0.12);color:#d97706;font-size:11px;">5
                                / Angk.</span>
                        </div>
                        <p class="txt-primary" style="font-size:28px;font-weight:800;line-height:1;">15</p>
                        <p class="txt-muted" style="font-size:12px;margin-top:4px;">Total Rombel / Kelas</p>
                        <div class="prog-track" style="margin-top:14px;">
                            <div class="prog-bar" style="width:100%;background:linear-gradient(90deg,#f59e0b,#fbbf24);">
                            </div>
                        </div>
                    </div>

                    <!-- SPP — solid gradient card -->
                    <div class="glass-card"
                        style="padding:22px;background:linear-gradient(135deg,rgba(99,102,241,0.85),rgba(139,92,246,0.85));border:1px solid rgba(255,255,255,0.20);">
                        <div
                            style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px;">
                            <div
                                style="width:44px;height:44px;border-radius:14px;background:rgba(255,255,255,0.20);display:flex;align-items:center;justify-content:center;">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="badge"
                                style="background:rgba(255,255,255,0.22);color:white;font-size:11px;">+8.2%</span>
                        </div>
                        <p style="font-size:22px;font-weight:800;color:white;line-height:1;">Rp 15 Jt</p>
                        <p style="font-size:12px;color:rgba(255,255,255,0.65);margin-top:4px;">Pendapatan SPP Bulan Ini
                        </p>
                        <div
                            style="margin-top:14px;height:6px;border-radius:999px;background:rgba(255,255,255,0.20);overflow:hidden;">
                            <div style="height:100%;border-radius:999px;width:62%;background:rgba(255,255,255,0.7);">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ──── ROW 2: KEHADIRAN + PENGUMUMAN ──── -->
                <div class="fu d3" style="display:grid;grid-template-columns:2fr 1fr;gap:16px;">

                    <!-- Kehadiran -->
                    <div class="glass-card" style="padding:24px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;">
                            <div>
                                <h3 class="txt-primary" style="font-size:15px;font-weight:700;">Kehadiran Siswa Hari Ini
                                </h3>
                                <p class="txt-muted" style="font-size:12px;margin-top:2px;">Total 450 siswa terdaftar
                                </p>
                            </div>
                            <div
                                style="display:flex;align-items:center;gap:6px;background:rgba(16,185,129,0.10);border:1px solid rgba(16,185,129,0.20);border-radius:999px;padding:5px 12px;">
                                <span class="pulse-dot"
                                    style="width:6px;height:6px;border-radius:50%;background:#10b981;"></span>
                                <span style="font-size:11px;font-weight:600;color:#10b981;">Live</span>
                            </div>
                        </div>

                        <!-- Donut + Legend -->
                        <div style="display:flex;align-items:center;gap:28px;">
                            <div style="position:relative;flex-shrink:0;">
                                <div class="donut"></div>
                                <div
                                    style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;z-index:2;">
                                    <span class="txt-primary"
                                        style="font-size:22px;font-weight:800;line-height:1;">72%</span>
                                    <span class="txt-muted" style="font-size:10px;">Hadir</span>
                                </div>
                            </div>

                            <div style="flex:1;display:flex;flex-direction:column;gap:12px;">
                                <div>
                                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                        <span
                                            style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:500;"
                                            class="txt-secondary">
                                            <span
                                                style="width:10px;height:10px;border-radius:3px;background:#6366f1;flex-shrink:0;"></span>Hadir</span>
                                        <span class="txt-primary" style="font-size:13px;font-weight:700;">324 <span
                                                class="txt-muted"
                                                style="font-weight:400;font-size:11px;">(72%)</span></span>
                                    </div>
                                    <div class="prog-track">
                                        <div class="prog-bar"
                                            style="width:72%;background:linear-gradient(90deg,#6366f1,#818cf8);"></div>
                                    </div>
                                </div>
                                <div>
                                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                        <span
                                            style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:500;"
                                            class="txt-secondary">
                                            <span
                                                style="width:10px;height:10px;border-radius:3px;background:#ef4444;flex-shrink:0;"></span>Absen</span>
                                        <span class="txt-primary" style="font-size:13px;font-weight:700;">45 <span
                                                class="txt-muted"
                                                style="font-weight:400;font-size:11px;">(10%)</span></span>
                                    </div>
                                    <div class="prog-track">
                                        <div class="prog-bar"
                                            style="width:10%;background:linear-gradient(90deg,#ef4444,#f87171);"></div>
                                    </div>
                                </div>
                                <div>
                                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                        <span
                                            style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:500;"
                                            class="txt-secondary">
                                            <span
                                                style="width:10px;height:10px;border-radius:3px;background:#f59e0b;flex-shrink:0;"></span>Sakit</span>
                                        <span class="txt-primary" style="font-size:13px;font-weight:700;">36 <span
                                                class="txt-muted"
                                                style="font-weight:400;font-size:11px;">(8%)</span></span>
                                    </div>
                                    <div class="prog-track">
                                        <div class="prog-bar"
                                            style="width:8%;background:linear-gradient(90deg,#f59e0b,#fbbf24);"></div>
                                    </div>
                                </div>
                                <div>
                                    <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                        <span
                                            style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:500;"
                                            class="txt-secondary">
                                            <span
                                                style="width:10px;height:10px;border-radius:3px;background:#10b981;flex-shrink:0;"></span>Izin</span>
                                        <span class="txt-primary" style="font-size:13px;font-weight:700;">45 <span
                                                class="txt-muted"
                                                style="font-weight:400;font-size:11px;">(10%)</span></span>
                                    </div>
                                    <div class="prog-track">
                                        <div class="prog-bar"
                                            style="width:10%;background:linear-gradient(90deg,#10b981,#34d399);"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 7-day bar chart -->
                        <div style="margin-top:24px;padding-top:20px;border-top:1px solid rgba(99,102,241,0.10);">
                            <p class="txt-muted"
                                style="font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;margin-bottom:12px;">
                                7 Hari Terakhir</p>
                            <div style="display:flex;align-items:flex-end;gap:8px;height:60px;">
                                <div
                                    style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px;height:100%;">
                                    <div style="flex:1;width:100%;display:flex;align-items:flex-end;">
                                        <div class="bar"
                                            style="width:100%;height:65%;background:linear-gradient(180deg,#818cf8,#6366f1);">
                                        </div>
                                    </div>
                                    <span class="txt-muted" style="font-size:10px;">Sen</span>
                                </div>
                                <div
                                    style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px;height:100%;">
                                    <div style="flex:1;width:100%;display:flex;align-items:flex-end;">
                                        <div class="bar"
                                            style="width:100%;height:80%;background:linear-gradient(180deg,#818cf8,#6366f1);">
                                        </div>
                                    </div>
                                    <span class="txt-muted" style="font-size:10px;">Sel</span>
                                </div>
                                <div
                                    style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px;height:100%;">
                                    <div style="flex:1;width:100%;display:flex;align-items:flex-end;">
                                        <div class="bar"
                                            style="width:100%;height:72%;background:linear-gradient(180deg,#818cf8,#6366f1);">
                                        </div>
                                    </div>
                                    <span class="txt-muted" style="font-size:10px;">Rab</span>
                                </div>
                                <div
                                    style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px;height:100%;">
                                    <div style="flex:1;width:100%;display:flex;align-items:flex-end;">
                                        <div class="bar"
                                            style="width:100%;height:88%;background:linear-gradient(180deg,#818cf8,#6366f1);">
                                        </div>
                                    </div>
                                    <span class="txt-muted" style="font-size:10px;">Kam</span>
                                </div>
                                <div
                                    style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px;height:100%;">
                                    <div style="flex:1;width:100%;display:flex;align-items:flex-end;">
                                        <div class="bar"
                                            style="width:100%;height:58%;background:linear-gradient(180deg,#fbbf24,#f59e0b);">
                                        </div>
                                    </div>
                                    <span class="txt-muted" style="font-size:10px;">Jum</span>
                                </div>
                                <div
                                    style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px;height:100%;">
                                    <div style="flex:1;width:100%;display:flex;align-items:flex-end;">
                                        <div class="bar" style="width:100%;height:8%;background:rgba(150,150,150,0.3);">
                                        </div>
                                    </div>
                                    <span class="txt-muted" style="font-size:10px;">Sab</span>
                                </div>
                                <div
                                    style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px;height:100%;">
                                    <div style="flex:1;width:100%;display:flex;align-items:flex-end;">
                                        <div class="bar" style="width:100%;height:8%;background:rgba(150,150,150,0.3);">
                                        </div>
                                    </div>
                                    <span class="txt-muted" style="font-size:10px;">Min</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pengumuman -->
                    <div class="glass-card" style="padding:22px;display:flex;flex-direction:column;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                            <h3 class="txt-primary" style="font-size:15px;font-weight:700;">Pengumuman Internal</h3>
                            <button
                                style="font-size:12px;color:#6366f1;font-weight:600;background:none;border:none;cursor:pointer;">+
                                Tambah</button>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:10px;flex:1;">

                            <div
                                style="padding:13px 14px;border-radius:13px;background:rgba(245,158,11,0.10);border:1px solid rgba(245,158,11,0.22);">
                                <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                                    <span class="badge"
                                        style="background:rgba(245,158,11,0.20);color:#d97706;font-size:10px;">Penting</span>
                                    <span class="txt-muted" style="font-size:10px;">2 jam lalu</span>
                                </div>
                                <p class="txt-primary" style="font-size:12px;font-weight:600;margin-bottom:3px;">Rapat
                                    Koordinasi Wali Kelas</p>
                                <p class="txt-muted" style="font-size:11px;line-height:1.5;">Seluruh wali kelas hadir di
                                    aula pukul 13.00 WIB.</p>
                            </div>

                            <div
                                style="padding:13px 14px;border-radius:13px;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.18);">
                                <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                                    <span class="badge"
                                        style="background:rgba(99,102,241,0.15);color:#6366f1;font-size:10px;">Info</span>
                                    <span class="txt-muted" style="font-size:10px;">1 hari lalu</span>
                                </div>
                                <p class="txt-primary" style="font-size:12px;font-weight:600;margin-bottom:3px;">Input
                                    Nilai UTS Dibuka</p>
                                <p class="txt-muted" style="font-size:11px;line-height:1.5;">Deadline input nilai UTS:
                                    15 Juli 2025.</p>
                            </div>

                            <div
                                style="padding:13px 14px;border-radius:13px;background:rgba(139,92,246,0.08);border:1px solid rgba(139,92,246,0.18);">
                                <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                                    <span class="badge"
                                        style="background:rgba(139,92,246,0.15);color:#7c3aed;font-size:10px;">Sistem</span>
                                    <span class="txt-muted" style="font-size:10px;">3 hari lalu</span>
                                </div>
                                <p class="txt-primary" style="font-size:12px;font-weight:600;margin-bottom:3px;">Backup
                                    Otomatis Selesai</p>
                                <p class="txt-muted" style="font-size:11px;line-height:1.5;">Backup data berhasil pukul
                                    02.00 WIB.</p>
                            </div>
                        </div>
                        <button
                            style="margin-top:14px;width:100%;padding:9px;border-radius:11px;border:1.5px dashed rgba(99,102,241,0.25);background:none;cursor:pointer;font-size:12px;color:#6366f1;font-weight:500;transition:all 0.15s;"
                            onmouseover="this.style.background='rgba(99,102,241,0.06)'"
                            onmouseout="this.style.background='none'">Lihat Semua</button>
                    </div>
                </div>

                <!-- ──── ROW 3: SPP TABLE + ULTAH ──── -->
                <div class="fu d4" style="display:grid;grid-template-columns:2fr 1fr;gap:16px;">

                    <!-- SPP Table -->
                    <div class="glass-card" style="padding:24px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                            <div>
                                <h3 class="txt-primary" style="font-size:15px;font-weight:700;">Transaksi SPP Terbaru
                                </h3>
                                <p class="txt-muted" style="font-size:12px;margin-top:2px;">5 transaksi terakhir hari
                                    ini</p>
                            </div>
                            <a href="#"
                                style="font-size:12px;color:#6366f1;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:4px;">
                                Lihat Semua <svg style="width:12px;height:12px;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <table class="tbl" style="width:100%;border-collapse:collapse;">
                            <thead>
                                <tr>
                                    <th class="txt-muted" style="text-align:left;">Siswa</th>
                                    <th class="txt-muted" style="text-align:left;">Kelas</th>
                                    <th class="txt-muted" style="text-align:left;">Bulan</th>
                                    <th class="txt-muted" style="text-align:right;">Nominal</th>
                                    <th class="txt-muted" style="text-align:center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div class="av" style="background:rgba(99,102,241,0.15);color:#6366f1;">AR
                                            </div><span class="txt-primary"
                                                style="font-size:13px;font-weight:500;">Anisa Rahmawati</span>
                                        </div>
                                    </td>
                                    <td class="txt-secondary" style="font-size:13px;">XII IPA 1</td>
                                    <td class="txt-secondary" style="font-size:13px;">Juli 2025</td>
                                    <td class="txt-primary" style="font-size:13px;font-weight:600;text-align:right;">Rp
                                        350.000</td>
                                    <td style="text-align:center;"><span class="badge"
                                            style="background:rgba(16,185,129,0.12);color:#059669;">Lunas</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div class="av" style="background:rgba(139,92,246,0.15);color:#7c3aed;">BP
                                            </div><span class="txt-primary" style="font-size:13px;font-weight:500;">Budi
                                                Pratama</span>
                                        </div>
                                    </td>
                                    <td class="txt-secondary" style="font-size:13px;">XI IPS 2</td>
                                    <td class="txt-secondary" style="font-size:13px;">Juli 2025</td>
                                    <td class="txt-primary" style="font-size:13px;font-weight:600;text-align:right;">Rp
                                        350.000</td>
                                    <td style="text-align:center;"><span class="badge"
                                            style="background:rgba(16,185,129,0.12);color:#059669;">Lunas</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div class="av" style="background:rgba(245,158,11,0.15);color:#d97706;">CL
                                            </div><span class="txt-primary"
                                                style="font-size:13px;font-weight:500;">Citra Lestari</span>
                                        </div>
                                    </td>
                                    <td class="txt-secondary" style="font-size:13px;">X IPA 3</td>
                                    <td class="txt-secondary" style="font-size:13px;">Juni 2025</td>
                                    <td class="txt-primary" style="font-size:13px;font-weight:600;text-align:right;">Rp
                                        350.000</td>
                                    <td style="text-align:center;"><span class="badge"
                                            style="background:rgba(245,158,11,0.12);color:#d97706;">Sebagian</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div class="av" style="background:rgba(16,185,129,0.15);color:#059669;">DH
                                            </div><span class="txt-primary"
                                                style="font-size:13px;font-weight:500;">Dimas Haryanto</span>
                                        </div>
                                    </td>
                                    <td class="txt-secondary" style="font-size:13px;">XII IPS 1</td>
                                    <td class="txt-secondary" style="font-size:13px;">Juli 2025</td>
                                    <td class="txt-primary" style="font-size:13px;font-weight:600;text-align:right;">Rp
                                        350.000</td>
                                    <td style="text-align:center;"><span class="badge"
                                            style="background:rgba(16,185,129,0.12);color:#059669;">Lunas</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="display:flex;align-items:center;gap:10px;">
                                            <div class="av" style="background:rgba(239,68,68,0.15);color:#dc2626;">EN
                                            </div><span class="txt-primary" style="font-size:13px;font-weight:500;">Eka
                                                Nugroho</span>
                                        </div>
                                    </td>
                                    <td class="txt-secondary" style="font-size:13px;">XI IPA 2</td>
                                    <td class="txt-secondary" style="font-size:13px;">Mei 2025</td>
                                    <td class="txt-primary" style="font-size:13px;font-weight:600;text-align:right;">Rp
                                        350.000</td>
                                    <td style="text-align:center;"><span class="badge"
                                            style="background:rgba(239,68,68,0.12);color:#dc2626;">Tunggak</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Ulang Tahun -->
                    <div class="glass-card" style="padding:22px;display:flex;flex-direction:column;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
                            <div>
                                <h3 class="txt-primary" style="font-size:15px;font-weight:700;">Ulang Tahun</h3>
                                <p class="txt-muted" style="font-size:12px;margin-top:2px;">8 Juli 2025</p>
                            </div>
                            <span style="font-size:22px;">🎂</span>
                        </div>

                        <div style="display:flex;flex-direction:column;gap:10px;flex:1;">
                            <div
                                style="display:flex;align-items:center;gap:11px;padding:11px 13px;border-radius:13px;background:rgba(236,72,153,0.09);border:1px solid rgba(236,72,153,0.18);">
                                <div class="av" style="background:rgba(236,72,153,0.20);color:#be185d;">FN</div>
                                <div style="flex:1;min-width:0;">
                                    <p class="txt-primary" style="font-size:12px;font-weight:600;">Farah Nabila</p>
                                    <p class="txt-muted" style="font-size:11px;">XI IPA 1 — 17 tahun</p>
                                </div>
                                <button
                                    style="font-size:10px;background:rgba(236,72,153,0.18);color:#be185d;border:none;padding:5px 10px;border-radius:999px;cursor:pointer;font-weight:600;white-space:nowrap;">Ucapkan</button>
                            </div>

                            <div
                                style="display:flex;align-items:center;gap:11px;padding:11px 13px;border-radius:13px;background:rgba(139,92,246,0.09);border:1px solid rgba(139,92,246,0.18);">
                                <div class="av" style="background:rgba(139,92,246,0.20);color:#7c3aed;">GS</div>
                                <div style="flex:1;min-width:0;">
                                    <p class="txt-primary" style="font-size:12px;font-weight:600;">Gilang Santoso</p>
                                    <p class="txt-muted" style="font-size:11px;">XII IPS 2 — 18 tahun</p>
                                </div>
                                <button
                                    style="font-size:10px;background:rgba(139,92,246,0.18);color:#7c3aed;border:none;padding:5px 10px;border-radius:999px;cursor:pointer;font-weight:600;white-space:nowrap;">Ucapkan</button>
                            </div>

                            <div
                                style="display:flex;align-items:center;gap:11px;padding:11px 13px;border-radius:13px;background:rgba(59,130,246,0.09);border:1px solid rgba(59,130,246,0.18);">
                                <div class="av" style="background:rgba(59,130,246,0.20);color:#1d4ed8;">HW</div>
                                <div style="flex:1;min-width:0;">
                                    <p class="txt-primary" style="font-size:12px;font-weight:600;">Hana Wijayanti</p>
                                    <p class="txt-muted" style="font-size:11px;">X IPA 2 — 16 tahun</p>
                                </div>
                                <button
                                    style="font-size:10px;background:rgba(59,130,246,0.18);color:#1d4ed8;border:none;padding:5px 10px;border-radius:999px;cursor:pointer;font-weight:600;white-space:nowrap;">Ucapkan</button>
                            </div>
                        </div>

                        <div
                            style="margin-top:14px;padding:13px;border-radius:13px;background:linear-gradient(135deg,rgba(99,102,241,0.12),rgba(139,92,246,0.12));border:1px solid rgba(99,102,241,0.18);text-align:center;">
                            <p style="font-size:13px;font-weight:700;color:#6366f1;">3 siswa berulang tahun hari ini 🎉
                            </p>
                            <p class="txt-muted" style="font-size:11px;margin-top:3px;">Semoga selalu semangat belajar!
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div style="padding:12px 0;display:flex;align-items:center;justify-content:space-between;">
                    <p class="txt-muted" style="font-size:11px;">SMA Nusantara &copy; 2025 — Admin Panel v1.0</p>
                    <p class="txt-muted" style="font-size:11px;">Diperbarui: 08 Jul 2025, 07:30 WIB</p>
                </div>

            </div><!-- /content -->
        </div><!-- /mainArea -->
    </div><!-- /layout -->

    <script>
        function tm(id) {
            const s = document.getElementById('sm-' + id);
            const c = document.getElementById('cv-' + id);
            s.classList.toggle('open');
            c.classList.toggle('open');
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        function toggleProfile() {
            document.getElementById('profileDD').classList.toggle('open');
        }

        let dark = false;
        function toggleTheme() {
            dark = !dark;
            document.documentElement.classList.toggle('dark', dark);
            document.getElementById('icon-sun').style.display = dark ? 'none' : '';
            document.getElementById('icon-moon').style.display = dark ? '' : 'none';
            document.getElementById('theme-label').textContent = dark ? 'Dark' : 'Light';
            document.getElementById('theme-label').style.color = dark ? '#a5b4fc' : '#6366f1';

            if (dark) {
                document.body.style.background = '#0c0e1a';
                document.body.style.backgroundImage =
                    'radial-gradient(ellipse 80% 60% at 15% 10%, rgba(99,102,241,0.20) 0%, transparent 60%),' +
                    'radial-gradient(ellipse 60% 50% at 85% 85%, rgba(139,92,246,0.16) 0%, transparent 55%),' +
                    'radial-gradient(ellipse 50% 40% at 50% 50%, rgba(59,130,246,0.08) 0%, transparent 60%)';
            } else {
                document.body.style.background = '#dde4f5';
                document.body.style.backgroundImage =
                    'radial-gradient(ellipse 80% 60% at 15% 10%, rgba(99,102,241,0.28) 0%, transparent 60%),' +
                    'radial-gradient(ellipse 60% 50% at 85% 85%, rgba(139,92,246,0.22) 0%, transparent 55%),' +
                    'radial-gradient(ellipse 50% 40% at 50% 50%, rgba(59,130,246,0.12) 0%, transparent 60%)';
            }
        }

        document.addEventListener('click', e => {
            const dd = document.getElementById('profileDD');
            if (!e.target.closest('[onclick="toggleProfile()"]') && !dd.contains(e.target)) {
                dd.classList.remove('open');
            }
        });
    </script>
</body>

</html>