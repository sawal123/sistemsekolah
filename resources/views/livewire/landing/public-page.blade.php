<main class="bg-white">
    <x-landing.page-hero eyebrow="Informasi Sekolah" :title="$heading" :description="$description" />

    <section class="py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            @if(in_array($page, ['pengumuman', 'prestasi']))
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($posts as $post)
                        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            @if($post->thumbnail)
                                <img src="{{ asset('storage/'.$post->thumbnail) }}" alt="{{ $post->judul }}" class="aspect-video w-full object-cover" loading="lazy">
                            @endif
                            <div class="p-6">
                                <p class="text-xs font-semibold text-blue-600">{{ $post->kategori?->nama_kategori }} · {{ $post->created_at->translatedFormat('d M Y') }}</p>
                                <h2 class="mt-3 font-heading text-xl font-bold"><a href="{{ route('landing.blog.show', $post->slug) }}" class="hover:text-blue-600">{{ $post->judul }}</a></h2>
                                <p class="mt-3 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags($post->konten), 150) }}</p>
                                <a href="{{ route('landing.blog.show', $post->slug) }}" class="mt-5 inline-flex text-sm font-semibold text-blue-600">Baca selengkapnya →</a>
                            </div>
                        </article>
                    @empty
                        <p class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-slate-600">{{ $page === 'pengumuman' ? 'Belum ada pengumuman resmi yang dipublikasikan.' : 'Belum ada berita prestasi yang dipublikasikan.' }}</p>
                    @endforelse
                </div>
                <div class="mt-8">{{ $posts->links() }}</div>
            @elseif($page === 'kalender-akademik')
                <p class="mb-6 text-sm text-slate-600">Agenda mulai {{ today()->startOfMonth()->translatedFormat('F Y') }}. Hari Minggu merupakan hari libur rutin.</p>
                <div class="space-y-4">
                    @forelse($events as $event)
                        <article class="flex flex-col gap-4 rounded-2xl border border-slate-200 p-6 sm:flex-row sm:items-center">
                            <time datetime="{{ $event->tanggal->toDateString() }}" class="shrink-0 rounded-xl bg-blue-50 px-5 py-3 font-semibold text-blue-700">{{ $event->tanggal->translatedFormat('d F Y') }}</time>
                            <div class="flex-1"><h2 class="font-heading text-lg font-bold">{{ $event->keterangan }}</h2></div>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $event->is_libur ? 'bg-amber-50 text-amber-800' : 'bg-emerald-50 text-emerald-700' }}">{{ $event->is_libur ? 'Hari libur' : 'Kegiatan sekolah' }}</span>
                        </article>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-slate-600">Belum ada agenda atau hari libur tambahan yang dijadwalkan.</p>
                    @endforelse
                </div>
                <div class="mt-8">{{ $events->links() }}</div>
            @elseif($page === 'galeri')
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse($photos as $photo)
                        <figure class="overflow-hidden rounded-2xl border border-slate-200">
                            @if($photo->foto)
                                <button type="button" data-lightbox-src="{{ asset('storage/'.$photo->foto) }}" data-lightbox-title="{{ $photo->judul }}" data-lightbox-category="Galeri Kegiatan" data-lightbox-desc="{{ $photo->deskripsi }}" class="block w-full" aria-label="Perbesar foto {{ $photo->judul }}">
                                    <img src="{{ asset('storage/'.$photo->foto) }}" alt="{{ $photo->judul }}" class="aspect-[4/3] w-full object-cover" loading="lazy">
                                </button>
                            @else
                                <div class="flex aspect-[4/3] items-center justify-center bg-blue-50 text-slate-500">Foto belum tersedia</div>
                            @endif
                            <figcaption class="p-5"><h2 class="font-heading text-lg font-bold">{{ $photo->judul }}</h2><p class="mt-2 text-sm text-slate-600">{{ $photo->deskripsi }}</p></figcaption>
                        </figure>
                    @empty
                        <p class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-slate-600">Dokumentasi kegiatan belum tersedia.</p>
                    @endforelse
                </div>
                <div class="mt-8">{{ $photos->links() }}</div>
            @elseif($page === 'alumni')
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse($alumni as $item)
                        <article class="rounded-2xl border border-slate-200 p-6">
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">{{ str_replace('_', ' ', $item->jenis_kegiatan) }}</span>
                            <h2 class="mt-4 font-heading text-xl font-bold">{{ $item->siswa?->user?->name }}</h2>
                            <p class="mt-2 font-medium text-slate-700">{{ $item->nama_instansi }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $item->posisi_jurusan }} · Sejak {{ $item->tahun_mulai }}</p>
                            <p class="mt-4 text-sm leading-6 text-slate-600">{{ $item->deskripsi }}</p>
                        </article>
                    @empty
                        <p class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center text-slate-600">Kisah alumni belum tersedia untuk ditampilkan.</p>
                    @endforelse
                </div>
                <div class="mt-8">{{ $alumni->links() }}</div>
            @elseif($page === 'peta-situs')
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach([
                        'Profil Sekolah' => [['Beranda', route('home')], ['Tentang & Sejarah', route('landing.about')], ['Guru', route('landing.staffing', 'guru')], ['Tata Usaha', route('landing.staffing', 'tata-usaha')], ['Struktur Organisasi', route('landing.staffing', 'struktur-organisasi')], ['Fasilitas', route('landing.facilities')]],
                        'Akademik & Kesiswaan' => [['Kurikulum', route('landing.academic', 'kurikulum')], ['Program IPA', route('landing.academic', 'ipa')], ['Program IPS', route('landing.academic', 'ips')], ['Program Bahasa', route('landing.academic', 'bahasa')], ['Kalender Akademik', route('landing.kalender-akademik')], ['Prestasi', route('landing.prestasi')], ['Galeri', route('landing.galeri')], ['Alumni', route('landing.alumni')]],
                        'Informasi & Layanan' => [['Berita', route('landing.blog')], ['Pengumuman', route('landing.pengumuman')], ['Informasi PPDB', route('landing.ppdb')], ['Persyaratan PPDB', route('landing.ppdb.section', 'syarat').'#syarat'], ['Tahapan PPDB', route('landing.ppdb.section', 'jalur').'#jalur'], ['Biaya PPDB', route('landing.ppdb.section', 'biaya').'#biaya'], ['Daftar PPDB', route('landing.ppdb.section', 'daftar').'#daftar'], ['Kontak', route('landing.contact')], ['Login Portal', route('login')], ['Kebijakan Privasi', route('landing.kebijakan-privasi')], ['Standar Pelayanan', route('landing.standar-pelayanan')]],
                    ] as $group => $links)
                        <div class="rounded-2xl border border-slate-200 p-6"><h2 class="font-heading text-lg font-bold">{{ $group }}</h2><ul class="mt-4 space-y-3">@foreach($links as [$label, $url])<li><a href="{{ $url }}" class="text-sm text-blue-700 hover:underline">{{ $label }}</a></li>@endforeach</ul></div>
                    @endforeach
                </div>
            @elseif($page === 'kebijakan-privasi')
                <div class="max-w-3xl space-y-8 text-slate-600 leading-7">
                    <div><h2 class="font-heading text-xl font-bold text-slate-900">Data yang dikumpulkan</h2><p class="mt-3">Formulir PPDB meminta data identitas calon siswa, asal sekolah, alamat, pilihan pendidikan, serta kontak orang tua untuk proses pendaftaran. Website juga mencatat kunjungan untuk statistik penggunaan dan keamanan layanan.</p></div>
                    <div><h2 class="font-heading text-xl font-bold text-slate-900">Penggunaan data</h2><p class="mt-3">Data pendaftaran digunakan untuk verifikasi dan komunikasi oleh panitia. Jangan mengirim kata sandi atau informasi pembayaran melalui kolom formulir. Informasi alumni yang ditampilkan telah diverifikasi dan ditandai sebagai data publik.</p><p class="mt-3">Untuk mencegah informasi pop-up yang sama tampil berulang, website menyimpan penanda hash dari kombinasi alamat IP dan identitas browser/perangkat beserta waktu penayangannya.</p></div>
                    <div><h2 class="font-heading text-xl font-bold text-slate-900">Sesi dan akses akun</h2><p class="mt-3">Portal menggunakan sesi untuk menjaga status masuk dan keamanan formulir. Keluar dari akun setelah menggunakan perangkat bersama. Akses administrasi memerlukan akun dan hak akses yang sesuai.</p></div>
                    <div><h2 class="font-heading text-xl font-bold text-slate-900">Pertanyaan dan koreksi data</h2><p class="mt-3">Hubungi sekolah untuk meminta penjelasan penggunaan data atau mengajukan perbaikan data yang telah dikirim.</p><a href="{{ route('landing.contact') }}" class="mt-3 inline-flex font-semibold text-blue-700">Hubungi sekolah →</a></div>
                </div>
            @elseif($page === 'standar-pelayanan')
                <div class="grid gap-6 md:grid-cols-3">
                    @foreach([
                        ['Informasi sekolah', 'Profil, program pendidikan, fasilitas, dan pengumuman dapat diakses melalui website tanpa akun.', route('landing.pengumuman'), 'Lihat pengumuman'],
                        ['Pendaftaran siswa baru', 'Periksa jadwal dan persyaratan, isi formulir awal saat gelombang dibuka, lalu ikuti arahan verifikasi dari panitia.', route('landing.ppdb'), 'Informasi PPDB'],
                        ['Bantuan administrasi', 'Sampaikan kebutuhan layanan dan kontak yang dapat dihubungi. Persyaratan serta waktu penyelesaian dikonfirmasi oleh petugas sesuai jenis layanan.', route('landing.contact'), 'Hubungi petugas'],
                    ] as [$title, $text, $url, $label])
                        <article class="rounded-2xl border border-slate-200 p-6"><h2 class="font-heading text-xl font-bold">{{ $title }}</h2><p class="mt-4 text-sm leading-7 text-slate-600">{{ $text }}</p><a href="{{ $url }}" class="mt-5 inline-flex text-sm font-semibold text-blue-700">{{ $label }} →</a></article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</main>
