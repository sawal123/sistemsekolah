<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Admin Dashboard' }} — SMA Nusantara</title>
    <meta name="description" content="Panel Admin SMA Nusantara — Sistem Manajemen Sekolah" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />

    @livewireStyles
</head>

<body x-init="$store.theme.init()">
    <div id="layout">


        {{-- ════════════════════════ SIDEBAR ════════════════════════ --}}
        @persist('sidebar')
        @include('components.admin.sidebar')
        @endpersist

        {{-- ════════════════════════ MAIN AREA ════════════════════════ --}}
        <div id="mainArea" style="flex:1;display:flex;flex-direction:column;overflow:hidden;min-width:0;">

            {{-- ══════════ HEADER ══════════ --}}
            @include('components.admin.header')

            {{-- ══════════ CONTENT ══════════ --}}
            <div id="content">
                {{ $slot }}
            </div>

        </div>
    </div>



    @livewireScripts

    <script>
        function setupAdminStores() {
            const initStores = (Alpine) => {
                // Jangan override jika sudah ada
                if (!Alpine.store('theme')) {
                    Alpine.store('theme', {
                        dark: localStorage.getItem('theme') === 'dark',

                        toggle() {
                            this.dark = !this.dark;
                            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                            this.applyBackground();
                        },

                        applyBackground() {
                            if (this.dark) {
                                document.documentElement.classList.add('dark');
                                document.body.style.background = '#0c0e1a';
                                document.body.style.backgroundImage =
                                    'radial-gradient(ellipse 80% 60% at 15% 10%, rgba(99,102,241,0.20) 0%, transparent 60%),' +
                                    'radial-gradient(ellipse 60% 50% at 85% 85%, rgba(139,92,246,0.16) 0%, transparent 55%),' +
                                    'radial-gradient(ellipse 50% 40% at 50% 50%, rgba(59,130,246,0.08) 0%, transparent 60%)';
                            } else {
                                document.documentElement.classList.remove('dark');
                                document.body.style.background = '#dde4f5';
                                document.body.style.backgroundImage =
                                    'radial-gradient(ellipse 80% 60% at 15% 10%, rgba(99,102,241,0.28) 0%, transparent 60%),' +
                                    'radial-gradient(ellipse 60% 50% at 85% 85%, rgba(139,92,246,0.22) 0%, transparent 55%),' +
                                    'radial-gradient(ellipse 50% 40% at 50% 50%, rgba(59,130,246,0.12) 0%, transparent 60%)';
                            }
                        },

                        init() {
                            this.applyBackground();
                        }
                    });
                }

                if (!Alpine.store('sidebar')) {
                    Alpine.store('sidebar', {
                        open: window.innerWidth >= 1024,

                        toggle() {
                            this.open = !this.open;
                        }
                    });
                }
            };

            // Jika Alpine sudah ada (karena wire:navigate), langsung eksekusi
            if (window.Alpine) {
                initStores(window.Alpine);
            } else {
                // Jika load pertama kali, tunggu event init
                document.addEventListener('alpine:init', () => initStores(window.Alpine));
            }
        }
        setupAdminStores();

        // Memastikan background tetap di-apply ulang saat navigasi selesai
        document.addEventListener('livewire:navigated', () => {
            if (window.Alpine && window.Alpine.store('theme')) {
                window.Alpine.store('theme').applyBackground();
            }
        });
    </script>
</body>

</html>