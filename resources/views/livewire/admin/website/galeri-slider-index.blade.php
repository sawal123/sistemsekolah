<div style="display:flex;flex-direction:column;gap:24px;height:100%;">
    <x-ui.toast />

    <div class="fu d1 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Galeri & Slider</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Upload gambar website dan atur urutan tampil slider utama.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($tab === 'slider')
                <x-ui.button wire:click="openSliderModal" variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Slider
                </x-ui.button>
            @else
                <x-ui.button wire:click="openGalleryModal" variant="primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Foto
                </x-ui.button>
            @endif
        </div>
    </div>

    <div class="fu d2 grid grid-cols-1 md:grid-cols-3 gap-3">
        <x-ui.card class="p-5">
            <p class="txt-muted text-xs font-bold uppercase tracking-wider">Total Slider</p>
            <p class="txt-primary mt-2 text-2xl font-extrabold">{{ $sliders->count() }}</p>
        </x-ui.card>
        <x-ui.card class="p-5">
            <p class="txt-muted text-xs font-bold uppercase tracking-wider">Slider Aktif</p>
            <p class="txt-primary mt-2 text-2xl font-extrabold">{{ $sliders->where('is_active', true)->count() }}</p>
        </x-ui.card>
        <x-ui.card class="p-5">
            <p class="txt-muted text-xs font-bold uppercase tracking-wider">Foto Galeri</p>
            <p class="txt-primary mt-2 text-2xl font-extrabold">{{ $galleries->count() }}</p>
        </x-ui.card>
    </div>

    <div class="fu d3 inline-flex w-fit flex-wrap gap-2 rounded-2xl border border-indigo-500/10 bg-white/35 p-1 dark:bg-white/5 dark:border-white/10">
        <button wire:click="setTab('slider')" class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $tab === 'slider' ? 'bg-indigo-500 text-white shadow-md shadow-indigo-500/25' : 'txt-secondary hover:bg-indigo-500/10' }}">Slider</button>
        <button wire:click="setTab('gallery')" class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $tab === 'gallery' ? 'bg-indigo-500 text-white shadow-md shadow-indigo-500/25' : 'txt-secondary hover:bg-indigo-500/10' }}">Galeri</button>
    </div>

    @if($tab === 'slider')
        <x-ui.card padding="0" class="fu d4 overflow-hidden">
            <div class="px-6 py-4 border-b border-indigo-500/10 dark:border-white/10">
                <h2 class="txt-primary text-base font-bold">Urutan Slider</h2>
                <p class="txt-muted text-xs mt-1">Gunakan tombol panah untuk mengubah posisi gambar di slider utama.</p>
            </div>

            <div class="divide-y divide-indigo-500/10 dark:divide-white/10">
                @forelse($sliders as $slider)
                    <div class="px-6 py-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-11 h-11 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center font-extrabold flex-shrink-0">
                                {{ $slider->urutan }}
                            </div>

                            @if($slider->foto)
                                <img src="{{ asset('storage/'.$slider->foto) }}" alt="{{ $slider->judul }}" class="w-32 h-20 rounded-2xl object-cover border border-indigo-500/10 flex-shrink-0">
                            @else
                                <div class="w-32 h-20 rounded-2xl bg-indigo-500/10 border border-indigo-500/10 flex items-center justify-center txt-muted text-xs flex-shrink-0">No Image</div>
                            @endif

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="txt-primary text-sm font-bold truncate">{{ $slider->judul }}</p>
                                    @if($slider->is_active)
                                        <x-ui.badge variant="success">Aktif</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="secondary">Nonaktif</x-ui.badge>
                                    @endif
                                </div>
                                <p class="txt-muted text-xs mt-1 line-clamp-2">{{ $slider->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 flex-shrink-0">
                            <button wire:click="moveSliderUp({{ $slider->id }})" class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500 transition" title="Naikkan urutan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 15l7-7 7 7" />
                                </svg>
                            </button>
                            <button wire:click="moveSliderDown({{ $slider->id }})" class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500 transition" title="Turunkan urutan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <button wire:click="toggleSliderStatus({{ $slider->id }})" class="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition" title="Aktif/nonaktif">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button wire:click="editSlider({{ $slider->id }})" class="p-2 rounded-lg hover:bg-indigo-500/10 text-indigo-500 transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M4 20h4.768L19.768 9a2.5 2.5 0 10-3.536-3.536L5.232 16.464 4 20z" />
                                </svg>
                            </button>
                            <button wire:click="confirmDelete('slider', {{ $slider->id }})" class="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-16 text-center txt-muted text-sm">Belum ada gambar slider.</div>
                @endforelse
            </div>
        </x-ui.card>
    @else
        <div class="fu d4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($galleries as $gallery)
                <x-ui.card padding="0" class="overflow-hidden">
                    @if($gallery->foto)
                        <img src="{{ asset('storage/'.$gallery->foto) }}" alt="{{ $gallery->judul }}" class="w-full aspect-video object-cover">
                    @else
                        <div class="w-full aspect-video bg-indigo-500/10 flex items-center justify-center txt-muted text-sm">No Image</div>
                    @endif
                    <div class="p-5">
                        <h3 class="txt-primary text-sm font-bold">{{ $gallery->judul }}</h3>
                        <p class="txt-muted text-xs mt-2 min-h-8">{{ $gallery->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                        <div class="flex items-center justify-end gap-2 mt-4 pt-4 border-t border-indigo-500/10">
                            <button wire:click="editGallery({{ $gallery->id }})" class="px-3 py-2 rounded-xl text-xs font-bold text-indigo-500 hover:bg-indigo-500/10">Edit</button>
                            <button wire:click="confirmDelete('gallery', {{ $gallery->id }})" class="px-3 py-2 rounded-xl text-xs font-bold text-red-500 hover:bg-red-500/10">Hapus</button>
                        </div>
                    </div>
                </x-ui.card>
            @empty
                <x-ui.card class="sm:col-span-2 xl:col-span-3 p-10 text-center">
                    <p class="txt-muted text-sm">Belum ada foto galeri.</p>
                </x-ui.card>
            @endforelse
        </div>
    @endif

    <x-ui.modal name="slider-form" :show="$isSliderModalOpen" maxWidth="2xl">
        <h2 class="text-xl font-bold txt-primary mb-1">{{ $editSliderId ? 'Edit Slider' : 'Tambah Slider' }}</h2>
        <p class="text-sm txt-muted mb-6">Gambar slider akan tampil berdasarkan nomor urutan terkecil.</p>

        <form wire:submit.prevent="saveSlider" class="space-y-5">
            <div>
                <x-ui.label for="sliderJudul" value="Judul Slider" class="mb-2 txt-secondary" />
                <x-ui.input wire:model="sliderJudul" id="sliderJudul" type="text" class="w-full" />
                @error('sliderJudul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-ui.label for="sliderDeskripsi" value="Deskripsi" class="mb-2 txt-secondary" />
                <textarea wire:model="sliderDeskripsi" id="sliderDeskripsi" class="block w-full rounded-xl border-2 glass focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 txt-primary py-2 px-3 transition-colors duration-200 placeholder-gray-400 outline-none" style="min-height:92px;"></textarea>
                @error('sliderDeskripsi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-ui.label value="Upload Gambar Slider" class="mb-2 txt-secondary" />
                <input wire:model="sliderFoto" type="file" accept="image/*" class="block w-full text-sm txt-muted" />
                <p class="txt-muted text-xs mt-1">Rekomendasi rasio 16:9, maksimal 4 MB.</p>
                @error('sliderFoto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="mt-3">
                    @if($sliderFoto)
                        <img src="{{ $sliderFoto->temporaryUrl() }}" class="w-full aspect-video rounded-2xl object-cover border border-indigo-500/10" alt="Preview slider">
                    @elseif($sliderExistingFoto)
                        <img src="{{ asset('storage/'.$sliderExistingFoto) }}" class="w-full aspect-video rounded-2xl object-cover border border-indigo-500/10" alt="Slider saat ini">
                    @endif
                </div>
            </div>

            <label class="flex items-center gap-3 rounded-2xl border border-indigo-500/10 bg-indigo-500/[0.03] p-4 cursor-pointer">
                <input wire:model="sliderIsActive" type="checkbox" class="rounded border-indigo-500/30 text-indigo-600 focus:ring-indigo-500">
                <span class="txt-primary text-sm font-semibold">Tampilkan slider ini</span>
            </label>

            <div class="flex justify-end gap-3 pt-4 border-t border-indigo-500/10">
                <x-ui.button wire:click="closeModal" variant="secondary" type="button">Batal</x-ui.button>
                <x-ui.button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="saveSlider">Simpan Slider</span>
                    <span wire:loading wire:target="saveSlider">Menyimpan...</span>
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal name="gallery-form" :show="$isGalleryModalOpen" maxWidth="2xl">
        <h2 class="text-xl font-bold txt-primary mb-1">{{ $editGalleryId ? 'Edit Foto Galeri' : 'Tambah Foto Galeri' }}</h2>
        <p class="text-sm txt-muted mb-6">Upload foto dokumentasi untuk galeri website sekolah.</p>

        <form wire:submit.prevent="saveGallery" class="space-y-5">
            <div>
                <x-ui.label for="galleryJudul" value="Judul Foto" class="mb-2 txt-secondary" />
                <x-ui.input wire:model="galleryJudul" id="galleryJudul" type="text" class="w-full" />
                @error('galleryJudul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-ui.label for="galleryDeskripsi" value="Deskripsi" class="mb-2 txt-secondary" />
                <textarea wire:model="galleryDeskripsi" id="galleryDeskripsi" class="block w-full rounded-xl border-2 glass focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 txt-primary py-2 px-3 transition-colors duration-200 placeholder-gray-400 outline-none" style="min-height:92px;"></textarea>
            </div>

            <div>
                <x-ui.label value="Upload Foto" class="mb-2 txt-secondary" />
                <input wire:model="galleryFoto" type="file" accept="image/*" class="block w-full text-sm txt-muted" />
                <p class="txt-muted text-xs mt-1">JPG, PNG, atau WebP maksimal 4 MB.</p>
                @error('galleryFoto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="mt-3">
                    @if($galleryFoto)
                        <img src="{{ $galleryFoto->temporaryUrl() }}" class="w-full aspect-video rounded-2xl object-cover border border-indigo-500/10" alt="Preview galeri">
                    @elseif($galleryExistingFoto)
                        <img src="{{ asset('storage/'.$galleryExistingFoto) }}" class="w-full aspect-video rounded-2xl object-cover border border-indigo-500/10" alt="Foto saat ini">
                    @endif
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-indigo-500/10">
                <x-ui.button wire:click="closeModal" variant="secondary" type="button">Batal</x-ui.button>
                <x-ui.button variant="primary" type="submit">
                    <span wire:loading.remove wire:target="saveGallery">Simpan Foto</span>
                    <span wire:loading wire:target="saveGallery">Menyimpan...</span>
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.confirm-modal
        name="confirm-delete-modal"
        title="Hapus Gambar"
        message="Apakah Anda yakin ingin menghapus gambar ini? File gambar juga akan dihapus dari storage."
        onConfirm="delete"
    />
</div>
