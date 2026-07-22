<div style="display:flex;flex-direction:column;gap:20px;">
    <x-ui.toast />

    {{-- ── Listen for PDF tab open ──────────────────────────── --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-pdf-tab', (data) => {
                window.open(data[0].url, '_blank');
            });
        });
    </script>

    {{-- ── Header ──────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Transaksi Pembayaran</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Catat pembayaran SPP siswa dengan cepat dan akurat.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-semibold txt-muted uppercase tracking-wider whitespace-nowrap">Tahun Tagihan:</span>
            <div class="w-32">
                @php
                    $yearOptions = [];
                    for($y = now()->year + 1; $y >= now()->year - 3; $y--) {
                        $yearOptions[$y] = $y;
                    }
                @endphp
                <x-ui.select 
                    wire:model.live="selectedTahun"
                    :options="$yearOptions"
                />
            </div>
        </div>
    </div>

    {{-- ── Search Siswa ─────────────────────────────────────── --}}
    @if(!$selectedSiswaId)
    <x-ui.card class="overflow-visible" style="position: relative; z-index: 10;">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-lg bg-indigo-500/15 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold txt-primary">Cari Siswa</p>
                <p class="text-xs txt-muted">Ketik nama lengkap, NISN, atau NIS siswa</p>
            </div>
        </div>
        <div class="relative" style="z-index: 99;">
            <input wire:model.live.debounce.250ms="searchQuery"
                type="text"
                id="search-siswa-input"
                placeholder="Contoh: Ahmad Fauzi / 0012345678 ..."
                autocomplete="off"
                class="w-full px-4 py-3 rounded-xl text-sm bg-white/5 border border-indigo-500/20 txt-primary focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition-all"
            />
            <div wire:loading wire:target="searchQuery"
                class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="w-4 h-4 txt-muted animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>
            @if($showDropdown && count($this->hasilPencarian) > 0)
            <div class="absolute top-full left-0 right-0 glass bg-white dark:bg-slate-900 mt-2 rounded-xl overflow-hidden shadow-2xl"
                style="z-index: 9999;  border: 1px solid rgba(99,102,241,0.2);">
                @foreach($this->hasilPencarian as $siswa)
                <button wire:click="selectSiswa({{ $siswa['id'] }})" type="button"
                    class="w-full flex items-center gap-4 px-4 py-3 hover:bg-indigo-500/10 transition-colors text-left cursor-pointer border-b border-white/5 last:border-b-0">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br
                        {{ $siswa['jenjang'] === 'SMP' ? 'from-blue-500 to-blue-700' : 'from-purple-500 to-purple-700' }}
                        flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                        {{ strtoupper(substr($siswa['nama'], 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold txt-primary truncate">{{ $siswa['nama'] }}</p>
                        <p class="text-xs txt-muted truncate">{{ $siswa['nisn'] }} · {{ $siswa['kelas'] }}</p>
                    </div>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-md
                        {{ $siswa['jenjang'] === 'SMP' ? 'bg-blue-500/15 text-blue-400' : 'bg-purple-500/15 text-purple-400' }}">
                        {{ $siswa['jenjang'] }}
                    </span>
                </button>
                @endforeach
            </div>
            @elseif($showDropdown && strlen($searchQuery) >= 2)
            <div class="absolute top-full left-0 right-0 glass bg-white dark:bg-slate-900 mt-2 rounded-xl overflow-hidden shadow-2xl px-4 py-6 text-center"
                style="z-index: 9999;  border: 1px solid rgba(99,102,241,0.2);">
                <p class="text-sm txt-muted">Siswa tidak ditemukan untuk "<strong>{{ $searchQuery }}</strong>"</p>
            </div>
            @endif
        </div>
    </x-ui.card>
    @endif

    {{-- ── Student Info Card ─────────────────────────────────── --}}
    @if($selectedSiswaId && !empty($selectedSiswaData))
    <div class="flex items-center gap-4 px-5 py-4 rounded-2xl"
        style="background: linear-gradient(135deg, rgba(99,102,241,0.12), rgba(139,92,246,0.08)); border: 1px solid rgba(99,102,241,0.25);">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br
            {{ ($selectedSiswaData['jenjang'] ?? '') === 'SMP' ? 'from-blue-500 to-blue-700' : 'from-purple-500 to-purple-700' }}
            flex items-center justify-center text-white text-xl font-bold shadow-lg flex-shrink-0">
            {{ strtoupper(substr($selectedSiswaData['nama'] ?? 'S', 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <p class="text-base font-bold txt-primary">{{ $selectedSiswaData['nama'] }}</p>
                <span class="text-xs font-bold px-2 py-0.5 rounded-md
                    {{ ($selectedSiswaData['jenjang'] ?? '') === 'SMP' ? 'bg-blue-500/20 text-blue-400' : 'bg-purple-500/20 text-purple-400' }}">
                    {{ $selectedSiswaData['jenjang'] ?? '-' }}
                </span>
                <span class="text-xs bg-emerald-500/15 text-emerald-400 px-2 py-0.5 rounded-md font-semibold">
                    {{ $selectedSiswaData['status'] ?? 'Aktif' }}
                </span>
            </div>
            <p class="text-sm txt-muted mt-0.5">
                {{ $selectedSiswaData['nisn'] }} · {{ $selectedSiswaData['kelas'] }}
            </p>
        </div>
        <button wire:click="clearSiswa" type="button"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-red-400 hover:bg-red-500/10 transition-colors cursor-pointer flex-shrink-0">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Ganti Siswa
        </button>
    </div>

    {{-- ── ACTION PANEL (dipindah ke sini supaya selalu terlihat) ── --}}
    @if(!empty($sppMatrix))
    <div class="rounded-2xl overflow-hidden"
        style="background: linear-gradient(135deg, rgba(79,70,229,0.15), rgba(109,40,217,0.1)); border: 1px solid rgba(99,102,241,0.3);">
        <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Total Info --}}
            <div>
                <p class="text-xs font-bold txt-muted uppercase tracking-wider mb-1">Total Pembayaran</p>
                <p class="text-2xl font-black {{ $totalBayar > 0 ? 'text-indigo-400' : 'txt-muted' }} transition-all duration-300">
                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                </p>
                <p class="text-xs txt-muted mt-0.5">
                    @if(count($selectedItems) > 0)
                        {{ count($selectedItems) }} item dipilih
                    @else
                        Pilih bulan/tagihan di bawah
                    @endif
                </p>
            </div>
            {{-- Action Buttons --}}
            <div class="flex items-center gap-2.5 flex-wrap sm:flex-nowrap">
                {{-- Bayar Semua Tunggakan --}}
                <button wire:click="bayarSemuaTunggakan" type="button"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold
                        bg-amber-500/15 txt-primary border border-amber-500/30
                        hover:bg-amber-500/25 transition-all cursor-pointer whitespace-nowrap">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Pilih Semua Tunggakan
                </button>

                {{-- Proses Bayar --}}
                <button wire:click="prosesBayar" type="button"
                    wire:loading.attr="disabled"
                    @class([
                        'flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg transition-all cursor-pointer whitespace-nowrap',
                        'bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 hover:shadow-indigo-500/30' => count($selectedItems) > 0,
                        'bg-slate-500/10 txt-muted cursor-not-allowed border border-slate-500/20' => count($selectedItems) === 0,
                    ])
                    {{ count($selectedItems) === 0 ? 'disabled' : '' }}>
                    <span wire:loading.remove wire:target="prosesBayar" class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                        Proses Bayar
                    </span>
                    <span wire:loading wire:target="prosesBayar" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif
    @endif

    {{-- ── SPP Payment Matrix ────────────────────────────────── --}}
    @if($selectedSiswaId && !empty($sppMatrix))
        @foreach($sppMatrix as $sppId => $data)
        <x-ui.card>
            {{-- Header kartu per kategori --}}
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center
                        {{ $data['is_bulanan'] ? 'bg-indigo-500/15' : 'bg-amber-500/15' }}">
                        <span class="text-lg">{{ $data['is_bulanan'] ? '🗓️' : '💳' }}</span>
                    </div>
                    <div>
                        <p class="font-bold txt-primary text-sm">{{ $data['kategori'] }}</p>
                        <p class="text-xs txt-muted">Rp {{ number_format($data['nominal'], 0, ',', '.') }}
                            {{ $data['is_bulanan'] ? '/bulan' : '(sekali bayar)' }}
                        </p>
                    </div>
                </div>
                @if($data['is_bulanan'])
                @php $lunasCnt = collect($data['bulans'])->where('lunas', true)->count(); @endphp
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg
                    {{ $lunasCnt === 12 ? 'bg-emerald-500/15 text-emerald-400' : 'bg-slate-500/10 txt-muted' }}">
                    {{ $lunasCnt }}/12 Bulan
                </span>
                @endif
            </div>

            @if($data['is_bulanan'])
            {{-- ── Matriks 12 Bulan ── --}}
            @php $bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des']; @endphp
            <div class="space-y-3">
                {{-- Semester 1 --}}
                <div>
                    <p class="text-[10px] font-bold txt-muted uppercase tracking-wider mb-2">Semester 1 — Januari s/d Juni</p>
                    <div class="grid grid-cols-6 gap-2">
                        @for($b = 1; $b <= 6; $b++)
                        @php
                            $bulan   = $data['bulans'][$b];
                            $itemKey = $sppId . '_' . $b;
                            $isSelected  = isset($selectedItems[$itemKey]);
                            $isFuture    = $b > now()->month && $selectedTahun == now()->year;
                        @endphp
                        <button
                            wire:click="{{ !$bulan['lunas'] && !$isFuture ? 'toggleItem(' . $sppId . ', ' . $b . ')' : '' }}"
                            type="button"
                            @class([
                                'flex flex-col items-center justify-center py-3 rounded-xl border-2 transition-all duration-150 text-center select-none',
                                'bg-emerald-500/10 border-emerald-500/30 cursor-default' => $bulan['lunas'],
                                'bg-indigo-500/20 border-indigo-500 ring-2 ring-indigo-500/30 cursor-pointer scale-95' => !$bulan['lunas'] && $isSelected,
                                'bg-red-500/8 border-red-500/25 hover:bg-red-500/15 hover:scale-95 cursor-pointer' => !$bulan['lunas'] && !$isSelected && !$isFuture,
                                'bg-slate-500/5 border-slate-500/15 cursor-not-allowed opacity-50' => $isFuture,
                            ])>
                            <span @class([
                                'text-[11px] font-bold',
                                'text-emerald-400' => $bulan['lunas'],
                                'text-indigo-300' => !$bulan['lunas'] && $isSelected,
                                'text-red-400' => !$bulan['lunas'] && !$isSelected && !$isFuture,
                                'txt-muted' => $isFuture,
                            ])>{{ $bulanLabels[$b-1] }}</span>
                            @if($bulan['lunas'])
                                <svg class="w-3 h-3 text-emerald-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($isSelected)
                                <svg class="w-3 h-3 text-indigo-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @elseif(!$isFuture)
                                <span class="text-[9px] text-red-400 mt-0.5 font-semibold leading-none">TUNGGAK</span>
                            @else
                                <span class="text-[9px] txt-muted mt-0.5">—</span>
                            @endif
                        </button>
                        @endfor
                    </div>
                </div>

                {{-- Semester 2 --}}
                <div>
                    <p class="text-[10px] font-bold txt-muted uppercase tracking-wider mb-2">Semester 2 — Juli s/d Desember</p>
                    <div class="grid grid-cols-6 gap-2">
                        @for($b = 7; $b <= 12; $b++)
                        @php
                            $bulan   = $data['bulans'][$b];
                            $itemKey = $sppId . '_' . $b;
                            $isSelected  = isset($selectedItems[$itemKey]);
                            $isFuture    = $b > now()->month && $selectedTahun == now()->year;
                        @endphp
                        <button
                            wire:click="{{ !$bulan['lunas'] && !$isFuture ? 'toggleItem(' . $sppId . ', ' . $b . ')' : '' }}"
                            type="button"
                            @class([
                                'flex flex-col items-center justify-center py-3 rounded-xl border-2 transition-all duration-150 text-center select-none',
                                'bg-emerald-500/10 border-emerald-500/30 cursor-default' => $bulan['lunas'],
                                'bg-indigo-500/20 border-indigo-500 ring-2 ring-indigo-500/30 cursor-pointer scale-95' => !$bulan['lunas'] && $isSelected,
                                'bg-red-500/8 border-red-500/25 hover:bg-red-500/15 hover:scale-95 cursor-pointer' => !$bulan['lunas'] && !$isSelected && !$isFuture,
                                'bg-slate-500/5 border-slate-500/15 cursor-not-allowed opacity-50' => $isFuture,
                            ])>
                            <span @class([
                                'text-[11px] font-bold',
                                'text-emerald-400' => $bulan['lunas'],
                                'text-indigo-300' => !$bulan['lunas'] && $isSelected,
                                'text-red-400' => !$bulan['lunas'] && !$isSelected && !$isFuture,
                                'txt-muted' => $isFuture,
                            ])>{{ $bulanLabels[$b-1] }}</span>
                            @if($bulan['lunas'])
                                <svg class="w-3 h-3 text-emerald-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($isSelected)
                                <svg class="w-3 h-3 text-indigo-300 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                            @elseif(!$isFuture)
                                <span class="text-[9px] text-red-400 mt-0.5 font-semibold leading-none">TUNGGAK</span>
                            @else
                                <span class="text-[9px] txt-muted mt-0.5">—</span>
                            @endif
                        </button>
                        @endfor
                    </div>
                </div>
            </div>

            @else
            {{-- ── Tipe Sekali Bayar ── --}}
            @php
                $itemKey    = $sppId . '_sekali';
                $isSelected = isset($selectedItems[$itemKey]);
            @endphp
            <button
                wire:click="{{ !$data['lunas'] ? 'toggleItem(' . $sppId . ', \'sekali\')' : '' }}"
                type="button"
                @class([
                    'w-full flex items-center justify-between px-5 py-4 rounded-xl border-2 transition-all duration-150',
                    'bg-emerald-500/8 border-emerald-500/30 cursor-default' => $data['lunas'],
                    'bg-indigo-500/15 border-indigo-500 cursor-pointer' => !$data['lunas'] && $isSelected,
                    'bg-red-500/6 border-red-500/20 hover:bg-red-500/12 cursor-pointer' => !$data['lunas'] && !$isSelected,
                ])>
                <div class="flex items-center gap-3">
                    @if($data['lunas'])
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-emerald-400">Sudah Lunas</p>
                            <p class="text-xs txt-muted">Dibayar: {{ $data['tanggal'] ?? '-' }}</p>
                        </div>
                    @elseif($isSelected)
                        <div class="w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-indigo-300">Dipilih untuk dibayar — klik lagi untuk batal</p>
                    @else
                        <div class="w-8 h-8 rounded-full bg-red-500/15 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-red-400">Belum Dibayar — Klik untuk pilih</p>
                    @endif
                </div>
                <span class="text-sm font-bold
                    {{ $data['lunas'] ? 'text-emerald-400' : ($isSelected ? 'text-indigo-300' : 'text-red-400') }}">
                    Rp {{ number_format($data['nominal'], 0, ',', '.') }}
                </span>
            </button>
            @endif
        </x-ui.card>
        @endforeach

    @elseif($selectedSiswaId && empty($sppMatrix))
        <x-ui.card>
            <div class="flex flex-col items-center justify-center py-12 opacity-60">
                <svg class="w-12 h-12 txt-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-bold txt-primary">Tidak Ada Tarif SPP</p>
                <p class="text-xs txt-muted mt-1 text-center max-w-xs">
                    Belum ada tarif SPP aktif untuk jenjang {{ $selectedSiswaData['jenjang'] ?? '' }}.
                    Silakan tambahkan di menu <strong>Master Data SPP</strong> terlebih dahulu.
                </p>
            </div>
        </x-ui.card>

    @else
        <x-ui.card>
            <div class="flex flex-col items-center justify-center py-16 opacity-40">
                <svg class="w-16 h-16 txt-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-sm font-bold txt-primary">Cari Siswa Terlebih Dahulu</p>
                <p class="text-xs txt-muted mt-1">Gunakan kolom pencarian di atas untuk menemukan siswa.</p>
            </div>
        </x-ui.card>
    @endif



    {{-- ── Riwayat Transaksi Siswa ──────────────────────────── --}}
    @if($selectedSiswaId && $riwayat->isNotEmpty())
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-slate-500/10 flex items-center justify-center">
                    <svg class="w-4 h-4 txt-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold txt-primary">Riwayat Pembayaran</p>
                    <p class="text-xs txt-muted">{{ $riwayat->count() }} transaksi terakhir</p>
                </div>
            </div>
        </div>
        <div class="space-y-2">
            @php
                $bulanNamesRiwayat = [
                    1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
                    5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
                    9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
                ];
            @endphp
            @foreach($riwayat as $r)
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/[0.02] border border-white/[0.04] hover:bg-white/[0.04] transition-colors">
                {{-- Icon kategori --}}
                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold txt-primary truncate">
                        {{ $r->spp?->kategori ?? '-' }}
                        @if($r->bulan)
                            — {{ $bulanNamesRiwayat[$r->bulan] ?? '' }} {{ $r->tahun }}
                        @else
                            — Tahun {{ $r->tahun }}
                        @endif
                    </p>
                    <p class="text-xs txt-muted">
                        {{ $r->tanggal_bayar?->format('d/m/Y') }}
                    </p>
                </div>
                {{-- Nominal --}}
                <span class="text-sm font-bold text-emerald-400 flex-shrink-0">
                    Rp {{ number_format($r->netto_bayar, 0, ',', '.') }}
                </span>
                {{-- Cetak Kuitansi --}}
                <a href="{{ route('admin.keuangan.kuitansi.cetak', ['ids' => $r->id]) }}"
                    target="_blank"
                    class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold
                        txt-muted hover:text-indigo-400 hover:bg-indigo-500/10 transition-all flex-shrink-0"
                    title="Cetak Kuitansi">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak
                </a>
            </div>
            @endforeach
        </div>
    </x-ui.card>
    @endif

    {{-- ── Kuitansi Modal ────────────────────────────────────── --}}
    <x-ui.modal name="kuitansi-modal" :show="$showKuitansiModal" maxWidth="md">

        <div class="py-2 text-center">
            <div class="w-16 h-16 rounded-2xl bg-emerald-500/15 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold txt-primary mb-2">Pembayaran Berhasil!</h3>
            <p class="text-sm txt-muted mb-6">
                {{ count($lastPembayaranIds ?? []) }} tagihan telah berhasil dicatat.
                Apakah Anda ingin mencetak kuitansi pembayaran?
            </p>
            <div class="flex gap-3 justify-center">
                <x-ui.button wire:click="closeKuitansiModal" variant="secondary" type="button">
                    Lewati
                </x-ui.button>
                <x-ui.button wire:click="openKuitansi" variant="primary" type="button">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Kuitansi PDF
                </x-ui.button>
            </div>
        </div>
    </x-ui.modal>
</div>
