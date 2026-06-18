<div class="flex flex-col gap-6 pb-20">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Dashboard</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Ringkasan penting hari ini, {{ now()->translatedFormat('l, d F Y') }}.</p>
        </div>

        @hasrole('admin')
            <a href="{{ route('admin.website.pengunjung') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2 font-semibold text-sm rounded-xl transition-all duration-200 bg-indigo-500 text-white shadow-md shadow-indigo-500/25">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14m-6 1a4 4 0 100-8 4 4 0 000 8z" />
                </svg>
                Detail Pengunjung Website
            </a>
        @endhasrole
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach([
            ['Siswa Aktif', $totalSiswa, 'Data siswa aktif saat ini', 'bg-indigo-500/10 text-indigo-500'],
            ['Guru', $totalGuru, 'Total guru terdata', 'bg-emerald-500/10 text-emerald-500'],
            ['Rombel', $totalKelas, 'Kelas/rombel aktif', 'bg-amber-500/10 text-amber-500'],
            ['Visit Hari Ini', $visitToday, $uniqueVisitToday . ' pengunjung unik', 'bg-blue-500/10 text-blue-500'],
        ] as [$label, $value, $caption, $class])
            <x-ui.card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs txt-muted font-semibold">{{ $label }}</p>
                        <h2 class="text-3xl font-black txt-primary mt-2">{{ $value }}</h2>
                        <p class="text-xs txt-muted mt-1">{{ $caption }}</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl {{ $class }} flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </x-ui.card>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_380px] gap-5">
        <x-ui.card>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
                <div>
                    <h2 class="txt-primary text-base font-bold">Grafik Visit Website</h2>
                    <p class="txt-muted text-xs mt-1">Kunjungan landing page selama 7 hari terakhir.</p>
                </div>
                <div class="flex items-center gap-4 text-xs txt-muted">
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-indigo-500"></span>Total</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-red-500"></span>Bahaya</span>
                </div>
            </div>

            <div class="h-72 flex items-end gap-3 border-b border-indigo-500/10 pb-4">
                @foreach($visitChart as $point)
                    @php
                        $height = max(8, ($point['total'] / $maxVisit) * 100);
                        $dangerHeight = $point['total'] > 0 ? max(4, ($point['danger'] / max($point['total'], 1)) * $height) : 0;
                    @endphp
                    <div class="flex-1 h-full flex flex-col items-center justify-end gap-2">
                        <div class="w-full h-full flex items-end justify-center gap-1">
                            <div class="w-7 rounded-t-lg bg-indigo-500/80" style="height: {{ $height }}%;" title="{{ $point['total'] }} visit"></div>
                            <div class="w-3 rounded-t bg-red-500/80" style="height: {{ $dangerHeight }}%;" title="{{ $point['danger'] }} log bahaya"></div>
                        </div>
                        <div class="text-center">
                            <p class="text-[11px] font-bold txt-primary">{{ $point['label'] }}</p>
                            <p class="text-[10px] txt-muted">{{ $point['date'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <div class="space-y-5">
            <x-ui.card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="txt-primary text-base font-bold">Keamanan Website</h2>
                        <p class="txt-muted text-xs mt-1">Request mencurigakan yang perlu dipantau.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full {{ $dangerToday > 0 ? 'bg-red-500/10 text-red-500' : 'bg-emerald-500/10 text-emerald-500' }} text-[10px] font-bold uppercase">
                        {{ $dangerToday }} hari ini
                    </span>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($dangerLogs as $log)
                        <div class="rounded-xl border border-red-500/10 bg-red-500/5 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-xs font-black text-red-500">{{ $log->threat_type }}</p>
                                <p class="text-[10px] txt-muted">{{ $log->visited_at?->format('H:i') }}</p>
                            </div>
                            <p class="text-xs txt-primary font-semibold mt-1">{{ $log->ip_address ?: '-' }}</p>
                            <p class="text-[11px] txt-muted mt-1 break-all">{{ $log->path }}</p>
                        </div>
                    @empty
                        <div class="rounded-xl border border-emerald-500/10 bg-emerald-500/5 p-4">
                            <p class="text-sm font-bold text-emerald-500">Belum ada log berbahaya.</p>
                            <p class="text-xs txt-muted mt-1">Pantauan hari ini terlihat aman.</p>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>

            @hasrole('admin')
                <x-ui.card>
                    <h2 class="txt-primary text-base font-bold">PPDB</h2>
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-xl bg-indigo-500/10 p-4">
                            <p class="text-2xl font-black text-indigo-500">{{ $ppdbOpen }}</p>
                            <p class="text-xs txt-muted mt-1">Gelombang dibuka</p>
                        </div>
                        <div class="rounded-xl bg-amber-500/10 p-4">
                            <p class="text-2xl font-black text-amber-500">{{ $ppdbNeedReview }}</p>
                            <p class="text-xs txt-muted mt-1">Perlu dicek</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.ppdb.pendaftaran-murid-baru') }}" wire:navigate class="mt-4 inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded-xl bg-indigo-500/10 text-indigo-500 text-sm font-bold">
                        Buka Data PPDB
                    </a>
                </x-ui.card>
            @endhasrole
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <x-ui.card class="lg:col-span-2">
            <h2 class="txt-primary text-base font-bold">Fokus Hari Ini</h2>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="rounded-xl border border-indigo-500/10 p-4">
                    <p class="text-xs txt-muted font-semibold">PPDB Baru</p>
                    <p class="text-2xl font-black txt-primary mt-2">{{ $ppdbNew }}</p>
                    <p class="text-xs txt-muted mt-1">Pendaftar belum diperiksa.</p>
                </div>
                <div class="rounded-xl border border-indigo-500/10 p-4">
                    <p class="text-xs txt-muted font-semibold">Unique Visitor</p>
                    <p class="text-2xl font-black txt-primary mt-2">{{ $uniqueVisitToday }}</p>
                    <p class="text-xs txt-muted mt-1">Pengunjung berbeda hari ini.</p>
                </div>
                <div class="rounded-xl border border-indigo-500/10 p-4">
                    <p class="text-xs txt-muted font-semibold">Log Bahaya</p>
                    <p class="text-2xl font-black {{ $dangerToday > 0 ? 'text-red-500' : 'text-emerald-500' }} mt-2">{{ $dangerToday }}</p>
                    <p class="text-xs txt-muted mt-1">Request mencurigakan hari ini.</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <h2 class="txt-primary text-base font-bold">Waktu Sistem</h2>
            <p class="text-3xl font-black txt-primary mt-3">{{ now()->format('H:i') }}</p>
            <p class="text-xs txt-muted mt-1">Asia/Jakarta</p>
            <p class="text-xs txt-muted mt-5">Diperbarui saat halaman dimuat.</p>
        </x-ui.card>
    </div>
</div>
