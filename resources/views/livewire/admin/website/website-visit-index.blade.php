<div class="flex flex-col gap-6 pb-20">
    <x-ui.toast />

    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Pengunjung Website</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Pantau kunjungan landing page dan request yang mencurigakan.</p>
        </div>

        <a href="{{ route('dashboard') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2 font-semibold text-sm rounded-xl transition-all duration-200 glass txt-secondary hover:bg-white/40 dark:hover:bg-white/10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach([
            ['Kunjungan Hari Ini', $stats['today'], 'bg-indigo-500/10 text-indigo-500'],
            ['Pengunjung Unik', $stats['unique_today'], 'bg-blue-500/10 text-blue-500'],
            ['Bahaya Hari Ini', $stats['danger_today'], 'bg-red-500/10 text-red-500'],
            ['Total Log Bahaya', $stats['total_danger'], 'bg-amber-500/10 text-amber-500'],
        ] as [$label, $value, $class])
            <x-ui.card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs txt-muted font-medium">{{ $label }}</p>
                        <h3 class="text-2xl font-black txt-primary mt-1">{{ $value }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl {{ $class }} flex items-center justify-center font-black">
                        {{ substr($label, 0, 1) }}
                    </div>
                </div>
            </x-ui.card>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-5">
        <x-ui.card padding="0">
            <div class="flex flex-wrap items-center gap-3 p-3">
                <div class="w-20 flex-shrink-0">
                    <x-ui.select wire:model.live="perPage" :options="['15' => '15', '30' => '30', '50' => '50']" />
                </div>
                <div class="flex-1 min-w-[180px]">
                    <x-ui.search model="search" placeholder="Cari IP, URL, threat, atau user agent..." />
                </div>
                <div class="w-44">
                    <x-ui.select wire:model.live="filterRisk" :options="['' => 'Semua Risiko', 'danger' => 'Berbahaya', 'safe' => 'Aman']" />
                </div>
                <div class="w-44">
                    <x-ui.input wire:model.live="filterDate" type="date" />
                </div>
            </div>

            <div class="rounded-xl overflow-hidden border border-indigo-500/10 dark:border-white/10 m-3 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left" style="border-collapse: separate; border-spacing: 0;">
                        <thead>
                            <tr class="bg-indigo-500/5 dark:bg-white/5">
                                <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Waktu</th>
                                <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">IP & URL</th>
                                <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">User Agent</th>
                                <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-500/10 dark:divide-white/10">
                            @forelse($visits as $visit)
                                <tr class="hover:bg-indigo-500/[0.02] dark:hover:bg-white/[0.02]">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold txt-primary">{{ $visit->visited_at?->format('d/m/Y') }}</div>
                                        <div class="text-[11px] txt-muted">{{ $visit->visited_at?->format('H:i:s') }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm font-bold txt-primary">{{ $visit->ip_address ?: '-' }}</div>
                                        <div class="text-xs txt-muted max-w-sm truncate">{{ $visit->method }} {{ $visit->path }}</div>
                                        @if($visit->referer)
                                            <div class="text-[11px] txt-muted max-w-sm truncate">Ref: {{ $visit->referer }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-xs txt-muted max-w-md line-clamp-2">{{ $visit->user_agent ?: '-' }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        @if($visit->is_suspicious)
                                            <span class="px-2.5 py-1 rounded-full bg-red-500/10 text-red-500 text-[10px] font-bold uppercase">{{ $visit->threat_type ?: 'Bahaya' }}</span>
                                            <p class="text-[11px] txt-muted mt-2 max-w-xs">{{ $visit->threat_reason }}</p>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase">Aman</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-16 text-center txt-muted">Belum ada data kunjungan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <x-ui.pagination :links="$visits" />
        </x-ui.card>

        <x-ui.card title="Log Berbahaya Terbaru">
            <div class="space-y-3">
                @forelse($dangerLogs as $log)
                    <div class="rounded-xl border border-red-500/10 bg-red-500/5 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-xs font-black text-red-500">{{ $log->threat_type }}</span>
                            <span class="text-[10px] txt-muted">{{ $log->visited_at?->format('d/m H:i') }}</span>
                        </div>
                        <p class="text-xs txt-primary font-semibold mt-1">{{ $log->ip_address }}</p>
                        <p class="text-[11px] txt-muted mt-1 break-all">{{ $log->path }}</p>
                        <p class="text-[11px] txt-muted mt-1">{{ $log->threat_reason }}</p>
                    </div>
                @empty
                    <p class="text-sm txt-muted">Belum ada log berbahaya.</p>
                @endforelse
            </div>
        </x-ui.card>
    </div>
</div>
