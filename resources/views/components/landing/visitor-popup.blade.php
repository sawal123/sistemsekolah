@props(['popup' => null])
@if($popup)
    <dialog id="visitor-popup" class="visitor-popup" aria-label="{{ $popup->judul }}" data-impression-url="{{ route('landing.popup.impression', $popup) }}">
        <div class="visitor-popup-stage">
            <a href="{{ $popup->link }}" class="visitor-popup-image-link" aria-label="{{ $popup->judul }} — lihat informasi">
                <img src="{{ asset('storage/'.$popup->gambar) }}" alt="{{ $popup->judul }}" data-popup-image>
            </a>
            <button type="button" class="visitor-popup-close" data-popup-close aria-label="Tutup informasi" autofocus>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" stroke-linecap="round"/></svg>
            </button>
        </div>
    </dialog>
@endif
