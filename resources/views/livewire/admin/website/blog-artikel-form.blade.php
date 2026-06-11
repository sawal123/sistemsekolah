<div style="display:flex;flex-direction:column;gap:22px;height:100%;">
    <x-ui.toast />

    <div class="fu d1 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">
                {{ $post ? 'Edit Artikel Blog' : 'Tulis Artikel Blog' }}
            </h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Rancang konten, media, status publikasi, dan SEO dalam satu ruang editorial.</p>
        </div>

        <a href="{{ route('admin.website.blog-artikel') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2 font-semibold text-sm rounded-xl transition-all duration-200 glass txt-secondary hover:bg-white/40 dark:hover:bg-white/10">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="save" class="fu d2 grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_360px] gap-5 items-start">
        <section class="glass-card" style="padding:24px;border-radius:18px;">
            <div class="space-y-5">
                <div>
                    <x-ui.label for="judul" value="Judul Artikel" class="mb-2 txt-secondary" />
                    <x-ui.input wire:model.live.debounce.300ms="judul" id="judul" type="text" class="w-full text-lg font-bold" placeholder="Contoh: Siswa SMA Nusantara Raih Prestasi Nasional" />
                    @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-ui.label for="slug" value="Slug URL" class="mb-2 txt-secondary" />
                    <x-ui.input wire:model="slug" id="slug" type="text" class="w-full" placeholder="siswa-sma-nusantara-raih-prestasi-nasional" />
                    @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div
                    x-data="blogPostEditor(@entangle('konten'))"
                    x-on:livewire:navigated.window="init()"
                >
                    <x-ui.label value="Konten Artikel" class="mb-2 txt-secondary" />
                    <div class="rounded-2xl border border-indigo-500/10 bg-white/45 overflow-hidden dark:bg-white/5 dark:border-white/10">
                        <div class="flex flex-wrap gap-1 border-b border-indigo-500/10 p-2 dark:border-white/10">
                            <button type="button" @click="format('bold')" title="Bold" class="editor-btn"><strong>B</strong></button>
                            <button type="button" @click="format('italic')" title="Italic" class="editor-btn"><em>I</em></button>
                            <button type="button" @click="format('underline')" title="Underline" class="editor-btn"><u>U</u></button>
                            <button type="button" @click="block('h2')" title="Heading 2" class="editor-btn">H2</button>
                            <button type="button" @click="block('h3')" title="Heading 3" class="editor-btn">H3</button>
                            <button type="button" @click="block('p')" title="Paragraf" class="editor-btn">P</button>
                            <button type="button" @click="format('insertUnorderedList')" title="Bullet list" class="editor-btn">List</button>
                            <button type="button" @click="format('insertOrderedList')" title="Numbered list" class="editor-btn">1. List</button>
                            <button type="button" @click="block('blockquote')" title="Kutipan" class="editor-btn">Quote</button>
                            <button type="button" @click="link()" title="Tautan" class="editor-btn">Link</button>
                            <button type="button" @click="format('removeFormat')" title="Hapus format" class="editor-btn">Clear</button>
                        </div>
                        <div
                            wire:ignore
                            x-ref="editor"
                            x-init="init()"
                            @input="sync()"
                            contenteditable="true"
                            class="blog-editor min-h-[520px] p-6 txt-primary text-[15px] leading-8 outline-none"
                        ></div>
                    </div>
                    @error('konten') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <aside class="space-y-5">
            <section class="glass-card" style="padding:20px;border-radius:18px;">
                <h2 class="txt-primary text-base font-bold mb-4">Publikasi</h2>
                <div class="space-y-4">
                    <div>
                        <x-ui.label for="status" value="Status" class="mb-2 txt-secondary" />
                        <select wire:model="status" id="status" class="w-full rounded-xl border border-indigo-500/10 bg-white/60 px-3 py-2.5 text-sm txt-primary outline-none dark:bg-white/5 dark:border-white/10">
                            <option value="Draft">Draft</option>
                            <option value="Published">Published</option>
                        </select>
                    </div>

                    <div>
                        <x-ui.label for="kategori_id" value="Kategori" class="mb-2 txt-secondary" />
                        <select wire:model="kategori_id" id="kategori_id" class="w-full rounded-xl border border-indigo-500/10 bg-white/60 px-3 py-2.5 text-sm txt-primary outline-none dark:bg-white/5 dark:border-white/10">
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                        @error('kategori_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-ui.label value="Tag" class="mb-2 txt-secondary" />
                        <div class="max-h-44 overflow-y-auto custom-scrollbar rounded-2xl border border-indigo-500/10 bg-white/35 p-2 dark:bg-white/5 dark:border-white/10">
                            @forelse($tags as $tag)
                                <label class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm txt-secondary cursor-pointer hover:bg-indigo-500/10">
                                    <input wire:model="selectedTags" type="checkbox" value="{{ $tag->id }}" class="rounded border-indigo-500/30 text-indigo-600 focus:ring-indigo-500" />
                                    #{{ $tag->nama_tag }}
                                </label>
                            @empty
                                <p class="txt-muted text-sm p-3">Belum ada tag.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <section class="glass-card" style="padding:20px;border-radius:18px;">
                <h2 class="txt-primary text-base font-bold mb-4">Thumbnail</h2>
                <label class="block rounded-2xl border border-dashed border-indigo-500/30 bg-indigo-500/[0.03] p-4 cursor-pointer hover:bg-indigo-500/[0.06]">
                    <input wire:model="thumbnailFile" type="file" accept="image/*" class="sr-only" />
                    <div class="flex flex-col items-center justify-center text-center gap-2 min-h-28">
                        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M4 20h16a2 2 0 002-2V6a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="txt-primary text-sm font-semibold">Upload gambar thumbnail</p>
                        <p class="txt-muted text-xs">JPG, PNG, WebP maksimal 3 MB</p>
                    </div>
                </label>
                @error('thumbnailFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                <div class="mt-4">
                    @if($thumbnailFile)
                        <img src="{{ $thumbnailFile->temporaryUrl() }}" class="w-full aspect-video object-cover rounded-2xl border border-indigo-500/10" alt="Preview thumbnail">
                    @elseif($existingThumbnail)
                        <img src="{{ asset('storage/'.$existingThumbnail) }}" class="w-full aspect-video object-cover rounded-2xl border border-indigo-500/10" alt="Thumbnail artikel">
                    @else
                        <div class="w-full aspect-video rounded-2xl border border-indigo-500/10 bg-white/30 dark:bg-white/5 flex items-center justify-center txt-muted text-sm">Belum ada thumbnail</div>
                    @endif
                </div>
            </section>

            <section class="glass-card" style="padding:20px;border-radius:18px;">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h2 class="txt-primary text-base font-bold">SEO</h2>
                        <p class="txt-muted text-xs mt-1">Meta untuk hasil pencarian dan share preview.</p>
                    </div>
                    <button type="button" wire:click="generateSeo" wire:loading.attr="disabled" wire:target="generateSeo" class="inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-3 py-2 text-xs font-bold text-white shadow-md shadow-indigo-500/25 disabled:opacity-60">
                        <span wire:loading.remove wire:target="generateSeo">AI SEO</span>
                        <span wire:loading wire:target="generateSeo">Membuat...</span>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-ui.label for="meta_title" value="Meta Title" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="meta_title" id="meta_title" type="text" class="w-full" maxlength="70" />
                        <p class="txt-muted text-xs mt-1">{{ mb_strlen($meta_title) }}/70 karakter</p>
                        @error('meta_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-ui.label for="meta_description" value="Meta Description" class="mb-2 txt-secondary" />
                        <textarea wire:model="meta_description" id="meta_description" maxlength="180" class="block w-full rounded-xl border-2 glass focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 txt-primary py-2 px-3 transition-colors duration-200 placeholder-gray-400 outline-none" style="min-height:92px;"></textarea>
                        <p class="txt-muted text-xs mt-1">{{ mb_strlen($meta_description) }}/180 karakter</p>
                        @error('meta_description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <x-ui.label for="meta_keywords" value="Meta Keywords" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="meta_keywords" id="meta_keywords" type="text" class="w-full" placeholder="prestasi siswa, sma, sekolah" />
                        @error('meta_keywords') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <div class="glass-card" style="padding:16px;border-radius:18px;">
                <x-ui.button type="submit" variant="primary" class="w-full py-3">
                    <span wire:loading.remove wire:target="save">{{ $post ? 'Simpan Perubahan' : 'Simpan Artikel' }}</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </x-ui.button>
            </div>
        </aside>
    </form>

    <style>
        .editor-btn {
            min-width: 38px;
            height: 34px;
            border-radius: 10px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 700;
            color: #4f46e5;
            background: rgba(99, 102, 241, 0.08);
        }

        .editor-btn:hover {
            background: rgba(99, 102, 241, 0.16);
        }

        .blog-editor h2 {
            font-size: 24px;
            font-weight: 800;
            margin: 20px 0 10px;
        }

        .blog-editor h3 {
            font-size: 19px;
            font-weight: 800;
            margin: 18px 0 8px;
        }

        .blog-editor p {
            margin: 0 0 14px;
        }

        .blog-editor ul,
        .blog-editor ol {
            padding-left: 26px;
            margin: 0 0 14px;
        }

        .blog-editor blockquote {
            border-left: 4px solid #6366f1;
            padding: 10px 16px;
            margin: 14px 0;
            background: rgba(99, 102, 241, 0.08);
            border-radius: 0 12px 12px 0;
        }

        .blog-editor a {
            color: #4f46e5;
            text-decoration: underline;
        }
    </style>

    <script>
        window.blogPostEditor = function(model) {
            return {
                model,
                init() {
                    if (this.$refs.editor && this.$refs.editor.innerHTML !== this.model) {
                        this.$refs.editor.innerHTML = this.model || '';
                    }
                },
                sync() {
                    this.model = this.$refs.editor.innerHTML;
                },
                format(command) {
                    document.execCommand(command, false, null);
                    this.sync();
                },
                block(tag) {
                    document.execCommand('formatBlock', false, tag);
                    this.sync();
                },
                link() {
                    const url = window.prompt('Masukkan URL tautan');
                    if (url) {
                        document.execCommand('createLink', false, url);
                        this.sync();
                    }
                }
            }
        }
    </script>
</div>
