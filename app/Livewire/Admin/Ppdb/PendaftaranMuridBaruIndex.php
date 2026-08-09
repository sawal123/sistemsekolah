<?php

namespace App\Livewire\Admin\Ppdb;

use App\Models\Jurusan;
use App\Models\PendaftaranMuridBaru;
use App\Models\PpdbGelombang;
use App\Models\PpdbGelombangRiwayat;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\AddressGeocodingService;
use App\Services\PpdbDocumentExtractionService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Pendaftaran Murid Baru')]
class PendaftaranMuridBaruIndex extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public string $filterTahunPendaftaran = '';

    public string $filterGelombangId = '';

    public int $perPage = 10;

    public bool $isModalOpen = false;

    public bool $isGelombangModalOpen = false;

    public ?int $editId = null;

    public ?int $gelombangEditId = null;

    public ?int $idBeingDeleted = null;

    public string $ppdb_gelombang_id = '';

    public string $gelombang_tahun_pendaftaran = '';

    public string $gelombang_nama = '';

    public string $gelombang_tanggal_mulai = '';

    public string $gelombang_tanggal_selesai = '';

    public string $gelombang_status = 'Draft';

    public string $gelombang_deskripsi = '';

    public string $nama_lengkap = '';

    public string $nisn = '';

    public string $nik = '';

    public string $tempat_lahir = '';

    public string $tanggal_lahir = '';

    public string $jenis_kelamin = '';

    public string $agama = '';

    public string $no_hp = '';

    public string $email = '';

    public string $alamat = '';

    public string $rt = '';

    public string $rw = '';

    public string $dusun = '';

    public string $kelurahan = '';

    public string $kecamatan = '';

    public string $kota_kabupaten = '';

    public string $provinsi = '';

    public string $no_kk = '';

    public string $tanggal_terbit_kk = '';

    public string $koordinat_rumah = '';

    public string $sekolah_asal = '';

    public string $npsn_sekolah_asal = '';

    public string $tahun_lulus = '';

    public string $nilai_semester_1 = '';

    public string $nilai_semester_2 = '';

    public string $nilai_semester_3 = '';

    public string $nilai_semester_4 = '';

    public string $nilai_semester_5 = '';

    public string $nama_prestasi = '';

    public string $tingkat_prestasi = '';

    public string $tahun_prestasi = '';

    public string $penyelenggara_prestasi = '';

    public string $nama_ayah = '';

    public string $nik_ayah = '';

    public string $pekerjaan_ayah = '';

    public string $nama_ibu = '';

    public string $nik_ibu = '';

    public string $pekerjaan_ibu = '';

    public string $nama_wali = '';

    public string $nik_wali = '';

    public string $pekerjaan_wali = '';

    public string $penghasilan_ortu = '';

    public string $no_telp_ortu = '';

    public string $sekolah_pilihan_1 = '';

    public string $jenjang_pilihan = '';

    public string $jurusan_id = '';

    public string $jurusan_pilihan_1 = '';

    public string $sekolah_pilihan_2 = '';

    public string $jurusan_pilihan_2 = '';

    public string $document_upload_mode = 'terpisah';

    public string $status = 'Baru';

    public string $catatan = '';

    public $ijazah_skl;

    public $kartu_keluarga;

    public $akta_kelahiran;

    public $ktp_ayah;

    public $ktp_ibu;

    public $buku_rapor;

    public $pas_foto;

    public $dokumen_gabungan;

    public array $existingFiles = [];

    public array $aiExtractedFields = [];

    public string $aiLastDocument = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTahunPendaftaran(): void
    {
        $this->filterGelombangId = '';
        $this->resetPage();
    }

    public function updatingFilterGelombangId(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PendaftaranMuridBaru::with(['gelombang', 'jurusan'])
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_lengkap', 'like', '%' . $this->search . '%')
                        ->orWhere('nisn', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('sekolah_asal', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus !== '', function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterTahunPendaftaran !== '', function ($q) {
                $q->whereHas('gelombang', fn($gelombang) => $gelombang->where('tahun_pendaftaran', $this->filterTahunPendaftaran));
            })
            ->when($this->filterGelombangId !== '', function ($q) {
                $q->where('ppdb_gelombang_id', $this->filterGelombangId);
            });

        $tahunOptions = PpdbGelombang::query()
            ->select('tahun_pendaftaran')
            ->distinct()
            ->orderByDesc('tahun_pendaftaran')
            ->pluck('tahun_pendaftaran', 'tahun_pendaftaran')
            ->mapWithKeys(fn($value, $key) => [(string) $key => (string) $value])
            ->toArray();

        $gelombangs = PpdbGelombang::withCount('pendaftarans')
            ->orderByDesc('tahun_pendaftaran')
            ->orderBy('tanggal_mulai')
            ->orderBy('nama_gelombang')
            ->get();

        $filteredGelombangOptions = $gelombangs
            ->when($this->filterTahunPendaftaran !== '', fn($items) => $items->where('tahun_pendaftaran', (int) $this->filterTahunPendaftaran))
            ->mapWithKeys(fn($item) => [(string) $item->id => $item->tahun_pendaftaran . ' - ' . $item->nama_gelombang])
            ->toArray();

        $gelombangOptions = $gelombangs
            ->mapWithKeys(fn($item) => [(string) $item->id => $item->tahun_pendaftaran . ' - ' . $item->nama_gelombang . ' (' . $item->status . ')'])
            ->toArray();

        return view('livewire.admin.ppdb.pendaftaran-murid-baru-index', [
            'pendaftarans' => $query->latest()->paginate($this->perPage),
            'statusOptions' => $this->statusOptions(),
            'tahunOptions' => $tahunOptions,
            'gelombangs' => $gelombangs,
            'gelombangOptions' => $gelombangOptions,
            'filteredGelombangOptions' => $filteredGelombangOptions,
            'riwayatGelombangs' => PpdbGelombangRiwayat::with(['gelombang', 'user'])->latest('terjadi_pada')->limit(8)->get(),
            'jurusans' => Jurusan::where('is_active', true)->orderBy('nama')->get(),
            'stats' => [
                'total' => (clone $query)->count(),
                'baru' => (clone $query)->where('status', 'Baru')->count(),
                'lengkap' => (clone $query)->where('status', 'Lengkap')->count(),
                'diterima' => (clone $query)->where('status', 'Diterima')->count(),
            ],
        ]);
    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->ppdb_gelombang_id = (string) (PpdbGelombang::where('status', 'Dibuka')
            ->orderByDesc('tahun_pendaftaran')
            ->orderBy('tanggal_mulai')
            ->value('id') ?? '');
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'pendaftaran-murid-baru-form');
    }

    public function openGelombangModal(): void
    {
        $this->resetGelombangForm();
        $this->isGelombangModalOpen = true;
        $this->dispatch('open-modal', 'gelombang-ppdb-form');
    }

    public function closeModal(): void
    {
        $this->isModalOpen = false;
        $this->idBeingDeleted = null;
        $this->dispatch('close-modal', 'pendaftaran-murid-baru-form');
        $this->dispatch('close-modal', 'gelombang-ppdb-form');
        $this->dispatch('close-modal', 'confirm-delete-pendaftaran-modal');
    }

    public function resetForm(): void
    {
        $this->reset([
            'editId',
            'ppdb_gelombang_id',
            'nama_lengkap',
            'nisn',
            'nik',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'agama',
            'no_hp',
            'email',
            'alamat',
            'rt',
            'rw',
            'dusun',
            'kelurahan',
            'kecamatan',
            'kota_kabupaten',
            'provinsi',
            'no_kk',
            'tanggal_terbit_kk',
            'koordinat_rumah',
            'sekolah_asal',
            'npsn_sekolah_asal',
            'tahun_lulus',
            'nilai_semester_1',
            'nilai_semester_2',
            'nilai_semester_3',
            'nilai_semester_4',
            'nilai_semester_5',
            'nama_prestasi',
            'tingkat_prestasi',
            'tahun_prestasi',
            'penyelenggara_prestasi',
            'nama_ayah',
            'nik_ayah',
            'pekerjaan_ayah',
            'nama_ibu',
            'nik_ibu',
            'pekerjaan_ibu',
            'nama_wali',
            'nik_wali',
            'pekerjaan_wali',
            'penghasilan_ortu',
            'no_telp_ortu',
            'sekolah_pilihan_1',
            'jenjang_pilihan',
            'jurusan_id',
            'jurusan_pilihan_1',
            'sekolah_pilihan_2',
            'jurusan_pilihan_2',
            'catatan',
            'ijazah_skl',
            'kartu_keluarga',
            'akta_kelahiran',
            'ktp_ayah',
            'ktp_ibu',
            'buku_rapor',
            'pas_foto',
            'dokumen_gabungan',
            'aiExtractedFields',
            'aiLastDocument',
        ]);

        $this->document_upload_mode = 'terpisah';
        $this->status = 'Baru';
        $this->existingFiles = [];
        $this->resetValidation();
    }

    public function resetGelombangForm(): void
    {
        $this->gelombangEditId = null;
        $this->gelombang_tahun_pendaftaran = (string) now()->year;
        $this->gelombang_nama = '';
        $this->gelombang_tanggal_mulai = '';
        $this->gelombang_tanggal_selesai = '';
        $this->gelombang_status = 'Draft';
        $this->gelombang_deskripsi = '';
        $this->resetValidation();
    }

    public function edit(int $id): void
    {
        $this->resetForm();

        $item = PendaftaranMuridBaru::findOrFail($id);
        $this->editId = $item->id;
        $this->ppdb_gelombang_id = (string) ($item->ppdb_gelombang_id ?? '');

        foreach ($this->formFields() as $field) {
            if (property_exists($this, $field)) {
                $this->{$field} = (string) ($item->{$field} ?? '');
            }
        }

        $this->tanggal_lahir = optional($item->tanggal_lahir)->format('Y-m-d') ?? '';
        $this->document_upload_mode = 'terpisah';
        $this->status = $item->status;

        $nilai = $item->nilai_rapor ?? [];
        $this->nilai_semester_1 = (string) ($nilai['semester_1'] ?? '');
        $this->nilai_semester_2 = (string) ($nilai['semester_2'] ?? '');
        $this->nilai_semester_3 = (string) ($nilai['semester_3'] ?? '');
        $this->nilai_semester_4 = (string) ($nilai['semester_4'] ?? '');
        $this->nilai_semester_5 = (string) ($nilai['semester_5'] ?? '');

        $this->existingFiles = [
            'ijazah_skl' => $item->ijazah_skl,
            'kartu_keluarga' => $item->kartu_keluarga,
            'akta_kelahiran' => $item->akta_kelahiran,
            'ktp_ayah' => $item->ktp_ayah,
            'ktp_ibu' => $item->ktp_ibu,
            'buku_rapor' => $item->buku_rapor,
            'pas_foto' => $item->pas_foto,
            'dokumen_gabungan' => $item->dokumen_gabungan,
        ];

        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'pendaftaran-murid-baru-form');
    }

    public function editGelombang(int $id): void
    {
        $this->resetGelombangForm();

        $item = PpdbGelombang::findOrFail($id);
        $this->gelombangEditId = $item->id;
        $this->gelombang_tahun_pendaftaran = (string) $item->tahun_pendaftaran;
        $this->gelombang_nama = $item->nama_gelombang;
        $this->gelombang_tanggal_mulai = optional($item->tanggal_mulai)->format('Y-m-d') ?? '';
        $this->gelombang_tanggal_selesai = optional($item->tanggal_selesai)->format('Y-m-d') ?? '';
        $this->gelombang_status = $item->status;
        $this->gelombang_deskripsi = $item->deskripsi ?? '';

        $this->isGelombangModalOpen = true;
        $this->dispatch('open-modal', 'gelombang-ppdb-form');
    }

    public function saveGelombang(): void
    {
        $this->validate([
            'gelombang_tahun_pendaftaran' => 'required|integer|min:2000|max:' . (now()->year + 5),
            'gelombang_nama' => 'required|string|max:100',
            'gelombang_tanggal_mulai' => 'nullable|date',
            'gelombang_tanggal_selesai' => 'nullable|date|after_or_equal:gelombang_tanggal_mulai',
            'gelombang_status' => 'required|in:Draft,Dibuka,Ditutup',
            'gelombang_deskripsi' => 'nullable|string|max:1000',
        ]);

        $duplicateExists = PpdbGelombang::where('tahun_pendaftaran', $this->gelombang_tahun_pendaftaran)
            ->where('nama_gelombang', $this->gelombang_nama)
            ->when($this->gelombangEditId, fn($query) => $query->where('id', '!=', $this->gelombangEditId))
            ->exists();

        if ($duplicateExists) {
            $this->addError('gelombang_nama', 'Nama gelombang sudah digunakan pada tahun pendaftaran ini.');

            return;
        }

        $existingStatus = null;
        $existingGelombang = null;
        if ($this->gelombangEditId) {
            $existingGelombang = PpdbGelombang::findOrFail($this->gelombangEditId);
            $existingStatus = $existingGelombang->status;
        }

        $gelombang = PpdbGelombang::updateOrCreate(
            ['id' => $this->gelombangEditId],
            [
                'tahun_pendaftaran' => $this->gelombang_tahun_pendaftaran,
                'nama_gelombang' => $this->gelombang_nama,
                'tanggal_mulai' => $this->gelombang_tanggal_mulai ?: null,
                'tanggal_selesai' => $this->gelombang_tanggal_selesai ?: null,
                'status' => $this->gelombang_status,
                'deskripsi' => $this->gelombang_deskripsi ?: null,
                'dibuka_pada' => $this->gelombang_status === 'Dibuka'
                    ? ($existingGelombang?->dibuka_pada ?? now())
                    : $existingGelombang?->dibuka_pada,
                'ditutup_pada' => $this->gelombang_status === 'Ditutup'
                    ? ($existingGelombang?->ditutup_pada ?? now())
                    : ($this->gelombang_status === 'Dibuka' ? null : $existingGelombang?->ditutup_pada),
            ]
        );

        $this->recordGelombangHistory(
            $gelombang,
            $existingStatus ? 'Diperbarui' : 'Dibuat',
            $existingStatus,
            $this->gelombang_status,
            $existingStatus === $this->gelombang_status ? 'Data gelombang diperbarui.' : 'Status gelombang berubah saat disimpan.'
        );

        $this->closeModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Gelombang PPDB berhasil disimpan.']);
    }

    public function openGelombang(int $id): void
    {
        $this->updateGelombangStatus($id, 'Dibuka');
    }

    public function closeGelombang(int $id): void
    {
        $this->updateGelombangStatus($id, 'Ditutup');
    }

    public function extractFromDocument(string $field, PpdbDocumentExtractionService $extractor, AddressGeocodingService $geocoder): void
    {
        if (! array_key_exists($field, $this->fileFields())) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Jenis berkas tidak dikenal.']);

            return;
        }

        try {
            $file = $this->resolveDocumentForExtraction($field);
            $data = $extractor->extract($field, $file['path'], $file['filename'], $file['mime'], [
                'nama_lengkap' => $this->nama_lengkap,
            ]);
            $filled = $this->applyExtractedData($data, $field);
            $coordinateFilled = $this->fillCoordinateFromKkAddress($field, $geocoder);

            $this->aiExtractedFields = array_values(array_filter(array_keys($data), fn($key) => filled($data[$key] ?? null)));
            $this->aiLastDocument = $this->documentLabel($field);

            $message = $filled > 0
                ? "AI berhasil mengisi {$filled} field dari {$this->aiLastDocument}. Silakan cek ulang sebelum simpan."
                : "AI membaca {$this->aiLastDocument}, tetapi tidak menemukan field baru yang bisa diisi.";

            if ($coordinateFilled) {
                $message .= ' Titik koordinat juga berhasil dicari dari alamat KK.';
            }

            if (filled($data['catatan_ai'] ?? '')) {
                $this->catatan = trim($this->catatan . "\nCatatan AI ({$this->aiLastDocument}): " . $data['catatan_ai']);
            }

            $this->dispatch('notify', ['type' => 'success', 'message' => $message]);
        } catch (\Throwable $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Gagal membaca dokumen dengan AI: ' . $e->getMessage(),
            ]);
        }
    }

    public function save(): void
    {
        $this->document_upload_mode = 'terpisah';
        $this->validate($this->rules());

        $data = [];
        foreach ($this->formFields() as $field) {
            $data[$field] = $this->{$field} !== '' ? $this->{$field} : null;
        }

        $data['tanggal_lahir'] = $this->tanggal_lahir ?: null;
        $data['ppdb_gelombang_id'] = $this->ppdb_gelombang_id ?: null;
        $data['tanggal_terbit_kk'] = $this->tanggal_terbit_kk ?: null;
        $data['tahun_lulus'] = $this->tahun_lulus ?: null;
        $data['tahun_prestasi'] = $this->tahun_prestasi ?: null;
        $data['document_upload_mode'] = $this->document_upload_mode;
        $data['status'] = $this->status;
        $data['catatan'] = $this->catatan ?: null;
        $data['nilai_rapor'] = [
            'semester_1' => $this->nilai_semester_1 ?: null,
            'semester_2' => $this->nilai_semester_2 ?: null,
            'semester_3' => $this->nilai_semester_3 ?: null,
            'semester_4' => $this->nilai_semester_4 ?: null,
            'semester_5' => $this->nilai_semester_5 ?: null,
        ];

        foreach ($this->fileFields() as $field => $folder) {
            $data[$field] = $this->existingFiles[$field] ?? null;

            if ($this->{$field}) {
                $data[$field] = $this->{$field}->store($folder, 'public');

                if (! empty($this->existingFiles[$field])) {
                    Storage::disk('public')->delete($this->existingFiles[$field]);
                }
            }
        }

        $this->deleteUnusedModeFiles($data);

        PendaftaranMuridBaru::updateOrCreate(['id' => $this->editId], $data);

        $this->closeModal();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->editId ? 'Data pendaftaran berhasil diperbarui.' : 'Data pendaftaran murid baru berhasil ditambahkan.',
        ]);
    }

    public function confirmDelete(int $id): void
    {
        $this->idBeingDeleted = $id;
        $this->dispatch('open-modal', 'confirm-delete-pendaftaran-modal');
    }

    public function delete(): void
    {
        if (! $this->idBeingDeleted) {
            return;
        }

        $item = PendaftaranMuridBaru::findOrFail($this->idBeingDeleted);
        foreach (array_keys($this->fileFields()) as $field) {
            if ($item->{$field}) {
                Storage::disk('public')->delete($item->{$field});
            }
        }

        $item->delete();
        $this->closeModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Data pendaftaran berhasil dihapus.']);
    }

    private function rules(): array
    {
        $id = $this->editId ? ',' . $this->editId : '';

        return [
            'nama_lengkap' => 'required|string|max:255',
            'ppdb_gelombang_id' => 'nullable|exists:ppdb_gelombangs,id',
            'nisn' => 'nullable|string|max:20|unique:pendaftaran_murid_barus,nisn' . $id,
            'nik' => 'nullable|string|max:20|unique:pendaftaran_murid_barus,nik' . $id,
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:Laki-Laki,Perempuan',
            'agama' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya',
            'no_hp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'alamat' => 'nullable|string|max:1000',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'dusun' => 'nullable|string|max:100',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kota_kabupaten' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'no_kk' => 'nullable|string|max:30',
            'tanggal_terbit_kk' => 'nullable|date',
            'koordinat_rumah' => 'nullable|string|max:100',
            'sekolah_asal' => 'nullable|string|max:150',
            'npsn_sekolah_asal' => 'nullable|string|max:20',
            'tahun_lulus' => 'nullable|integer|min:2000|max:' . (now()->year + 1),
            'nilai_semester_1' => 'nullable|numeric|min:0|max:100',
            'nilai_semester_2' => 'nullable|numeric|min:0|max:100',
            'nilai_semester_3' => 'nullable|numeric|min:0|max:100',
            'nilai_semester_4' => 'nullable|numeric|min:0|max:100',
            'nilai_semester_5' => 'nullable|numeric|min:0|max:100',
            'nama_prestasi' => 'nullable|string|max:150',
            'tingkat_prestasi' => 'nullable|string|max:100',
            'tahun_prestasi' => 'nullable|integer|min:2000|max:' . (now()->year + 1),
            'penyelenggara_prestasi' => 'nullable|string|max:150',
            'nama_ayah' => 'nullable|string|max:150',
            'nik_ayah' => 'nullable|string|max:20',
            'pekerjaan_ayah' => 'nullable|string|max:100',
            'nama_ibu' => 'nullable|string|max:150',
            'nik_ibu' => 'nullable|string|max:20',
            'pekerjaan_ibu' => 'nullable|string|max:100',
            'nama_wali' => 'nullable|string|max:150',
            'nik_wali' => 'nullable|string|max:20',
            'pekerjaan_wali' => 'nullable|string|max:100',
            'penghasilan_ortu' => 'nullable|string|max:100',
            'no_telp_ortu' => 'nullable|string|max:30',
            'sekolah_pilihan_1' => 'nullable|string|max:150',
            'jenjang_pilihan' => 'nullable|in:SMP,SMA,SMK',
            'jurusan_id' => 'nullable|required_if:jenjang_pilihan,SMK|exists:jurusans,id',
            'jurusan_pilihan_1' => 'nullable|string|max:100',
            'sekolah_pilihan_2' => 'nullable|string|max:150',
            'jurusan_pilihan_2' => 'nullable|string|max:100',
            'document_upload_mode' => 'required|in:terpisah',
            'status' => 'required|in:Baru,Diperiksa,Lengkap,Diterima,Ditolak,Daftar Ulang,Aktif',
            'catatan' => 'nullable|string|max:1000',
            'ijazah_skl' => 'nullable|file|mimes:pdf,jpg,jpeg|max:5120',
            'kartu_keluarga' => 'nullable|file|mimes:pdf,jpg,jpeg|max:5120',
            'akta_kelahiran' => 'nullable|file|mimes:pdf,jpg,jpeg|max:5120',
            'ktp_ayah' => 'nullable|file|mimes:pdf,jpg,jpeg|max:5120',
            'ktp_ibu' => 'nullable|file|mimes:pdf,jpg,jpeg|max:5120',
            'buku_rapor' => 'nullable|file|mimes:pdf,jpg,jpeg|max:10240',
            'pas_foto' => 'nullable|file|mimes:pdf,jpg,jpeg|max:2048',
            'dokumen_gabungan' => 'nullable|file|mimes:pdf|max:20480',
        ];
    }

    private function formFields(): array
    {
        return [
            'nama_lengkap',
            'nisn',
            'nik',
            'tempat_lahir',
            'jenis_kelamin',
            'agama',
            'no_hp',
            'email',
            'alamat',
            'rt',
            'rw',
            'dusun',
            'kelurahan',
            'kecamatan',
            'kota_kabupaten',
            'provinsi',
            'no_kk',
            'tanggal_terbit_kk',
            'koordinat_rumah',
            'sekolah_asal',
            'npsn_sekolah_asal',
            'tahun_lulus',
            'nama_prestasi',
            'tingkat_prestasi',
            'tahun_prestasi',
            'penyelenggara_prestasi',
            'nama_ayah',
            'nik_ayah',
            'pekerjaan_ayah',
            'nama_ibu',
            'nik_ibu',
            'pekerjaan_ibu',
            'nama_wali',
            'nik_wali',
            'pekerjaan_wali',
            'penghasilan_ortu',
            'no_telp_ortu',
            'sekolah_pilihan_1',
            'jenjang_pilihan',
            'jurusan_id',
            'jurusan_pilihan_1',
            'sekolah_pilihan_2',
            'jurusan_pilihan_2',
        ];
    }

    private function fileFields(): array
    {
        return [
            'ijazah_skl' => 'ppdb/ijazah-skl',
            'kartu_keluarga' => 'ppdb/kartu-keluarga',
            'akta_kelahiran' => 'ppdb/akta-kelahiran',
            'ktp_ayah' => 'ppdb/ktp-ayah',
            'ktp_ibu' => 'ppdb/ktp-ibu',
            'buku_rapor' => 'ppdb/buku-rapor',
            'pas_foto' => 'ppdb/pas-foto',
            'dokumen_gabungan' => 'ppdb/dokumen-gabungan',
        ];
    }

    private function resolveDocumentForExtraction(string $field): array
    {
        if ($this->{$field}) {
            return [
                'path' => $this->{$field}->getRealPath(),
                'filename' => $this->{$field}->getClientOriginalName(),
                'mime' => $this->{$field}->getMimeType() ?: 'application/octet-stream',
            ];
        }

        $storedPath = $this->existingFiles[$field] ?? null;

        if ($storedPath && Storage::disk('public')->exists($storedPath)) {
            $path = Storage::disk('public')->path($storedPath);

            return [
                'path' => $path,
                'filename' => basename($storedPath),
                'mime' => mime_content_type($path) ?: 'application/octet-stream',
            ];
        }

        throw new \RuntimeException('Upload atau simpan berkas terlebih dahulu.');
    }

    private function applyExtractedData(array $data, string $sourceField): int
    {
        $fillableFields = [
            'nama_lengkap',
            'nisn',
            'nik',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'agama',
            'alamat',
            'rt',
            'rw',
            'dusun',
            'kelurahan',
            'kecamatan',
            'kota_kabupaten',
            'provinsi',
            'no_kk',
            'sekolah_asal',
            'npsn_sekolah_asal',
            'tahun_lulus',
            'nama_ayah',
            'nik_ayah',
            'pekerjaan_ayah',
            'nama_ibu',
            'nik_ibu',
            'pekerjaan_ibu',
            'nama_wali',
            'nik_wali',
            'pekerjaan_wali',
        ];

        $filled = 0;
        $skipFields = $sourceField === 'kartu_keluarga'
            ? ['nama_lengkap', 'nisn', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'sekolah_asal', 'npsn_sekolah_asal', 'tahun_lulus']
            : [];
        $parentFields = [
            'nama_ayah',
            'nik_ayah',
            'pekerjaan_ayah',
            'nama_ibu',
            'nik_ibu',
            'pekerjaan_ibu',
            'nama_wali',
            'nik_wali',
            'pekerjaan_wali',
        ];
        $canFillParentFromKk = $sourceField === 'kartu_keluarga' && filled($this->nama_lengkap);

        foreach ($fillableFields as $field) {
            if (in_array($field, $skipFields, true)) {
                continue;
            }

            if (in_array($field, $parentFields, true) && ! $canFillParentFromKk) {
                continue;
            }

            $value = trim((string) Arr::get($data, $field, ''));

            if ($value === '') {
                continue;
            }

            if (in_array($field, ['tanggal_lahir'], true)) {
                $value = $this->normalizeDate($value);
            }

            if ($field === 'jenis_kelamin') {
                $value = $this->normalizeGender($value);
            }

            if ($field === 'agama') {
                $value = $this->normalizeReligion($value);
            }

            if (property_exists($this, $field) && filled($value) && ($canFillParentFromKk && in_array($field, $parentFields, true))) {
                $this->{$field} = $value;
                $filled++;

                continue;
            }

            if (property_exists($this, $field) && blank($this->{$field}) && filled($value)) {
                $this->{$field} = $value;
                $filled++;
            }
        }

        return $filled;
    }

    private function fillCoordinateFromKkAddress(string $sourceField, AddressGeocodingService $geocoder): bool
    {
        if ($sourceField !== 'kartu_keluarga' || filled($this->koordinat_rumah)) {
            return false;
        }

        $coordinate = $geocoder->geocode([
            'alamat' => $this->alamat,
            'kelurahan' => $this->kelurahan,
            'kecamatan' => $this->kecamatan,
            'kota_kabupaten' => $this->kota_kabupaten,
            'provinsi' => $this->provinsi,
        ]);

        if (blank($coordinate)) {
            return false;
        }

        $this->koordinat_rumah = $coordinate;

        return true;
    }

    private function normalizeDate(string $value): string
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return '';
        }
    }

    private function normalizeGender(string $value): string
    {
        $value = Str::lower($value);

        if (Str::contains($value, ['laki', 'pria', 'male'])) {
            return 'Laki-Laki';
        }

        if (Str::contains($value, ['perempuan', 'wanita', 'female'])) {
            return 'Perempuan';
        }

        return '';
    }

    private function normalizeReligion(string $value): string
    {
        $allowed = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'];
        $lower = Str::lower($value);

        foreach ($allowed as $religion) {
            if (Str::contains($lower, Str::lower($religion))) {
                return $religion;
            }
        }

        return '';
    }

    private function documentLabel(string $field): string
    {
        return [
            'ijazah_skl' => 'Ijazah/SKL',
            'kartu_keluarga' => 'Kartu Keluarga',
            'dokumen_gabungan' => 'PDF gabungan',
        ][$field] ?? $field;
    }

    private function deleteUnusedModeFiles(array &$data): void
    {
        $separateFields = ['ijazah_skl', 'kartu_keluarga', 'akta_kelahiran', 'ktp_ayah', 'ktp_ibu', 'buku_rapor', 'pas_foto'];
        $fieldsToDelete = $this->document_upload_mode === 'gabungan' ? $separateFields : ['dokumen_gabungan'];

        foreach ($fieldsToDelete as $field) {
            if (! empty($this->existingFiles[$field])) {
                Storage::disk('public')->delete($this->existingFiles[$field]);
            }

            $data[$field] = null;
        }
    }

    private function updateGelombangStatus(int $id, string $status): void
    {
        $gelombang = PpdbGelombang::findOrFail($id);
        $oldStatus = $gelombang->status;

        $updates = ['status' => $status];

        if ($status === 'Dibuka') {
            $updates['dibuka_pada'] = now();
            $updates['ditutup_pada'] = null;
        }

        if ($status === 'Ditutup') {
            $updates['ditutup_pada'] = now();
        }

        $gelombang->update($updates);

        $this->recordGelombangHistory(
            $gelombang,
            $status === 'Dibuka' ? 'Dibuka' : 'Ditutup',
            $oldStatus,
            $status,
            $status === 'Dibuka' ? 'Gelombang dibuka untuk pendaftaran.' : 'Gelombang ditutup dari pendaftaran.'
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $status === 'Dibuka' ? 'Gelombang PPDB dibuka.' : 'Gelombang PPDB ditutup.',
        ]);
    }

    private function recordGelombangHistory(PpdbGelombang $gelombang, string $aksi, ?string $before, ?string $after, ?string $catatan = null): void
    {
        PpdbGelombangRiwayat::create([
            'ppdb_gelombang_id' => $gelombang->id,
            'user_id' => auth()->id(),
            'aksi' => $aksi,
            'status_sebelum' => $before,
            'status_sesudah' => $after,
            'catatan' => $catatan,
            'terjadi_pada' => now(),
        ]);
    }

    private function statusOptions(): array
    {
        return [
            'Baru' => 'Baru',
            'Diperiksa' => 'Diperiksa',
            'Lengkap' => 'Lengkap',
            'Diterima' => 'Diterima',
            'Ditolak' => 'Ditolak',
            'Daftar Ulang' => 'Daftar Ulang',
            'Aktif' => 'Aktif',
        ];
    }

    /**
     * Konversi pendaftar PPDB berstatus "Diterima" menjadi Siswa aktif.
     * Membuat User, Siswa, dan memasukkan ke Rombel tahun ajaran aktif.
     */
    public function konversiKeSiswa(int $id): void
    {
        $ppdb = PendaftaranMuridBaru::with('gelombang')->findOrFail($id);

        if ($ppdb->status !== 'Diterima') {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Hanya pendaftar dengan status "Diterima" yang dapat dikonversi menjadi siswa.',
            ]);

            return;
        }

        // Cek apakah NISN sudah terdaftar sebagai siswa
        if ($ppdb->nisn && Siswa::where('nisn', $ppdb->nisn)->exists()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "NISN {$ppdb->nisn} sudah terdaftar sebagai siswa aktif.",
            ]);

            return;
        }

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        DB::transaction(function () use ($ppdb, $tahunAjaranAktif) {
            // 1. Generate NIS (selalu auto-generate, tidak dari NISN)
            $lastNis = Siswa::whereNotNull('nis')
                ->where('nis', 'like', now()->format('Y') . '%')
                ->orderBy('nis', 'desc')
                ->value('nis');
            $nis = $lastNis
                ? (string) ((int) $lastNis + 1)
                : now()->format('Y') . '001';

            // 2. Buat User — cegah hijack akun existing
            $baseEmail = $ppdb->email ?: strtolower(Str::slug($ppdb->nama_lengkap, '')) . $nis . '@sekolah.sch.id';

            // Jika email sudah dipakai oleh orang lain (bukan siswa ini), buat email unik
            $existingUser = User::where('email', $baseEmail)->first();
            if ($existingUser && ! Siswa::where('user_id', $existingUser->id)->exists()) {
                // Email dipakai non-siswa (guru/admin) — buat email baru
                $baseEmail = strtolower(Str::slug($ppdb->nama_lengkap, '')) . $nis . '@sekolah.sch.id';
            }

            $password = $ppdb->tanggal_lahir
                ? Carbon::parse($ppdb->tanggal_lahir)->format('dmY')
                : 'siswa123';

            $user = User::firstOrCreate(
                ['email' => $baseEmail],
                [
                    'name' => $ppdb->nama_lengkap,
                    'password' => Hash::make($password),
                ]
            );

            if (! $user->hasRole('siswa')) {
                $user->assignRole('siswa');
            }

            // 3. Buat Siswa — NISN hanya diisi jika ada, JANGAN diisi dengan NIS
            $jenjang = $ppdb->jenjang_pilihan ?: 'SMP';
            $jurusanId = $ppdb->jurusan_id;

            $siswaKey = $ppdb->nisn
                ? ['nisn' => $ppdb->nisn]
                : ['user_id' => $user->id];

            $siswa = Siswa::updateOrCreate(
                $siswaKey,
                [
                    'user_id' => $user->id,
                    'nisn' => $ppdb->nisn ?: null, // NISN hanya diisi jika ada dari PPDB
                    'nis' => $nis,
                    'jenjang' => $jenjang,
                    'jurusan_id' => $jurusanId,
                    'kelas_id' => null,
                    'tempat_lahir' => $ppdb->tempat_lahir,
                    'tanggal_lahir' => $ppdb->tanggal_lahir,
                    'agama' => $ppdb->agama,
                    'jenis_kelamin' => $ppdb->jenis_kelamin,
                    'alamat' => $ppdb->alamat,
                    'nama_ayah' => $ppdb->nama_ayah,
                    'nama_ibu' => $ppdb->nama_ibu,
                    'pekerjaan_ayah' => $ppdb->pekerjaan_ayah,
                    'pekerjaan_ibu' => $ppdb->pekerjaan_ibu,
                    'no_telp_ortu' => $ppdb->no_telp_ortu,
                    'status' => 'Aktif',
                ]
            );

            // 4. Update status PPDB
            $ppdb->update(['status' => 'Aktif']);
        });

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$ppdb->nama_lengkap} berhasil dikonversi menjadi siswa aktif. Silakan tempatkan ke kelas melalui menu Data Siswa.",
        ]);
    }
}
