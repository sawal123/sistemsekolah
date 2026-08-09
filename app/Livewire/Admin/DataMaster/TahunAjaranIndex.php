<?php

namespace App\Livewire\Admin\DataMaster;

use App\Models\AnggotaRombel;
use App\Models\Kelas;
use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Tahun Ajaran & Semester')]
class TahunAjaranIndex extends Component
{
    use WithPagination;

    public $isModalOpen = false;

    public $editId = null;

    // Form fields
    public $tahun;

    public $semester;

    public $tanggal_mulai;

    public $tanggal_selesai;

    public $status = 'Draft';

    public $is_active = false;

    public $idBeingDeleted = null;

    public $deleteErrorMessage = null;

    // ── Kenaikan Kelas ───────────────────────────────────────
    public bool $showKenaikanModal = false;

    public ?int $targetTahunAjaranId = null;

    public array $kenaikanPreview = [];

    public bool $showSalinRombelModal = false;

    public string $salinRombelMessage = '';

    public ?int $salinTargetTahunAjaranId = null;

    public function render()
    {
        // Mengambil data terbaru dan membaginya 10 baris per halaman
        $tahunAjarans = TahunAjaran::latest()->paginate(10);

        return view('livewire.admin.data-master.tahun-ajaran-index', [
            'tahunAjarans' => $tahunAjarans,
        ]);
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'tahun-ajaran-form');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->idBeingDeleted = null;
        $this->dispatch('close-modal', 'tahun-ajaran-form');
        $this->dispatch('close-modal', 'confirm-delete-modal');
    }

    public function resetForm()
    {
        $this->tahun = '';
        $this->semester = 'Ganjil';
        $this->tanggal_mulai = '';
        $this->tanggal_selesai = '';
        $this->status = 'Draft';
        $this->is_active = false;
        $this->editId = null;
        $this->idBeingDeleted = null;
        $this->deleteErrorMessage = null;
    }

    public function edit($id)
    {
        $this->resetForm();
        $item = TahunAjaran::findOrFail($id);
        $this->editId = $id;
        $this->tahun = $item->tahun;
        $this->semester = $item->semester;
        $this->tanggal_mulai = $item->tanggal_mulai ? $item->tanggal_mulai->format('Y-m-d') : '';
        $this->tanggal_selesai = $item->tanggal_selesai ? $item->tanggal_selesai->format('Y-m-d') : '';
        $this->status = $item->status;
        $this->is_active = (bool) $item->is_active;
        $this->isModalOpen = true;
        $this->dispatch('open-modal', 'tahun-ajaran-form');
    }

    public function toggleStatus($id)
    {
        $item = TahunAjaran::findOrFail($id);

        if (! $item->is_active) {
            // Nonaktifkan periode lain: set status Ditutup + is_active false
            TahunAjaran::where('id', '!=', $id)
                ->where('is_active', true)
                ->update(['is_active' => false, 'status' => 'Ditutup']);

            $item->update(['is_active' => true, 'status' => 'Aktif']);

            // Cek tawarkan salin rombel
            $semesterSebelumnya = $item->semester === 'Ganjil' ? 'Genap' : 'Ganjil';
            $taSebelumnya = TahunAjaran::where('tahun', $item->tahun)
                ->where('semester', $semesterSebelumnya)
                ->first();
            $hasRombelSebelumnya = $taSebelumnya && Rombel::where('tahun_ajaran_id', $taSebelumnya->id)->exists();
            $hasRombelSendiri = Rombel::where('tahun_ajaran_id', $item->id)->exists();

            if (! $hasRombelSendiri && $hasRombelSebelumnya) {
                $this->salinTargetTahunAjaranId = $item->id;
                $this->salinRombelMessage = "Semester {$item->semester} {$item->tahun} telah diaktifkan, tetapi belum memiliki data rombel. Salin rombel dari semester {$semesterSebelumnya}?";
                $this->showSalinRombelModal = true;
                $this->dispatch('open-modal', 'salin-rombel-modal');
                $msg = 'Status Tahun Ajaran diaktifkan! Anda dapat menyalin rombel dari semester sebelumnya.';
            } else {
                $msg = 'Status Tahun Ajaran diaktifkan!';
            }
        } else {
            $item->update([
                'is_active' => false,
                'status' => $item->status === 'Aktif' ? 'Ditutup' : $item->status,
            ]);
            $msg = 'Status Tahun Ajaran dinonaktifkan!';
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $msg,
        ]);
    }

    public function save()
    {
        $this->validate([
            'tahun' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:Draft,Aktif,Ditutup,Diarsipkan',
        ]);

        // status === 'Aktif' adalah satu-satunya sumber kebenaran untuk is_active
        $isActive = $this->status === 'Aktif';

        if ($this->editId) {
            $item = TahunAjaran::findOrFail($this->editId);

            if ($isActive && ! $item->is_active) {
                // Nonaktifkan periode lain: set status Ditutup + is_active false
                TahunAjaran::where('id', '!=', $item->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false, 'status' => 'Ditutup']);
            }

            $item->update([
                'tahun' => $this->tahun,
                'semester' => $this->semester,
                'tanggal_mulai' => $this->tanggal_mulai ?: null,
                'tanggal_selesai' => $this->tanggal_selesai ?: null,
                'status' => $this->status,
                'is_active' => $isActive,
            ]);
        } else {
            if ($isActive) {
                TahunAjaran::where('is_active', true)
                    ->update(['is_active' => false, 'status' => 'Ditutup']);
            }

            $item = TahunAjaran::create([
                'tahun' => $this->tahun,
                'semester' => $this->semester,
                'tanggal_mulai' => $this->tanggal_mulai ?: null,
                'tanggal_selesai' => $this->tanggal_selesai ?: null,
                'status' => $this->status,
                'is_active' => $isActive,
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->editId ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.',
        ]);
    }

    public function confirmDelete($id)
    {
        $item = TahunAjaran::findOrFail($id);

        if (! $item->canBeDeleted()) {
            $this->deleteErrorMessage = "Tahun ajaran {$item->tahun} - {$item->semester} tidak dapat dihapus karena berstatus '{$item->status}'. Hanya tahun ajaran dengan status 'Draft' yang boleh dihapus. Ubah status menjadi 'Ditutup' atau 'Diarsipkan' jika sudah tidak digunakan.";
            $this->dispatch('open-modal', 'cannot-delete-modal');

            return;
        }

        $this->deleteErrorMessage = null;
        $this->idBeingDeleted = $id;
        $this->dispatch('open-modal', 'confirm-delete-modal');
    }

    public function delete()
    {
        if (! $this->idBeingDeleted) {
            return;
        }

        $item = TahunAjaran::findOrFail($this->idBeingDeleted);

        if (! $item->canBeDeleted()) {
            $this->closeModal();
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Tahun ajaran ini tidak dapat dihapus karena sudah memiliki data transaksi.',
            ]);

            return;
        }

        $item->delete();
        $this->closeModal();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Data berhasil dihapus.',
        ]);
    }

    // ══════════════════════════════════════════════════════════
    //  Fitur: Salin Rombel dari Semester Sebelumnya
    // ══════════════════════════════════════════════════════════

    public function salinRombelDariSemesterSebelumnya(): void
    {
        if (! $this->salinTargetTahunAjaranId) {
            return;
        }

        $targetTa = TahunAjaran::findOrFail($this->salinTargetTahunAjaranId);
        $semesterSebelumnya = $targetTa->semester === 'Ganjil' ? 'Genap' : 'Ganjil';
        $sourceTa = TahunAjaran::where('tahun', $targetTa->tahun)
            ->where('semester', $semesterSebelumnya)
            ->first();

        if (! $sourceTa) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak ditemukan semester sebelumnya.']);

            return;
        }

        $rombelsSumber = Rombel::with(['anggotaRombels', 'kelas'])
            ->where('tahun_ajaran_id', $sourceTa->id)
            ->get();

        if ($rombelsSumber->isEmpty()) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Semester sebelumnya tidak memiliki data rombel.']);

            return;
        }

        DB::transaction(function () use ($targetTa, $rombelsSumber) {
            foreach ($rombelsSumber as $rombelLama) {
                $rombelBaru = Rombel::firstOrCreate(
                    [
                        'kelas_id' => $rombelLama->kelas_id,
                        'tahun_ajaran_id' => $targetTa->id,
                    ],
                    [
                        'wali_kelas_id' => $rombelLama->wali_kelas_id,
                        'kapasitas' => $rombelLama->kapasitas,
                        'status' => 'Aktif',
                    ]
                );

                // Salin anggota rombel (siswa yang masih aktif)
                foreach ($rombelLama->anggotaRombels as $anggota) {
                    AnggotaRombel::firstOrCreate(
                        [
                            'siswa_id' => $anggota->siswa_id,
                            'rombel_id' => $rombelBaru->id,
                        ],
                        [
                            'status' => 'Aktif',
                            'tanggal_masuk' => now()->toDateString(),
                            'tanggal_keluar' => null,
                        ]
                    );

                    // Update kelas_id di tabel siswas untuk kelas aktif
                    Siswa::where('id', $anggota->siswa_id)
                        ->update(['kelas_id' => $rombelLama->kelas_id]);
                }
            }
        });

        $this->showSalinRombelModal = false;
        $this->salinTargetTahunAjaranId = null;
        $this->dispatch('close-modal', 'salin-rombel-modal');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$rombelsSumber->count()} rombel berhasil disalin ke {$targetTa->semester} {$targetTa->tahun}.",
        ]);
    }

    public function tolakSalinRombel(): void
    {
        $this->showSalinRombelModal = false;
        $this->salinTargetTahunAjaranId = null;
        $this->dispatch('close-modal', 'salin-rombel-modal');
    }

    // ══════════════════════════════════════════════════════════
    //  Fitur: Kenaikan Kelas (untuk Tahun Ajaran Baru)
    // ══════════════════════════════════════════════════════════

    public function previewKenaikanKelas(int $targetTahunAjaranId): void
    {
        $targetTa = TahunAjaran::findOrFail($targetTahunAjaranId);
        $this->targetTahunAjaranId = $targetTa->id;

        // Cari tahun ajaran sebelumnya (aktif saat ini atau yang terakhir)
        $taAktif = TahunAjaran::where('is_active', true)
            ->where('id', '!=', $targetTa->id)
            ->first();

        if (! $taAktif) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak ada tahun ajaran aktif sebagai sumber kenaikan.']);

            return;
        }

        $rombelsAktif = Rombel::with(['kelas', 'anggotaRombels.siswa.user'])
            ->where('tahun_ajaran_id', $taAktif->id)
            ->get();

        if ($rombelsAktif->isEmpty()) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Tahun ajaran aktif tidak memiliki rombel.']);

            return;
        }

        // Mapping kenaikan: VII→VIII, VIII→IX, IX→(Lulus), X→XI, XI→XII, XII→(Lulus)
        $this->kenaikanPreview = [];
        $semuaKelas = Kelas::orderBy('jenjang')->orderBy('nama_kelas')->get();

        foreach ($rombelsAktif as $rombel) {
            $kelasAsal = $rombel->kelas;
            if (! $kelasAsal) {
                continue;
            }

            // Tentukan kelas tujuan berdasarkan jenjang dan angka
            $kelasTujuan = $this->cariKelasTujuan($kelasAsal, $semuaKelas);

            $this->kenaikanPreview[] = [
                'rombel_id' => $rombel->id,
                'kelas_asal' => $kelasAsal->nama_kelas,
                'jenjang' => $kelasAsal->jenjang,
                'kelas_tujuan' => $kelasTujuan?->nama_kelas,
                'kelas_tujuan_id' => $kelasTujuan?->id,
                'is_lulus' => $kelasTujuan === null,
                'jumlah_siswa' => $rombel->anggotaRombels->count(),
                'siswa' => $rombel->anggotaRombels->map(fn($a) => [
                    'id' => $a->siswa_id,
                    'nama' => $a->siswa?->user?->name ?? '-',
                ]),
            ];
        }

        $this->showKenaikanModal = true;
        $this->dispatch('open-modal', 'kenaikan-kelas-modal');
    }

    public function executeKenaikanKelas(): void
    {
        if (! $this->targetTahunAjaranId || empty($this->kenaikanPreview)) {
            return;
        }

        $targetTa = TahunAjaran::findOrFail($this->targetTahunAjaranId);

        DB::transaction(function () use ($targetTa) {
            foreach ($this->kenaikanPreview as $item) {
                if ($item['is_lulus'] || ! $item['kelas_tujuan_id']) {
                    // Siswa lulus — update status
                    Siswa::whereIn('id', collect($item['siswa'])->pluck('id'))
                        ->update(['status' => 'Lulus', 'tahun_lulus' => now()->year]);

                    continue;
                }

                // Buat/temukan rombel tujuan
                $rombelTujuan = Rombel::firstOrCreate(
                    [
                        'kelas_id' => $item['kelas_tujuan_id'],
                        'tahun_ajaran_id' => $targetTa->id,
                    ],
                    [
                        'status' => 'Aktif',
                    ]
                );

                // Pindahkan siswa ke rombel baru
                foreach ($item['siswa'] as $s) {
                    AnggotaRombel::firstOrCreate(
                        [
                            'siswa_id' => $s['id'],
                            'rombel_id' => $rombelTujuan->id,
                        ],
                        [
                            'status' => 'Aktif',
                            'tanggal_masuk' => now()->toDateString(),
                            'tanggal_keluar' => null,
                        ]
                    );

                    // Update kelas_id di siswas
                    Siswa::where('id', $s['id'])->update([
                        'kelas_id' => $item['kelas_tujuan_id'],
                    ]);
                }
            }
        });

        // Aktifkan tahun ajaran target, nonaktifkan yang lain
        TahunAjaran::where('id', '!=', $targetTa->id)
            ->where('is_active', true)
            ->update(['is_active' => false, 'status' => 'Ditutup']);
        $targetTa->update(['is_active' => true, 'status' => 'Aktif']);

        $this->showKenaikanModal = false;
        $this->kenaikanPreview = [];
        $this->targetTahunAjaranId = null;
        $this->dispatch('close-modal', 'kenaikan-kelas-modal');
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Kenaikan kelas berhasil! Tahun ajaran ' . $targetTa->tahun . ' ' . $targetTa->semester . ' telah diaktifkan.',
        ]);
    }

    public function batalKenaikanKelas(): void
    {
        $this->showKenaikanModal = false;
        $this->kenaikanPreview = [];
        $this->targetTahunAjaranId = null;
        $this->dispatch('close-modal', 'kenaikan-kelas-modal');
    }

    /**
     * Cari kelas tujuan kenaikan berdasarkan kelas asal.
     * SMP: VII→VIII, VIII→IX, IX→null (lulus)
     * SMA/SMK: X→XI, XI→XII, XII→null (lulus)
     */
    private function cariKelasTujuan(Kelas $kelasAsal, $semuaKelas): ?Kelas
    {
        $nama = $kelasAsal->nama_kelas;
        $jenjang = $kelasAsal->jenjang;

        if ($jenjang === 'SMP') {
            $map = ['7' => '8', '8' => '9', '9' => null, 'VII' => 'VIII', 'VIII' => 'IX', 'IX' => null];
        } else {
            $map = ['10' => '11', '11' => '12', '12' => null, 'X' => 'XI', 'XI' => 'XII', 'XII' => null];
        }

        foreach ($map as $dari => $ke) {
            if ($ke === null) {
                continue;
            }
            if (stripos($nama, (string) $dari) !== false) {
                $namaTujuan = str_ireplace((string) $dari, (string) $ke, $nama);

                return $semuaKelas->firstWhere('nama_kelas', $namaTujuan);
            }
        }

        return null;
    }
}
