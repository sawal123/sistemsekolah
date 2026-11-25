<div class="space-y-6">
    <x-ui.toast />

    {{-- ── Header ──────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-extrabold txt-primary">Verifikasi Pembayaran</h1>
            <p class="mt-1 text-sm txt-muted">Verifikasi bukti transfer dari portal siswa. Persetujuan akan otomatis mencatat transaksi pada ledger tagihan.</p>
        </div>
        <div>
            <a href="{{ route('admin.keuangan.transaksi-pembayaran') }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-indigo-400 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Buka Kasir Loket
            </a>
        </div>
    </div>

    {{-- ── 1. Statistik Ringkas Pengajuan ────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Menunggu Verifikasi --}}
        <div class="p-5 rounded-2xl bg-white/5 border border-indigo-500/10 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/15 flex items-center justify-center flex-shrink-0 text-amber-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold txt-muted uppercase tracking-wider">Menunggu Verifikasi</p>
                <p class="text-2xl font-black text-amber-400 mt-1">
                    {{ $stats['pending_count'] }} <span class="text-xs font-normal txt-muted">pengajuan</span>
                </p>
                <p class="text-[11px] txt-muted mt-0.5">
                    Rp {{ number_format($stats['pending_nominal'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Disetujui --}}
        <div class="p-5 rounded-2xl bg-white/5 border border-indigo-500/10 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/15 flex items-center justify-center flex-shrink-0 text-emerald-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold txt-muted uppercase tracking-wider">Telah Disetujui</p>
                <p class="text-2xl font-black text-emerald-400 mt-1">
                    {{ $stats['approved_count'] }} <span class="text-xs font-normal txt-muted">transaksi</span>
                </p>
                <p class="text-[11px] txt-muted mt-0.5">Tercatat di ledger tagihan</p>
            </div>
        </div>

        {{-- Ditolak --}}
        <div class="p-5 rounded-2xl bg-white/5 border border-indigo-500/10 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-500/15 flex items-center justify-center flex-shrink-0 text-rose-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold txt-muted uppercase tracking-wider">Ditolak</p>
                <p class="text-2xl font-black text-rose-400 mt-1">
                    {{ $stats['rejected_count'] }} <span class="text-xs font-normal txt-muted">pengajuan</span>
                </p>
                <p class="text-[11px] txt-muted mt-0.5">Bukti transfer tidak valid</p>
            </div>
        </div>

        {{-- Total Pengajuan --}}
        <div class="p-5 rounded-2xl bg-white/5 border border-indigo-500/10 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/15 flex items-center justify-center flex-shrink-0 text-indigo-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-semibold txt-muted uppercase tracking-wider">Total Riwayat</p>
                <p class="text-2xl font-black txt-primary mt-1">
                    {{ $stats['all_count'] }} <span class="text-xs font-normal txt-muted">pengajuan</span>
                </p>
                <p class="text-[11px] txt-muted mt-0.5">Semua status pengajuan</p>
            </div>
        </div>
    </div>

    {{-- ── 2. Filter Tabs Navigation ─────────────────────────── --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-1">
        <button wire:click="setFilter('Menunggu Verifikasi')" type="button"
            class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer flex items-center gap-2
            {{ $filter === 'Menunggu Verifikasi' ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/25' : 'bg-white/5 txt-muted hover:text-white hover:bg-white/10' }}">
            <span>Menunggu Verifikasi</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $filter === 'Menunggu Verifikasi' ? 'bg-white/20 text-white' : 'bg-amber-500/20 text-amber-400' }}">
                {{ $stats['pending_count'] }}
            </span>
        </button>

        <button wire:click="setFilter('Disetujui')" type="button"
            class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer flex items-center gap-2
            {{ $filter === 'Disetujui' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/25' : 'bg-white/5 txt-muted hover:text-white hover:bg-white/10' }}">
            <span>Disetujui</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $filter === 'Disetujui' ? 'bg-white/20 text-white' : 'bg-emerald-500/20 text-emerald-400' }}">
                {{ $stats['approved_count'] }}
            </span>
        </button>

        <button wire:click="setFilter('Ditolak')" type="button"
            class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer flex items-center gap-2
            {{ $filter === 'Ditolak' ? 'bg-rose-600 text-white shadow-lg shadow-rose-500/25' : 'bg-white/5 txt-muted hover:text-white hover:bg-white/10' }}">
            <span>Ditolak</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $filter === 'Ditolak' ? 'bg-white/20 text-white' : 'bg-rose-500/20 text-rose-400' }}">
                {{ $stats['rejected_count'] }}
            </span>
        </button>

        <button wire:click="setFilter('Semua')" type="button"
            class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer flex items-center gap-2
            {{ $filter === 'Semua' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'bg-white/5 txt-muted hover:text-white hover:bg-white/10' }}">
            <span>Semua Riwayat</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] {{ $filter === 'Semua' ? 'bg-white/20 text-white' : 'bg-indigo-500/20 text-indigo-400' }}">
                {{ $stats['all_count'] }}
            </span>
        </button>
    </div>

    {{-- ── 3. Daftar Pengajuan ────────────────────────────────── --}}
    <x-ui.card padding="0" class="overflow-hidden">
        <div class="divide-y divide-indigo-500/10">
            @forelse($submissions as $submission)
                <article class="p-5 hover:bg-white/[0.02] transition-colors">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                        {{-- Avatar Inisial --}}
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-base font-bold flex-shrink-0 shadow-sm">
                            {{ strtoupper(substr($submission->siswa?->user?->name ?? 'S', 0, 1)) }}
                        </div>

                        {{-- Info Pengajuan --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-bold txt-primary text-base">{{ $submission->siswa?->user?->name ?? 'Siswa' }}</h2>
                                <span class="text-xs px-2 py-0.5 rounded-md font-semibold bg-indigo-500/15 text-indigo-400">
                                    {{ $submission->siswa?->kelas?->nama_kelas ?? '-' }} · {{ $submission->siswa?->jenjang }}
                                </span>
                                <x-ui.badge :variant="$submission->status === 'Disetujui' ? 'success' : ($submission->status === 'Ditolak' ? 'danger' : 'warning')">
                                    {{ $submission->status }}
                                </x-ui.badge>
                            </div>

                            <p class="mt-1 text-sm font-semibold txt-secondary">
                                {{ $submission->tagihan?->jenis_biaya ?? 'SPP' }}
                                <span class="text-emerald-400 font-bold ml-1">· Rp {{ number_format($submission->nominal, 0, ',', '.') }}</span>
                                <span class="txt-muted font-normal">· {{ $submission->metode }}</span>
                            </p>

                            <p class="mt-1 text-xs txt-muted flex items-center gap-3 flex-wrap">
                                <span>📅 Tgl Transfer: <strong>{{ $submission->tanggal_transfer?->format('d/m/Y') }}</strong></span>
                                <span>⏱️ Dikirim: {{ $submission->created_at?->diffForHumans() }}</span>
                                @if($submission->reviewer)
                                    <span>👤 Diverifikasi oleh: <strong>{{ $submission->reviewer->name }}</strong></span>
                                @endif
                            </p>

                            @if($submission->catatan_siswa)
                                <div class="mt-2.5 px-3 py-2 rounded-xl bg-white/[0.03] border border-white/5 text-xs txt-secondary">
                                    <span class="font-semibold text-indigo-400">Catatan Siswa:</span> {{ $submission->catatan_siswa }}
                                </div>
                            @endif

                            @if($submission->catatan_admin)
                                <div class="mt-2 px-3 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-xs text-rose-300">
                                    <span class="font-semibold text-rose-400">Catatan Admin:</span> {{ $submission->catatan_admin }}
                                </div>
                            @endif
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center gap-2 flex-wrap flex-shrink-0">
                            <a href="{{ route('admin.payment-submissions.proof', $submission) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-indigo-500/20 text-xs font-bold text-indigo-400 hover:bg-indigo-500/10 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Lihat Bukti
                            </a>

                            @if($submission->status === \App\Models\PaymentSubmission::PENDING)
                                <x-ui.button wire:click="approve({{ $submission->id }})" wire:confirm="Setujui pembayaran ini dan catat ke ledger tagihan?" variant="primary">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Setujui
                                </x-ui.button>
                                <x-ui.button wire:click="openReject({{ $submission->id }})" variant="danger">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Tolak
                                </x-ui.button>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                {{-- ── EMPTY STATE CERDAS ──────────────────────────── --}}
                <div class="py-16 px-6 text-center">
                    @if($filter === 'Menunggu Verifikasi')
                        <div class="w-16 h-16 rounded-2xl bg-emerald-500/15 flex items-center justify-center mx-auto mb-4 text-emerald-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold txt-primary">Semua Pengajuan Telah Diverifikasi! 🎉</h3>
                        <p class="text-xs txt-muted mt-1 max-w-md mx-auto">
                            Tidak ada pengajuan transfer pembayaran yang sedang menunggu antrean verifikasi saat ini.
                            Siswa dapat mengunggah bukti transfer melalui Portal Siswa (Menu Pembayaran SPP).
                        </p>
                        <div class="mt-5 flex items-center justify-center gap-3">
                            <button wire:click="setFilter('Semua')" type="button"
                                class="px-4 py-2 rounded-xl text-xs font-semibold bg-white/5 txt-primary hover:bg-white/10 border border-indigo-500/20 transition-all cursor-pointer">
                                Lihat Semua Riwayat ({{ $stats['all_count'] }})
                            </button>
                            <a href="{{ route('admin.keuangan.transaksi-pembayaran') }}" wire:navigate
                                class="px-4 py-2 rounded-xl text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm transition-all">
                                Buka Transaksi Kasir
                            </a>
                        </div>
                    @elseif($filter === 'Disetujui')
                        <div class="w-12 h-12 rounded-xl bg-slate-500/10 flex items-center justify-center mx-auto mb-3 txt-muted">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold txt-primary">Belum ada pengajuan yang disetujui</p>
                        <p class="text-xs txt-muted mt-0.5">Pengajuan yang Anda setujui akan tercatat di sini.</p>
                    @elseif($filter === 'Ditolak')
                        <div class="w-12 h-12 rounded-xl bg-slate-500/10 flex items-center justify-center mx-auto mb-3 txt-muted">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold txt-primary">Tidak ada pengajuan yang ditolak</p>
                        <p class="text-xs txt-muted mt-0.5">Semua pengajuan yang pernah ditolak akan muncul di sini.</p>
                    @else
                        <div class="w-16 h-16 rounded-2xl bg-indigo-500/15 flex items-center justify-center mx-auto mb-4 text-indigo-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-base font-bold txt-primary">Belum Ada Pengajuan Pembayaran</h3>
                        <p class="text-xs txt-muted mt-1 max-w-md mx-auto">
                            Pengajuan akan muncul otomatis di halaman ini ketika siswa mengunggah bukti transfer melalui <strong>Portal Siswa</strong> pada menu Pembayaran SPP.
                        </p>
                    @endif
                </div>
            @endforelse
        </div>

        @if($submissions->hasPages())
            <div class="p-4 border-t border-indigo-500/10">
                {{ $submissions->links() }}
            </div>
        @endif
    </x-ui.card>

    {{-- ── 4. Modal Tolak Pengajuan ───────────────────────────── --}}
    <x-ui.modal name="reject-payment-submission" maxWidth="md">
        <div class="p-2">
            <div class="w-12 h-12 rounded-xl bg-rose-500/15 flex items-center justify-center mb-4 text-rose-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h2 class="text-lg font-bold txt-primary">Tolak Pengajuan Pembayaran</h2>
            <p class="text-xs txt-muted mt-1">Berikan alasan penolakan secara jelas agar siswa dapat memperbaiki atau mengunggah ulang bukti transfer.</p>

            <form wire:submit="reject" class="mt-4 space-y-4">
                <div>
                    <textarea wire:model="adminNote" rows="4"
                        class="w-full rounded-xl border border-indigo-500/20 bg-white/5 px-3 py-2.5 text-sm txt-primary focus:outline-none focus:ring-2 focus:ring-rose-500/30"
                        placeholder="Contoh: Foto bukti transfer buram, mohon unggah struk yang jelas dan memuat nomor referensi..."></textarea>
                    @error('adminNote')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <x-ui.button wire:click="closeReject" type="button" variant="secondary">Batal</x-ui.button>
                    <x-ui.button type="submit" variant="danger">Tolak Pengajuan</x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>
</div>
