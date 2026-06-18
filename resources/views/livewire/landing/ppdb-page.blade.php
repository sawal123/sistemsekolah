<main class="bg-white">
    <x-landing.page-hero
        eyebrow="Penerimaan Peserta Didik Baru"
        title="Informasi PPDB"
        description="Jadwal gelombang, persyaratan, jalur pendaftaran, biaya sekolah, dan formulir pendaftaran awal."
    />

    <nav class="sticky top-20 z-30 border-b border-blue-100 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl gap-2 overflow-x-auto px-6 py-3 lg:px-8">
            @foreach([
                'informasi' => 'Jadwal',
                'syarat' => 'Persyaratan',
                'jalur' => 'Jalur Masuk',
                'biaya' => 'Biaya',
                'daftar' => 'Daftar Online',
            ] as $slug => $label)
                <a href="{{ $slug === 'informasi' ? route('landing.ppdb') . '#informasi' : route('landing.ppdb.section', $slug) . '#' . $slug }}" class="whitespace-nowrap rounded-full px-5 py-2 text-sm font-semibold {{ $section === $slug ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700' }}">{{ $label }}</a>
            @endforeach
        </div>
    </nav>

    <section id="informasi" class="scroll-mt-36 py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Gelombang Pendaftaran</p>
                    <h2 class="mt-2 font-display text-3xl font-bold text-blue-950">Jadwal PPDB dari admin</h2>
                </div>
                @if($openWave)
                    <span class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-600">Pendaftaran sedang dibuka</span>
                @else
                    <span class="w-fit rounded-full bg-amber-50 px-4 py-2 text-sm font-bold text-amber-600">Belum ada gelombang dibuka</span>
                @endif
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @forelse($waves as $wave)
                    <article class="rounded-3xl border p-6 {{ $wave->status === 'Dibuka' ? 'border-emerald-200 bg-emerald-50/50' : 'border-blue-100' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-blue-600">Tahun {{ $wave->tahun_pendaftaran }}</p>
                                <h3 class="mt-2 font-display text-xl font-bold text-blue-950">{{ $wave->nama_gelombang }}</h3>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-bold {{ $wave->status === 'Dibuka' ? 'bg-emerald-100 text-emerald-700' : ($wave->status === 'Ditutup' ? 'bg-slate-100 text-slate-600' : 'bg-amber-100 text-amber-700') }}">{{ $wave->status }}</span>
                        </div>
                        <p class="mt-4 text-sm text-slate-500">
                            {{ $wave->tanggal_mulai?->translatedFormat('d M Y') ?: 'Tanggal belum ditentukan' }}
                            –
                            {{ $wave->tanggal_selesai?->translatedFormat('d M Y') ?: 'Selesai menyesuaikan' }}
                        </p>
                        @if($wave->deskripsi)<p class="mt-4 text-sm leading-6 text-slate-600">{{ $wave->deskripsi }}</p>@endif
                    </article>
                @empty
                    <div class="col-span-full rounded-3xl border border-dashed border-blue-200 bg-blue-50 p-10 text-center text-slate-500">Gelombang PPDB belum dibuat pada halaman admin.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="syarat" class="scroll-mt-36 bg-blue-50 py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Persyaratan</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-blue-950">Dokumen yang perlu disiapkan</h2>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    'Ijazah atau Surat Keterangan Lulus',
                    'Kartu Keluarga',
                    'Akta Kelahiran',
                    'KTP Ayah dan Ibu',
                    'Rapor semester 1–5',
                    'Pas foto terbaru',
                    'NISN dan NIK calon siswa',
                    'Nomor telepon orang tua aktif',
                ] as $requirement)
                    <div class="flex gap-3 rounded-2xl bg-white p-5">
                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">{{ $loop->iteration }}</span>
                        <p class="text-sm font-semibold leading-6 text-slate-700">{{ $requirement }}</p>
                    </div>
                @endforeach
            </div>
            <p class="mt-6 text-sm text-slate-500">Daftar dokumen mengikuti kolom berkas pada modul Pendaftaran Murid Baru di admin.</p>
        </div>
    </section>

    <section id="jalur" class="scroll-mt-36 py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Jalur Masuk</p>
            <h2 class="mt-2 font-display text-3xl font-bold text-blue-950">Tahapan pendaftaran</h2>
            <div class="mt-8 grid gap-5 md:grid-cols-4">
                @foreach([
                    ['Isi formulir awal', 'Masukkan data calon siswa dan orang tua melalui formulir online.'],
                    ['Verifikasi data', 'Panitia memeriksa data awal dan menghubungi calon peserta.'],
                    ['Lengkapi dokumen', 'Berkas persyaratan dilengkapi sesuai arahan panitia.'],
                    ['Pengumuman hasil', 'Status pendaftaran diperbarui menjadi diperiksa, lengkap, diterima, atau ditolak.'],
                ] as [$title, $description])
                    <article class="rounded-3xl border border-blue-100 p-6">
                        <span class="font-display text-4xl font-extrabold text-blue-100">0{{ $loop->iteration }}</span>
                        <h3 class="mt-4 font-display text-lg font-bold text-blue-950">{{ $title }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-500">{{ $description }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="biaya" class="scroll-mt-36 bg-blue-950 py-16 text-white">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-200">Biaya Sekolah</p>
            <h2 class="mt-2 font-display text-3xl font-bold">Rincian biaya aktif</h2>
            <p class="mt-3 max-w-2xl text-blue-200">Nominal dikelola admin per tahun ajaran, jenjang, dan jurusan SMK.</p>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($fees as $fee)
                    <article class="rounded-2xl border border-white/10 bg-white/10 p-6">
                        <p class="text-sm font-semibold text-blue-200">{{ $fee->kategori }}</p>
                        <p class="mt-3 font-display text-2xl font-bold">Rp {{ number_format($fee->nominal, 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs uppercase tracking-wide text-blue-300">{{ $fee->jenjang }} · {{ $fee->tahunAjaran?->tahun }}</p>
                        @if($fee->jurusan)
                            <p class="mt-2 text-sm font-semibold text-cyan-200">{{ $fee->jurusan->kode }} · {{ $fee->jurusan->nama }}</p>
                        @endif
                        @if($fee->keterangan)<p class="mt-4 text-sm leading-6 text-blue-100">{{ $fee->keterangan }}</p>@endif
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-white/20 p-8 text-blue-200">Biaya aktif belum ditetapkan oleh admin.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="daftar" class="scroll-mt-36 py-20">
        <div class="mx-auto grid max-w-7xl gap-10 px-6 lg:grid-cols-[0.72fr_1.28fr] lg:px-8">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-600">Daftar Online</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-blue-950">Formulir pendaftaran awal</h2>
                <p class="mt-5 leading-7 text-slate-600">Data yang dikirim akan langsung muncul pada modul Pendaftaran Murid Baru di admin dengan status <strong>Baru</strong>.</p>
                <div class="mt-7 rounded-2xl bg-blue-50 p-5 text-sm leading-6 text-slate-600">
                    Form ini belum meminta unggahan berkas. Panitia akan menghubungi calon siswa untuk proses verifikasi dan kelengkapan dokumen.
                </div>
            </div>

            <form wire:submit="register" class="rounded-3xl border border-blue-100 p-7 shadow-sm">
                @if(session('registration_success'))
                    <div class="mb-6 rounded-2xl bg-emerald-50 p-5 text-sm font-semibold text-emerald-700">{{ session('registration_success') }}</div>
                @endif
                @error('registration')<div class="mb-6 rounded-2xl bg-red-50 p-5 text-sm font-semibold text-red-600">{{ $message }}</div>@enderror

                <input wire:model="website" type="text" class="hidden" tabindex="-1" autocomplete="off">
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="block">
                        <span class="mb-2 block text-sm font-semibold text-slate-700">Jenjang tujuan *</span>
                        <select wire:model.live="jenjang_pilihan" class="w-full rounded-xl border border-blue-100 px-4 py-3 outline-none focus:border-blue-400">
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA</option>
                            <option value="SMK">SMK</option>
                        </select>
                        @error('jenjang_pilihan')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                    </label>

                    @if($jenjang_pilihan === 'SMK')
                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-700">Jurusan SMK *</span>
                            <select wire:model="jurusan_id" class="w-full rounded-xl border border-blue-100 px-4 py-3 outline-none focus:border-blue-400">
                                <option value="">Pilih jurusan</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->kode }} - {{ $jurusan->nama }}</option>
                                @endforeach
                            </select>
                            @error('jurusan_id')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                        </label>
                    @else
                        <label class="block">
                            <span class="mb-2 block text-sm font-semibold text-slate-700">Program/peminatan *</span>
                            <input wire:model="jurusan_pilihan_1" type="text" placeholder="Contoh: IPA, IPS, atau program umum"
                                class="w-full rounded-xl border border-blue-100 px-4 py-3 outline-none focus:border-blue-400">
                            @error('jurusan_pilihan_1')<span class="mt-1 block text-xs text-red-500">{{ $message }}</span>@enderror
                        </label>
                    @endif

                    @foreach([
                        ['nama_lengkap', 'Nama lengkap *', 'text'],
                        ['nisn', 'NISN *', 'text'],
                        ['nik', 'NIK', 'text'],
                        ['tempat_lahir', 'Tempat lahir', 'text'],
                        ['tanggal_lahir', 'Tanggal lahir *', 'date'],
                        ['no_hp', 'Nomor HP calon siswa *', 'text'],
                        ['email', 'Email', 'email'],
                        ['sekolah_asal', 'Sekolah asal *', 'text'],
                        ['tahun_lulus', 'Tahun lulus', 'number'],
                        ['nama_ayah', 'Nama ayah', 'text'],
                        ['nama_ibu', 'Nama ibu', 'text'],
                        ['no_telp_ortu', 'Nomor telepon orang tua *', 'text'],
                    ] as [$field, $label, $type])
                        <div class="{{ in_array($field, ['nama_lengkap', 'alamat'], true) ? 'md:col-span-2' : '' }}">
                            <label for="{{ $field }}" class="mb-2 block text-sm font-semibold text-slate-700">{{ $label }}</label>
                            <input wire:model="{{ $field }}" id="{{ $field }}" type="{{ $type }}" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-50">
                            @error($field)<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                    <div>
                        <label for="jenis_kelamin" class="mb-2 block text-sm font-semibold text-slate-700">Jenis kelamin *</label>
                        <select wire:model="jenis_kelamin" id="jenis_kelamin" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none focus:border-blue-400">
                            <option value="">Pilih</option><option>Laki-Laki</option><option>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="alamat" class="mb-2 block text-sm font-semibold text-slate-700">Alamat lengkap *</label>
                        <textarea wire:model="alamat" id="alamat" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-50"></textarea>
                        @error('alamat')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
                <button type="submit" @disabled(! $openWave) class="mt-6 w-full rounded-xl bg-blue-600 px-6 py-3.5 font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300">
                    {{ $openWave ? 'Kirim Pendaftaran Awal' : 'Pendaftaran Belum Dibuka' }}
                </button>
            </form>
        </div>
    </section>
</main>
