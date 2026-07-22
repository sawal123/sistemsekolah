<div style="display:flex;flex-direction:column;gap:20px;height:100%;">
    <div class="fu d1" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <h1 class="txt-primary" style="font-size:22px;font-weight:800;">{{ $guru ? 'Edit Data Guru' : 'Tambah Guru Baru' }}</h1>
            <p class="txt-muted" style="font-size:13px;margin-top:3px;">Isi data diri dan informasi akademik guru.</p>
        </div>
        <a href="{{ route('admin.civitas.data-guru') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2 font-semibold text-sm rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 glass txt-secondary hover:bg-white/40 dark:hover:bg-white/10 focus:ring-gray-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <form wire:submit.prevent="save" class="fu d2 glass-card" style="padding:24px;">
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:20px;">
            {{-- Biodata Pribadi --}}
            <div>
                <x-ui.label value="NIP (Nomor Induk Pegawai) *" class="mb-2" />
                <x-ui.input wire:model="nip" type="text" required />
                @error('nip') <span style="color:red;font-size:11px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-ui.label value="Nama Lengkap *" class="mb-2" />
                <x-ui.input wire:model="nama_lengkap" type="text" required />
                @error('nama_lengkap') <span style="color:red;font-size:11px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-ui.label value="Tempat Lahir" class="mb-2" />
                <x-ui.input wire:model="tempat_lahir" type="text" />
            </div>

            <div>
                <x-ui.label value="Tanggal Lahir *" class="mb-2" />
                <x-ui.input wire:model="tanggal_lahir" type="date" required />
                <p style="font-size:10px;margin-top:4px;" class="txt-muted">Otomatis jadi password (format ddmmyyyy) untuk akun baru.</p>
                @error('tanggal_lahir') <span style="color:red;font-size:11px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-ui.select wire:model="agama" label="Agama" :options="['' => 'Pilih Agama...', 'Islam' => 'Islam', 'Kristen' => 'Kristen', 'Katolik' => 'Katolik', 'Hindu' => 'Hindu', 'Buddha' => 'Buddha', 'Konghucu' => 'Konghucu', 'Lainnya' => 'Lainnya']" />
            </div>

            <div>
                <x-ui.select wire:model="jabatan" label="Jabatan *" :options="['' => 'Pilih Jabatan...', 'Guru Tetap' => 'Guru Tetap', 'Guru Honorer' => 'Guru Honorer', 'Wakasek' => 'Wakasek']" />
                @error('jabatan') <span style="color:red;font-size:11px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-ui.label value="No Telepon" class="mb-2" />
                <x-ui.input wire:model="no_telp" type="text" />
            </div>

            <div style="grid-column:1 / -1;">
                <x-ui.label value="Alamat Lengkap" class="mb-2" />
                <textarea wire:model="alamat" class="block w-full rounded-xl border-2 glass focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/15 txt-primary py-2 px-3 transition-colors duration-200 placeholder-gray-400 outline-none" style="min-height:80px;"></textarea>
            </div>

            {{-- Akun & Foto --}}
            <div style="grid-column:1 / -1;padding-top:16px;border-top:1px solid rgba(99,102,241,0.1);">
                <h3 class="txt-primary" style="font-size:15px;font-weight:700;margin-bottom:12px;">Akun & Media</h3>
            </div>

            <div>
                <x-ui.label value="Email Login" class="mb-2" />
                <x-ui.input wire:model="email" type="email" />
                <p style="font-size:10px;margin-top:4px;" class="txt-muted">Kosongkan agar otomatis di-generate: NIP@sekolah.sch.id</p>
                @error('email') <span style="color:red;font-size:11px;">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-ui.label value="Unggah Foto Profil" class="mb-2" />
                <input wire:model="foto" type="file" accept="image/*" style="width:100%;font-size:13px;" class="txt-muted">
                <div style="margin-top:8px;">
                    @if ($foto)
                        <img src="{{ $foto->temporaryUrl() }}" style="width:60px;height:60px;object-fit:cover;border-radius:10px;">
                    @elseif ($existing_foto)
                        <img src="{{ asset('storage/'.$existing_foto) }}" style="width:60px;height:60px;object-fit:cover;border-radius:10px;">
                    @endif
                </div>
                @error('foto') <span style="color:red;font-size:11px;">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-top:24px;padding-top:16px;border-top:1px solid rgba(99,102,241,0.1);display:flex;justify-content:flex-end;">
            <x-ui.button type="submit" variant="primary">
                <span wire:loading.remove wire:target="save">Simpan Data Guru</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </x-ui.button>
        </div>
    </form>
</div>
