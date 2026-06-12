<div style="display:flex;flex-direction:column;gap:24px;height:100%;">
    <x-ui.toast />

    <div class="fu d1 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="txt-primary" style="font-size:24px;font-weight:800;letter-spacing:-0.02em;">Visi, Misi & Sambutan</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:4px;">Kelola visi misi sekolah serta sambutan kepala sekolah untuk ditampilkan di halaman utama.</p>
        </div>

        <x-ui.button wire:click="save" variant="primary">
            <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
            <span wire:loading wire:target="save">Menyimpan...</span>
        </x-ui.button>
    </div>

    <form wire:submit.prevent="save" class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_380px] gap-5 items-start">
        <div class="space-y-5">
            {{-- Visi & Misi Sekolah --}}
            <x-ui.card title="Visi & Misi Sekolah" class="fu d2">
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <x-ui.label for="school_vision" value="Visi Sekolah" class="mb-2 txt-secondary" />
                        <textarea wire:model="school_vision" id="school_vision" 
                            placeholder="Tuliskan visi sekolah..."
                            class="block w-full rounded-xl border-2 glass focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 txt-primary py-2 px-3 transition-colors duration-200 placeholder-gray-400 outline-none" 
                            style="min-height:120px;"></textarea>
                        @error('school_vision') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <x-ui.label for="school_mission" value="Misi Sekolah" class="mb-1 txt-secondary" />
                        <span class="text-xs text-slate-400 dark:text-slate-500 block mb-2">*Tuliskan setiap poin misi pada baris baru (tekan Enter untuk baris baru) agar tampil sebagai daftar poin otomatis di landing page.</span>
                        <textarea wire:model="school_mission" id="school_mission" 
                            placeholder="Contoh:&#10;1. Melaksanakan pembelajaran aktif, inovatif, kreatif, dan menyenangkan.&#10;2. Menumbuhkan nilai karakter keagamaan yang kuat.&#10;3. Meningkatkan penguasaan teknologi informasi."
                            class="block w-full rounded-xl border-2 glass focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 txt-primary py-2 px-3 transition-colors duration-200 placeholder-gray-400 outline-none" 
                            style="min-height:220px;"></textarea>
                        @error('school_mission') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </x-ui.card>

            {{-- Sambutan Kepala Sekolah --}}
            <x-ui.card title="Sambutan Kepala Sekolah" class="fu d3">
                <div class="grid grid-cols-1 gap-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-ui.label for="principal_name" value="Nama Kepala Sekolah" class="mb-2 txt-secondary" />
                            <x-ui.input wire:model="principal_name" id="principal_name" type="text" placeholder="Contoh: Dr. H. Ahmad Fauzi, M.Pd" />
                            @error('principal_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-ui.label for="principal_nip" value="NIP / NIDN (Opsional)" class="mb-2 txt-secondary" />
                            <x-ui.input wire:model="principal_nip" id="principal_nip" type="text" placeholder="Contoh: 198203112009121003" />
                            @error('principal_nip') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <x-ui.label for="principal_title" value="Jabatan / Gelar Jabatan" class="mb-2 txt-secondary" />
                        <x-ui.input wire:model="principal_title" id="principal_title" type="text" placeholder="Contoh: Kepala Sekolah SMA Nusantara" />
                        @error('principal_title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <x-ui.label for="principal_greeting" value="Naskah Sambutan Kepala Sekolah" class="mb-2 txt-secondary" />
                        <textarea wire:model="principal_greeting" id="principal_greeting" 
                            placeholder="Tuliskan kata sambutan kepala sekolah secara mendalam..."
                            class="block w-full rounded-xl border-2 glass focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 txt-primary py-2 px-3 transition-colors duration-200 placeholder-gray-400 outline-none" 
                            style="min-height:240px;"></textarea>
                        @error('principal_greeting') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </x-ui.card>
        </div>

        <aside class="space-y-5">
            {{-- Foto Kepala Sekolah --}}
            <x-ui.card title="Foto Kepala Sekolah" class="fu d2">
                <div class="space-y-5">
                    <div>
                        <label class="block rounded-2xl border border-dashed border-indigo-500/30 bg-indigo-500/[0.03] p-4 cursor-pointer hover:bg-indigo-500/[0.06] transition-colors">
                            <input wire:model="principalImageFile" type="file" accept="image/*" class="sr-only" />
                            
                            <div class="flex flex-col items-center justify-center text-center py-4">
                                <div class="w-32 h-32 rounded-2xl bg-white/50 dark:bg-white/5 border border-indigo-500/10 flex items-center justify-center overflow-hidden mb-3 relative group">
                                    @if($principalImageFile)
                                        <img src="{{ $principalImageFile->temporaryUrl() }}" class="w-full h-full object-cover" alt="Preview foto">
                                    @elseif($existingPrincipalImage)
                                        <img src="{{ asset('storage/'.$existingPrincipalImage) }}" class="w-full h-full object-cover" alt="Foto saat ini">
                                    @else
                                        <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-600">
                                            <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span style="font-size: 11px;">Belum ada foto</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div>
                                    <p class="txt-primary text-sm font-bold">Upload Foto Kepala Sekolah</p>
                                    <p class="txt-muted text-xs mt-1">PNG/JPG/WebP maks 2 MB.</p>
                                </div>
                            </div>
                        </label>
                        @error('principalImageFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($existingPrincipalImage)
                        <button type="button" wire:click="deletePrincipalImage" 
                            wire:confirm="Apakah Anda yakin ingin menghapus foto kepala sekolah saat ini?"
                            class="w-full py-2.5 rounded-xl border border-red-500/20 bg-red-500/5 hover:bg-red-500/10 text-red-500 text-xs font-semibold transition-all">
                            Hapus Foto Saat Ini
                        </button>
                    @endif
                </div>
            </x-ui.card>

            <x-ui.card class="fu d4">
                <x-ui.button type="submit" variant="primary" class="w-full py-3">
                    <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </x-ui.button>
            </x-ui.card>
        </aside>
    </form>
</div>
