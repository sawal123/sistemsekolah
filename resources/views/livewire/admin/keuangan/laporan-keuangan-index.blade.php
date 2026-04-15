<div style="display:flex;flex-direction:column;gap:24px;height:100%;">
    <x-ui.toast />

    {{-- ── Header ──────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Laporan Keuangan</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Rekap penerimaan SPP, tunggakan siswa, dan efektivitas penagihan.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <span class="text-xs font-semibold txt-muted uppercase tracking-wider">Tahun:</span>
            <select wire:model.live="filterTahun"
                class="px-3 py-2 rounded-xl text-sm font-bold bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>

    {{-- ── Summary Cards ────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Hari Ini --}}
        <div class="rounded-2xl p-5 flex flex-col gap-2"
            style="background: linear-gradient(135deg, rgba(16,185,129,0.12), rgba(5,150,105,0.06)); border: 1px solid rgba(16,185,129,0.2);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Hari Ini</p>
                <div class="w-8 h-8 rounded-lg bg-emerald-500/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xl font-black text-emerald-400 leading-tight">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</p>
            <p class="text-xs txt-muted">{{ now()->format('d M Y') }}</p>
        </div>

        {{-- Bulan Ini --}}
        <div class="rounded-2xl p-5 flex flex-col gap-2"
            style="background: linear-gradient(135deg, rgba(99,102,241,0.12), rgba(79,70,229,0.06)); border: 1px solid rgba(99,102,241,0.2);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-indigo-400 uppercase tracking-wider">Bulan Ini</p>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xl font-black text-indigo-400 leading-tight">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</p>
            <p class="text-xs txt-muted">{{ now()->format('m') == 1 ? 'Januari' : (now()->format('m') == 2 ? 'Februari' : (now()->format('m') == 3 ? 'Maret' : (now()->format('m') == 4 ? 'April' : (now()->format('m') == 5 ? 'Mei' : (now()->format('m') == 6 ? 'Juni' : (now()->format('m') == 7 ? 'Juli' : (now()->format('m') == 8 ? 'Agustus' : (now()->format('m') == 9 ? 'September' : (now()->format('m') == 10 ? 'Oktober' : (now()->format('m') == 11 ? 'November' : 'Desember')))))))))) }} {{ now()->format('Y') }}</p>
        </div>

        {{-- Total Tahun --}}
        <div class="rounded-2xl p-5 flex flex-col gap-2"
            style="background: linear-gradient(135deg, rgba(139,92,246,0.12), rgba(109,40,217,0.06)); border: 1px solid rgba(139,92,246,0.2);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-purple-400 uppercase tracking-wider">Total {{ $filterTahun }}</p>
                <div class="w-8 h-8 rounded-lg bg-purple-500/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xl font-black text-purple-400 leading-tight">Rp {{ number_format($totalTahunIni, 0, ',', '.') }}</p>
            <p class="text-xs txt-muted">dari Rp {{ number_format($targetTagihan, 0, ',', '.') }} target</p>
        </div>

        {{-- Efektivitas --}}
        <div class="rounded-2xl p-5 flex flex-col gap-2"
            style="background: linear-gradient(135deg, rgba(245,158,11,0.12), rgba(217,119,6,0.06)); border: 1px solid rgba(245,158,11,0.2);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold text-amber-400 uppercase tracking-wider">Efektivitas</p>
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-black text-amber-400 leading-tight">{{ $efektivitas }}<span class="text-lg">%</span></p>
            {{-- Progress bar --}}
            <div class="w-full h-1.5 bg-amber-500/15 rounded-full overflow-hidden">
                <div class="h-full bg-amber-400 rounded-full transition-all duration-700"
                    style="width: {{ $efektivitas }}%;"></div>
            </div>
        </div>
    </div>

    {{-- ── Tab Switcher ──────────────────────────────────────── --}}
    <div class="flex items-center gap-1 p-1 rounded-xl w-fit"
        style="background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.12);">
        <button wire:click="setTab('laporan')" type="button"
            @class([
                'flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer',
                'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20' => $activeTab === 'laporan',
                'txt-muted hover:txt-primary' => $activeTab !== 'laporan',
            ])>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Riwayat Pembayaran
        </button>
        <button wire:click="setTab('tunggakan')" type="button"
            @class([
                'flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer',
                'bg-red-500 text-white shadow-lg shadow-red-500/20' => $activeTab === 'tunggakan',
                'txt-muted hover:txt-primary' => $activeTab !== 'tunggakan',
            ])>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Daftar Tunggakan
        </button>
    </div>

    {{-- ── Filter Bar ───────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
        @if($activeTab === 'laporan')
        <div class="flex items-center gap-2">
            <label class="text-xs txt-muted font-semibold whitespace-nowrap">Dari:</label>
            <input wire:model.live="filterDateMulai" type="date"
                class="px-3 py-2 rounded-xl text-sm bg-white/5 border border-indigo-500/20 txt-primary focus:outline-none focus:ring-2 focus:ring-indigo-500/30" />
        </div>
        <div class="flex items-center gap-2">
            <label class="text-xs txt-muted font-semibold whitespace-nowrap">Sampai:</label>
            <input wire:model.live="filterDateSelesai" type="date"
                class="px-3 py-2 rounded-xl text-sm bg-white/5 border border-indigo-500/20 txt-primary focus:outline-none focus:ring-2 focus:ring-indigo-500/30" />
        </div>
        @endif
        <select wire:model.live="filterJenjang"
            class="px-4 py-2 rounded-xl text-sm bg-white/5 border border-indigo-500/20 txt-primary focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            <option value="">Semua Jenjang</option>
            <option value="SMP">SMP</option>
            <option value="SMA">SMA</option>
        </select>
        <select wire:model.live="filterKelas"
            class="px-4 py-2 rounded-xl text-sm bg-white/5 border border-indigo-500/20 txt-primary focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            <option value="">Semua Kelas</option>
            @foreach($kelass as $kelas)
                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
            @endforeach
        </select>
    </div>

    {{-- ── Tab: Riwayat Pembayaran ───────────────────────────── --}}
    @if($activeTab === 'laporan')
    <x-ui.card padding="0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="border-collapse:separate;border-spacing:0;">
                <thead>
                    <tr class="bg-indigo-500/5 border-b border-indigo-500/10">
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Tanggal</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Siswa</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Kategori</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Bulan/Keterangan</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right">Jumlah</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Petugas</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-500/10">
                    @forelse($laporan as $item)
                    <tr class="hover:bg-indigo-500/[0.02] transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold txt-primary">{{ $item->tanggal_bayar->format('d M Y') }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold txt-primary">{{ $item->siswa?->user?->name ?? '-' }}</p>
                            <p class="text-xs txt-muted">{{ $item->siswa?->nisn }} · {{ $item->siswa?->kelas?->nama_kelas ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-bold px-2 py-1 rounded-lg bg-indigo-500/10 text-indigo-400">
                                {{ $item->spp?->kategori ?? '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-sm txt-muted">
                            {{ $item->nama_bulan }}
                            @if($item->tahun) <span class="text-xs">{{ $item->tahun }}</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <p class="text-sm font-bold text-emerald-400">
                                Rp {{ number_format($item->netto_bayar, 0, ',', '.') }}
                            </p>
                            @if($item->potongan > 0)
                                <p class="text-xs txt-muted line-through">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-sm txt-muted">
                            {{ $item->user?->name ?? 'Sistem' }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('admin.keuangan.kuitansi.cetak', ['ids' => $item->id]) }}"
                                target="_blank"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold
                                    bg-slate-500/10 txt-muted hover:bg-indigo-500/15 hover:text-indigo-400 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center opacity-40">
                                <svg class="w-10 h-10 txt-muted mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm txt-muted">Tidak ada data pembayaran pada periode ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-ui.pagination :links="$laporan" />
    </x-ui.card>
    @endif

    {{-- ── Tab: Daftar Tunggakan ─────────────────────────────── --}}
    @if($activeTab === 'tunggakan')
    @if($siswasBelumBayar->isNotEmpty())
    <div class="flex items-center justify-between px-1">
        <p class="text-sm txt-muted">
            <strong class="txt-primary">{{ $siswasBelumBayar->count() }}</strong> siswa dengan tunggakan SPP
        </p>
        <p class="text-sm font-bold text-red-400">
            Total: Rp {{ number_format($totalNominalTunggakan, 0, ',', '.') }}
        </p>
    </div>
    @endif
    <x-ui.card padding="0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left" style="border-collapse:separate;border-spacing:0;">
                <thead>
                    <tr class="bg-red-500/5 border-b border-red-500/10">
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted w-10">No</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted">Siswa</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-center">Bulan Tunggak</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-center w-24">Jumlah</th>
                        <th class="px-5 py-4 text-[11px] font-bold uppercase tracking-wider txt-muted text-right">Total Tunggakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-red-500/8">
                    @forelse($siswasBelumBayar as $idx => $row)
                    @php
                        $bulanLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
                    @endphp
                    <tr class="hover:bg-red-500/[0.02] transition-colors">
                        <td class="px-5 py-3.5 text-sm txt-muted">{{ $idx + 1 }}</td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-semibold txt-primary">{{ $row['siswa']->user?->name ?? '-' }}</p>
                            <p class="text-xs txt-muted">{{ $row['siswa']->nisn }} · {{ $row['siswa']->kelas?->nama_kelas ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex flex-wrap gap-1 justify-center">
                                @foreach($row['bulan_tunggakan'] as $bln)
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-red-500/15 text-red-400">
                                        {{ $bulanLabels[$bln - 1] }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="text-sm font-bold text-red-400 bg-red-500/10 px-2.5 py-1 rounded-lg">
                                {{ $row['jumlah_bulan'] }} bln
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <p class="text-sm font-bold text-red-400">
                                Rp {{ number_format($row['total_tunggakan'], 0, ',', '.') }}
                            </p>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center opacity-40">
                                <svg class="w-10 h-10 text-emerald-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm font-bold text-emerald-400">Tidak Ada Tunggakan! 🎉</p>
                                <p class="text-xs txt-muted mt-1">Semua siswa telah melunasi SPP bulan ini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    @endif
</div>
