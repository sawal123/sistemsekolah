<div style="display:flex;flex-direction:column;gap:24px;height:100%;">
    <x-ui.toast />

    <div class="fu d1 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Pengaturan Umum</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Atur identitas website, branding, SEO default, kontak, dan sosial media sekolah.</p>
        </div>

        <x-ui.button wire:click="save" variant="primary">
            <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </x-ui.button>
    </div>

    <form wire:submit.prevent="save" class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_380px] gap-5 items-start">
        <div class="space-y-5">
            <x-ui.card title="Identitas Website" class="fu d2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <x-ui.label for="site_name" value="Nama Website" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="site_name" id="site_name" type="text" />
                        @error('site_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-ui.label for="site_title" value="Title Browser" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="site_title" id="site_title" type="text" />
                        @error('site_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <x-ui.label for="site_tagline" value="Tagline" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="site_tagline" id="site_tagline" type="text" placeholder="Contoh: Berkarakter, Berprestasi, Berwawasan Global" />
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Profil Sekolah" class="fu d3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <x-ui.label for="school_name" value="Nama Sekolah" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="school_name" id="school_name" type="text" />
                        @error('school_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <x-ui.label for="school_address" value="Alamat" class="mb-2 txt-secondary" />
                        <textarea wire:model="school_address" id="school_address" class="block w-full rounded-xl border-2 glass focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 txt-primary py-2 px-3 transition-colors duration-200 placeholder-gray-400 outline-none" style="min-height:92px;"></textarea>
                    </div>
                    <div>
                        <x-ui.label for="school_phone" value="Telepon" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="school_phone" id="school_phone" type="text" />
                    </div>
                    <div>
                        <x-ui.label for="whatsapp" value="WhatsApp" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="whatsapp" id="whatsapp" type="text" placeholder="62812..." />
                    </div>
                    <div>
                        <x-ui.label for="school_email" value="Email" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="school_email" id="school_email" type="email" />
                        @error('school_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-ui.label for="school_website" value="Website" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="school_website" id="school_website" type="text" placeholder="https://sekolah.sch.id" />
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="fu d4">
                <x-slot name="header">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full">
                        <div>
                            <h3 class="txt-primary font-bold text-[15px]">SEO Default</h3>
                            <p class="txt-muted text-xs mt-1">Generate SEO untuk website sekolah menengah atas.</p>
                        </div>
                        <button type="button" wire:click="generateSeo" wire:loading.attr="disabled" wire:target="generateSeo"
                            class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-emerald-500/10 text-emerald-600 text-xs font-bold border border-emerald-500/20 hover:bg-emerald-500/20 disabled:opacity-50">
                            <svg wire:loading.remove wire:target="generateSeo" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            <svg wire:loading wire:target="generateSeo" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="generateSeo">Generate SEO AI</span>
                            <span wire:loading wire:target="generateSeo">Generate...</span>
                        </button>
                    </div>
                </x-slot>
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <x-ui.label for="seo_title" value="SEO Title" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="seo_title" id="seo_title" type="text" maxlength="70" />
                        <p class="txt-muted text-xs mt-1">{{ mb_strlen($seo_title) }}/70 karakter</p>
                        @error('seo_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-ui.label for="seo_description" value="SEO Description" class="mb-2 txt-secondary" />
                        <textarea wire:model="seo_description" id="seo_description" maxlength="180" class="block w-full rounded-xl border-2 glass focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 txt-primary py-2 px-3 transition-colors duration-200 placeholder-gray-400 outline-none" style="min-height:92px;"></textarea>
                        <p class="txt-muted text-xs mt-1">{{ mb_strlen($seo_description) }}/180 karakter</p>
                        @error('seo_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-ui.label for="seo_keywords" value="SEO Keywords" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="seo_keywords" id="seo_keywords" type="text" placeholder="sekolah, sma, prestasi siswa" />
                    </div>
                </div>
            </x-ui.card>
        </div>

        <aside class="space-y-5">
            <x-ui.card title="Branding" class="fu d2">
                <div class="space-y-5">
                    <div>
                        <x-ui.label value="Logo Website" class="mb-2 txt-secondary" />
                        <label class="block rounded-2xl border border-dashed border-indigo-500/30 bg-indigo-500/[0.03] p-4 cursor-pointer hover:bg-indigo-500/[0.06]">
                            <input wire:model="logoFile" type="file" accept="image/*" class="sr-only" />
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 rounded-2xl bg-white/50 dark:bg-white/5 border border-indigo-500/10 flex items-center justify-center overflow-hidden">
                                    @if($logoFile)
                                        <img src="{{ $logoFile->temporaryUrl() }}" class="w-full h-full object-contain" alt="Preview logo">
                                    @elseif($existingLogo)
                                        <img src="{{ asset('storage/'.$existingLogo) }}" class="w-full h-full object-contain" alt="Logo saat ini">
                                    @else
                                        <span class="txt-muted text-xs">Logo</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="txt-primary text-sm font-bold">Upload logo</p>
                                    <p class="txt-muted text-xs mt-1">PNG/JPG/WebP maksimal 2 MB.</p>
                                </div>
                            </div>
                        </label>
                        @error('logoFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-ui.label value="Favicon" class="mb-2 txt-secondary" />
                        <label class="block rounded-2xl border border-dashed border-indigo-500/30 bg-indigo-500/[0.03] p-4 cursor-pointer hover:bg-indigo-500/[0.06]">
                            <input wire:model="faviconFile" type="file" accept="image/*" class="sr-only" />
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl bg-white/50 dark:bg-white/5 border border-indigo-500/10 flex items-center justify-center overflow-hidden">
                                    @if($faviconFile)
                                        <img src="{{ $faviconFile->temporaryUrl() }}" class="w-full h-full object-contain" alt="Preview favicon">
                                    @elseif($existingFavicon)
                                        <img src="{{ asset('storage/'.$existingFavicon) }}" class="w-full h-full object-contain" alt="Favicon saat ini">
                                    @else
                                        <span class="txt-muted text-xs">Icon</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="txt-primary text-sm font-bold">Upload favicon</p>
                                    <p class="txt-muted text-xs mt-1">Disarankan persegi 512x512.</p>
                                </div>
                            </div>
                        </label>
                        @error('faviconFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Sosial Media" class="fu d3">
                <div class="space-y-4">
                    <div>
                        <x-ui.label for="facebook_url" value="Facebook" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="facebook_url" id="facebook_url" type="url" placeholder="https://facebook.com/..." />
                        @error('facebook_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-ui.label for="instagram_url" value="Instagram" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="instagram_url" id="instagram_url" type="url" placeholder="https://instagram.com/..." />
                        @error('instagram_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-ui.label for="youtube_url" value="YouTube" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="youtube_url" id="youtube_url" type="url" placeholder="https://youtube.com/..." />
                        @error('youtube_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-ui.label for="tiktok_url" value="TikTok" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="tiktok_url" id="tiktok_url" type="url" placeholder="https://tiktok.com/..." />
                        @error('tiktok_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-ui.label for="linkedin_url" value="LinkedIn" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="linkedin_url" id="linkedin_url" type="url" placeholder="https://linkedin.com/..." />
                        @error('linkedin_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="fu d4">
                <x-ui.button type="submit" variant="primary" class="w-full py-3">
                    <span wire:loading.remove wire:target="save">Simpan Pengaturan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </x-ui.button>
            </x-ui.card>
        </aside>
    </form>
</div>
