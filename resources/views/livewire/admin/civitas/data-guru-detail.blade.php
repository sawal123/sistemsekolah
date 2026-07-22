<div style="display:flex;flex-direction:column;gap:20px;height:100%;">
    <div class="fu d1" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1 class="txt-primary" style="font-size:22px;font-weight:800;">Profil Guru</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:3px;">Informasi detail, akademik, dan jadwal mengajar.</p>
        </div>
        <a href="{{ route('admin.civitas.data-guru') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2 font-semibold text-sm rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 glass txt-secondary hover:bg-white/40 dark:hover:bg-white/10 focus:ring-gray-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    {{-- Info Card --}}
    <div class="fu d2 glass-card" style="padding:24px;">
        <div style="display:flex;flex-wrap:wrap;gap:24px;align-items:flex-start;">
            <div style="flex-shrink:0;">
                @if($guru->foto)
                    <img src="{{ asset('storage/'.$guru->foto) }}" alt="Foto Profile" style="width:120px;height:120px;object-fit:cover;border-radius:16px;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
                @else
                    <div style="width:120px;height:120px;border-radius:16px;background:rgba(99,102,241,0.1);color:#6366f1;display:flex;align-items:center;justify-content:center;font-size:40px;font-weight:800;">
                        {{ strtoupper(substr($guru->user->name ?? 'G', 0, 1)) }}
                    </div>
                @endif
            </div>

            <div style="flex:1;min-width:260px;">
                <h2 class="txt-primary" style="font-size:24px;font-weight:800;margin-bottom:4px;">{{ $guru->user->name ?? 'Tanpa Nama' }}</h2>
                <div style="display:flex;gap:10px;align-items:center;margin-bottom:16px;">
                    <span class="txt-secondary" style="font-size:14px;font-weight:500;">NIP: {{ $guru->nip }}</span>
                    <span style="color:rgba(156,163,175,0.5);">•</span>
                    <span class="badge" style="background:rgba(139,92,246,0.12);color:#7c3aed;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">{{ $guru->jabatan ?: 'Guru' }}</span>
                    @if($guru->kelas)
                    <span class="badge" style="background:rgba(16,185,129,0.12);color:#059669;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">Wali Kelas {{ $guru->kelas->nama_kelas }}</span>
                    @endif
                </div>

                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:16px;margin-top:20px;">
                    <div>
                        <p class="txt-muted" style="font-size:11px;text-transform:uppercase;font-weight:700;letter-spacing:0.05em;">Tempat, Tanggal Lahir</p>
                        <p class="txt-primary" style="font-size:14px;font-weight:500;margin-top:2px;">{{ $guru->tempat_lahir ?: '-' }}, {{ $guru->tanggal_lahir ? \Carbon\Carbon::parse($guru->tanggal_lahir)->translatedFormat('d F Y') : '-' }}</p>
                    </div>
                    <div>
                        <p class="txt-muted" style="font-size:11px;text-transform:uppercase;font-weight:700;letter-spacing:0.05em;">Agama</p>
                        <p class="txt-primary" style="font-size:14px;font-weight:500;margin-top:2px;">{{ $guru->agama ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="txt-muted" style="font-size:11px;text-transform:uppercase;font-weight:700;letter-spacing:0.05em;">No. Telepon</p>
                        <p class="txt-primary" style="font-size:14px;font-weight:500;margin-top:2px;">{{ $guru->no_telp ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="txt-muted" style="font-size:11px;text-transform:uppercase;font-weight:700;letter-spacing:0.05em;">Email</p>
                        <p class="txt-primary" style="font-size:14px;font-weight:500;margin-top:2px;">{{ $guru->user->email ?? '-' }}</p>
                    </div>
                    <div style="grid-column:1 / -1;">
                        <p class="txt-muted" style="font-size:11px;text-transform:uppercase;font-weight:700;letter-spacing:0.05em;">Alamat Lengkap</p>
                        <p class="txt-primary" style="font-size:14px;font-weight:500;margin-top:2px;">{{ $guru->alamat ?: '-' }}</p>
                    </div>
                </div>
                
                <div style="margin-top:24px;padding-top:16px;border-top:1px solid rgba(99,102,241,0.1);">
                    <a href="{{ route('admin.civitas.data-guru.edit', $guru->id) }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2 font-semibold text-sm rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 glass txt-secondary hover:bg-white/40 dark:hover:bg-white/10 focus:ring-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Jadwal Mengajar --}}
    <div class="fu d3 glass-card" style="padding:24px;">
        <h3 class="txt-primary" style="font-size:16px;font-weight:700;margin-bottom:16px;">Jadwal Mengajar Aktif</h3>
        <div style="overflow-x:auto;">
            <table class="tbl" style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th class="txt-muted" style="text-align:left;">No</th>
                        <th class="txt-muted" style="text-align:left;">Mata Pelajaran</th>
                        <th class="txt-muted" style="text-align:left;">Kelas</th>
                        <th class="txt-muted" style="text-align:left;">Hari</th>
                        <th class="txt-muted" style="text-align:left;">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guru->jadwals as $index => $jadwal)
                    <tr>
                        <td class="txt-secondary" style="font-size:13px;">{{ $index + 1 }}</td>
                        <td class="txt-primary" style="font-size:13px;font-weight:600;">{{ $jadwal->mapel->nama_mapel ?? '-' }}</td>
                        <td class="txt-secondary" style="font-size:13px;">
                            <span class="badge" style="background:rgba(59,130,246,0.1);color:#3b82f6;">{{ $jadwal->kelas->nama_kelas ?? '-' }}</span>
                        </td>
                        <td class="txt-secondary" style="font-size:13px;">{{ $jadwal->hari ?? '-' }}</td>
                        <td class="txt-secondary" style="font-size:13px;">{{ $jadwal->jam_mulai ?? '-' }} - {{ $jadwal->jam_selesai ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="txt-muted" style="text-align:center;padding:20px;">Belum ada jadwal mengajar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
