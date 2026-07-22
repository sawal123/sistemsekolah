<?php

namespace App\Livewire\Landing;

use App\Models\Jurusan;
use App\Models\PendaftaranMuridBaru;
use App\Models\PpdbGelombang;
use App\Models\Setting;
use App\Models\Spp;
use App\Models\TahunAjaran;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.landing')]
#[Title('PPDB')]
class PpdbPage extends Component
{
    public string $section = 'informasi';

    public string $nama_lengkap = '';

    public string $nisn = '';

    public string $nik = '';

    public string $tempat_lahir = '';

    public string $tanggal_lahir = '';

    public string $jenis_kelamin = '';

    public string $no_hp = '';

    public string $email = '';

    public string $alamat = '';

    public string $sekolah_asal = '';

    public string $tahun_lulus = '';

    public string $jurusan_pilihan_1 = '';

    public string $jenjang_pilihan = 'SMA';

    public string $jurusan_id = '';

    public string $nama_ayah = '';

    public string $nama_ibu = '';

    public string $no_telp_ortu = '';

    public string $website = '';

    public function mount(string $section = 'informasi'): void
    {
        abort_unless(in_array($section, ['informasi', 'syarat', 'jalur', 'biaya', 'daftar'], true), 404);
        $this->section = $section;
    }

    public function register(): void
    {
        if ($this->website !== '') {
            return;
        }

        $wave = PpdbGelombang::where('status', 'Dibuka')
            ->orderByDesc('tahun_pendaftaran')
            ->orderBy('tanggal_mulai')
            ->first();

        if (! $wave) {
            $this->addError('registration', 'Pendaftaran belum dibuka oleh panitia PPDB.');

            return;
        }

        $validated = $this->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => ['required', 'string', 'max:20', Rule::unique('pendaftaran_murid_barus', 'nisn')],
            'nik' => ['nullable', 'string', 'max:20', Rule::unique('pendaftaran_murid_barus', 'nik')],
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'no_hp' => 'required|string|max:30',
            'email' => 'nullable|email|max:120',
            'alamat' => 'required|string|max:1000',
            'sekolah_asal' => 'required|string|max:255',
            'tahun_lulus' => 'nullable|integer|min:2000|max:'.(now()->year + 1),
            'jurusan_pilihan_1' => 'nullable|required_unless:jenjang_pilihan,SMK|string|max:100',
            'jenjang_pilihan' => 'required|in:SMP,SMA,SMK',
            'jurusan_id' => 'nullable|required_if:jenjang_pilihan,SMK|exists:jurusans,id',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'no_telp_ortu' => 'required|string|max:30',
        ]);

        $jurusan = $this->jurusan_id !== '' ? Jurusan::find($this->jurusan_id) : null;
        $validated['jurusan_pilihan_1'] = $jurusan?->nama ?? $this->jurusan_pilihan_1;
        $validated['jurusan_id'] = $jurusan?->id;

        PendaftaranMuridBaru::create($validated + [
            'ppdb_gelombang_id' => $wave->id,
            'sekolah_pilihan_1' => Setting::where('key', 'school_name')->value('value'),
            'document_upload_mode' => 'terpisah',
            'status' => 'Baru',
        ]);

        $this->reset([
            'nama_lengkap', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'no_hp', 'email', 'alamat', 'sekolah_asal', 'tahun_lulus', 'jurusan_pilihan_1',
            'nama_ayah', 'nama_ibu', 'no_telp_ortu',
            'jenjang_pilihan', 'jurusan_id',
        ]);

        session()->flash('registration_success', 'Pendaftaran awal berhasil dikirim. Panitia akan menghubungi Anda untuk kelengkapan dokumen.');
    }

    public function render()
    {
        $waves = PpdbGelombang::orderByDesc('tahun_pendaftaran')->orderBy('tanggal_mulai')->get();
        $activeYear = TahunAjaran::where('is_active', true)->first();

        return view('livewire.landing.ppdb-page', [
            'settings' => Setting::pluck('value', 'key'),
            'waves' => $waves,
            'openWave' => $waves->firstWhere('status', 'Dibuka'),
            'jurusans' => Jurusan::where('is_active', true)->orderBy('nama')->get(),
            'fees' => Spp::with('tahunAjaran')
                ->active()
                ->publicFees()
                ->when(
                    $activeYear,
                    fn ($query) => $query->where('tahun_ajaran_id', $activeYear->id),
                    fn ($query) => $query->whereRaw('1 = 0')
                )
                ->with('jurusan')
                ->orderByRaw("CASE kategori WHEN 'SPP Bulanan' THEN 1 WHEN 'Uang Bangunan' THEN 2 ELSE 3 END")
                ->orderByRaw("CASE jenjang WHEN 'SMP' THEN 1 WHEN 'SMA' THEN 2 WHEN 'SMK' THEN 3 ELSE 4 END")
                ->orderBy('jurusan_id')
                ->get(),
        ]);
    }
}
