<main class="bg-white">
    <x-landing.page-hero
        eyebrow="Hubungi Kami"
        title="Kontak Sekolah"
        description="Silakan hubungi sekolah melalui kanal resmi berikut untuk informasi akademik, PPDB, maupun layanan administrasi."
    />

    <section class="py-20">
        <div class="mx-auto grid max-w-7xl gap-8 px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
            @foreach([
                ['Alamat', $settings->get('school_address') ?: 'Alamat sekolah belum diatur', null],
                ['Telepon', $settings->get('school_phone') ?: 'Belum tersedia', $settings->get('school_phone') ? 'tel:' . preg_replace('/[^0-9+]/', '', $settings->get('school_phone')) : null],
                ['Email', $settings->get('school_email') ?: 'Belum tersedia', $settings->get('school_email') ? 'mailto:' . $settings->get('school_email') : null],
                ['WhatsApp', $settings->get('whatsapp') ?: 'Belum tersedia', $settings->get('whatsapp') ? 'https://wa.me/' . preg_replace('/\D/', '', $settings->get('whatsapp')) : null],
            ] as [$label, $value, $url])
                <article class="rounded-3xl border border-blue-100 p-6">
                    <p class="text-sm font-bold uppercase tracking-wide text-blue-600">{{ $label }}</p>
                    @if($url)
                        <a href="{{ $url }}" class="mt-4 block font-semibold leading-7 text-blue-950 hover:text-blue-600" @if(str_starts_with($url, 'http')) target="_blank" rel="noopener" @endif>{{ $value }}</a>
                    @else
                        <p class="mt-4 font-semibold leading-7 text-blue-950">{{ $value }}</p>
                    @endif
                </article>
            @endforeach
        </div>

        <div class="mx-auto mt-12 grid max-w-7xl gap-8 px-6 lg:grid-cols-[1.15fr_0.85fr] lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-slate-100">
                <iframe
                    title="Lokasi sekolah"
                    src="https://www.google.com/maps?q={{ urlencode($settings->get('school_address') ?: 'Indonesia') }}&output=embed"
                    class="h-[430px] w-full border-0"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>
            <aside class="rounded-3xl bg-blue-950 p-8 text-white">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-blue-200">Jam Pelayanan</p>
                <h2 class="mt-3 font-display text-3xl font-bold">Kami siap membantu</h2>
                <div class="mt-8 space-y-4 text-blue-100">
                    <div class="flex justify-between border-b border-white/10 pb-4"><span>Senin–Kamis</span><span class="font-semibold">07.30–15.30</span></div>
                    <div class="flex justify-between border-b border-white/10 pb-4"><span>Jumat</span><span class="font-semibold">07.30–14.30</span></div>
                    <div class="flex justify-between border-b border-white/10 pb-4"><span>Sabtu–Minggu</span><span class="font-semibold">Tutup</span></div>
                </div>
                <a href="{{ route('landing.ppdb') }}" class="mt-8 inline-flex rounded-full bg-white px-6 py-3 font-bold text-blue-700">Informasi PPDB</a>
            </aside>
        </div>
    </section>
</main>
