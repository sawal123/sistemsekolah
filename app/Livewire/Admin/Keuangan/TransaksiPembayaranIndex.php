<?php

namespace App\Livewire\Admin\Keuangan;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Transaksi Pembayaran SPP')]
class TransaksiPembayaranIndex extends Component
{
    use WithPagination; // In case we need it later

    #[Url(as: 'siswa_id')]
    public ?int $urlSiswaId = null;

    // ── Search State ──────────────────────────────────────────
    public string $searchQuery = '';

    public bool $showDropdown = false;

    // ── Selected Student ──────────────────────────────────────
    public ?int $selectedSiswaId = null;

    public array $selectedSiswaData = [];

    public int $selectedTahun;

    // ── Payment Matrix & Selection ────────────────────────────
    public array $sppMatrix = [];

    // key: "{sppId}_{bulan|'sekali'}" → ['spp_id' => int, 'bulan' => int|null]
    public array $selectedItems = [];

    // ── Post-Payment ──────────────────────────────────────────
    public ?array $lastPembayaranIds = null;

    public bool $showKuitansiModal = false;

    public function mount(): void
    {
        $this->selectedTahun = (int) now()->year;

        if ($this->urlSiswaId) {
            $this->selectSiswa($this->urlSiswaId);
        }
    }

    // ── Computed: Live Search Results ──────────────────────────

    #[Computed]
    public function hasilPencarian(): array
    {
        if (strlen(trim($this->searchQuery)) < 2) {
            return [];
        }

        return Siswa::with(['kelas', 'user', 'jurusan'])
            ->where('status', 'Aktif')
            ->where(function ($q) {
                $q->where('nisn', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('nis', 'like', '%' . $this->searchQuery . '%')
                    ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', '%' . $this->searchQuery . '%'));
            })
            ->limit(8)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'nama' => $s->user?->name ?? '-',
                'nisn' => $s->nisn,
                'kelas' => $s->kelas?->nama_kelas ?? 'Belum Ada Kelas',
                'jenjang' => $s->jenjang,
                'jurusan' => $s->jurusan?->kode,
            ])
            ->toArray();
    }

    public function updatedSearchQuery(): void
    {
        $this->showDropdown = strlen(trim($this->searchQuery)) >= 2;
        unset($this->hasilPencarian); // reset computed cache
    }

    // ── Student Selection ─────────────────────────────────────

    public function selectSiswa(int $id): void
    {
        $siswa = Siswa::with(['kelas', 'user', 'jurusan'])->findOrFail($id);

        $this->selectedSiswaId = $id;
        $this->selectedSiswaData = [
            'id' => $siswa->id,
            'nama' => $siswa->user?->name ?? '-',
            'nisn' => $siswa->nisn,
            'nis' => $siswa->nis,
            'jenjang' => $siswa->jenjang,
            'jurusan_id' => $siswa->jurusan_id,
            'jurusan' => $siswa->jurusan?->kode,
            'kelas' => $siswa->kelas?->nama_kelas ?? 'Belum Ada Kelas',
            'status' => $siswa->status,
        ];

        $this->searchQuery = '';
        $this->showDropdown = false;
        $this->selectedItems = [];
        $this->loadPaymentMatrix();
    }

    public function clearSiswa(): void
    {
        $this->selectedSiswaId = null;
        $this->selectedSiswaData = [];
        $this->sppMatrix = [];
        $this->selectedItems = [];
        $this->lastPembayaranIds = null;
        $this->searchQuery = '';
        $this->showDropdown = false;
    }

    public function updatedSelectedTahun(): void
    {
        $this->selectedItems = [];
        $this->loadPaymentMatrix();
    }

    // ── Payment Matrix Loader ─────────────────────────────────

    public function loadPaymentMatrix(): void
    {
        if (! $this->selectedSiswaId) {
            return;
        }

        $siswa = Siswa::find($this->selectedSiswaId);
        if (! $siswa) {
            return;
        }

        $jenjang = $this->selectedSiswaData['jenjang'] ?? null;
        $jurusanId = $this->selectedSiswaData['jurusan_id'] ?? null;

        // ── Petakan setiap bulan ke SPP yang berlaku bulan tsb (via forDate) ──
        $sppPerBulan = [];   // bulan => spp_id
        $sppCache = [];      // spp_id => Spp model
        $taIdsSelTahun = []; // distinct tahun_ajaran_id yang menutupi tahun terpilih

        for ($b = 1; $b <= 12; $b++) {
            $tgl = Carbon::create($this->selectedTahun, $b, 1);
            $taBulan = TahunAjaran::forDate($tgl);
            if (! $taBulan) {
                continue;
            }
            $taIdsSelTahun[$taBulan->id] = true;
            $spp = Spp::where('tahun_ajaran_id', $taBulan->id)
                ->active()
                ->applicableTo($jenjang, $jurusanId)
                ->where('kategori', 'SPP Bulanan')
                ->orderByRaw('jurusan_id IS NULL')
                ->orderBy('id')
                ->first();
            if ($spp) {
                $sppPerBulan[$b] = $spp->id;
                $sppCache[$spp->id] = $spp;
            }
        }

        // Fallback: jika tidak ada SPP bulanan dari forDate, pakai TA aktif
        if (empty($sppCache)) {
            $tahunAjaran = TahunAjaran::where('is_active', true)->first();
            if ($tahunAjaran) {
                $taIdsSelTahun[$tahunAjaran->id] = true;
                $spp = Spp::where('tahun_ajaran_id', $tahunAjaran->id)
                    ->active()
                    ->applicableTo($jenjang, $jurusanId)
                    ->where('kategori', 'SPP Bulanan')
                    ->orderByRaw('jurusan_id IS NULL')
                    ->orderBy('id')
                    ->first();
                if ($spp) {
                    for ($b = 1; $b <= 12; $b++) {
                        $sppPerBulan[$b] = $spp->id;
                        $sppCache[$spp->id] = $spp;
                    }
                }
            }
        }

        $matrix = [];

        // ── Matriks SPP Bulanan: generate tagihan hanya utk bulan milik SPP tsb ──
        $bulanMatrix = [];
        foreach ($sppPerBulan as $b => $sppId) {
            $spp = $sppCache[$sppId];
            if (! isset($bulanMatrix[$sppId])) {
                $bulanMatrix[$sppId] = [
                    'spp' => $spp,
                    'bulans' => [],
                ];
            }

            $jatuhTempo = Carbon::create($this->selectedTahun, $b, 10)->toDateString();
            $tagihan = Tagihan::generateDariSpp($siswa, $spp, $this->selectedTahun, $b, $jatuhTempo);

            $bulanMatrix[$sppId]['bulans'][$b] = [
                'tagihan_id' => $tagihan->id,
                'lunas' => $tagihan->status === 'Lunas',
                'sebagian' => $tagihan->status === 'Lunas Sebagian',
                'total_terbayar' => $tagihan->total_terbayar,
                'sisa' => $tagihan->sisa_tagihan,
                'persentase' => $tagihan->persentase_terbayar,
                'nominal' => (float) $spp->nominal,
                'pembayaran_terakhir' => $tagihan->pembayarans()->latest()->first()?->tanggal_bayar?->format('d/m/Y'),
            ];
        }

        // Isi bulan yang bukan milik SPP tsb dengan placeholder non-payable
        // (view menampilkan grid 12 bulan per kartu)
        $placeholder = static fn() => [
            'tagihan_id' => null,
            'lunas' => true,
            'sebagian' => false,
            'total_terbayar' => 0,
            'sisa' => 0,
            'persentase' => 0,
            'nominal' => 0,
            'pembayaran_terakhir' => null,
            'available' => false,
        ];

        foreach ($bulanMatrix as $sppId => &$entry) {
            for ($b = 1; $b <= 12; $b++) {
                if (! isset($entry['bulans'][$b])) {
                    $entry['bulans'][$b] = $placeholder();
                }
            }
            ksort($entry['bulans']);
        }
        unset($entry);

        foreach ($bulanMatrix as $sppId => $entry) {
            $spp = $entry['spp'];
            $matrix[$sppId] = [
                'id' => $spp->id,
                'kategori' => $spp->kategori,
                'nominal' => (float) $spp->nominal,
                'is_bulanan' => true,
                'bulans' => $entry['bulans'],
            ];
        }

        // ── Tagihan Sekali Bayar ────────────────────────────────
        // Ambil ID TahunAjaran dari periode yang menutupi tahun kalender terpilih.
        // (Sebelumnya memakai spp_id sebagai tahun_ajaran_id → periode salah / tidak muncul.)
        $taIds = array_keys($taIdsSelTahun);
        $taAktifId = TahunAjaran::where('is_active', true)->value('id');

        $sekaliSpps = $taIds
            ? Spp::whereIn('tahun_ajaran_id', $taIds)
                ->active()
                ->applicableTo($jenjang, $jurusanId)
                ->where('kategori', '!=', 'SPP Bulanan')
                ->orderBy('kategori')
                ->orderByRaw('jurusan_id IS NULL')
                ->get()
                ->sortBy([
                    // Periode aktif diutamakan → kategori sama hanya muncul 1×, dari periode benar
                    fn($s) => $s->tahun_ajaran_id === $taAktifId ? 0 : 1,
                    fn($s) => -1 * (int) $s->id,
                ])
                ->unique('kategori')
                ->values()
            : collect();

        foreach ($sekaliSpps as $spp) {
            $tagihan = Tagihan::generateDariSpp($siswa, $spp, $this->selectedTahun);

            $matrix[$spp->id] = [
                'id' => $spp->id,
                'kategori' => $spp->kategori,
                'nominal' => (float) $spp->nominal,
                'is_bulanan' => false,
                'tagihan_id' => $tagihan->id,
                'lunas' => $tagihan->status === 'Lunas',
                'sebagian' => $tagihan->status === 'Lunas Sebagian',
                'total_terbayar' => $tagihan->total_terbayar,
                'sisa' => $tagihan->sisa_tagihan,
                'persentase' => $tagihan->persentase_terbayar,
                'pembayaran_terakhir' => $tagihan->pembayarans()->latest()->first()?->tanggal_bayar?->format('d/m/Y'),
            ];
        }

        $this->sppMatrix = $matrix;
    }

    // ── Item Selection ────────────────────────────────────────

    /**
     * Toggle pilih/batalkan bulan untuk dibayar.
     */
    public function toggleItem(int $sppId, $bulan): void
    {
        $key = $sppId . '_' . $bulan;

        if (isset($this->selectedItems[$key])) {
            unset($this->selectedItems[$key]);

            return;
        }

        if ($bulan === 'sekali') {
            $data = $this->sppMatrix[$sppId] ?? null;
            if ($data && ! ($data['lunas'] ?? false)) {
                $this->selectedItems[$key] = [
                    'spp_id' => $sppId,
                    'bulan' => null,
                    'tagihan_id' => $data['tagihan_id'],
                ];
            }
        } else {
            $bulanInt = (int) $bulan;
            $data = $this->sppMatrix[$sppId]['bulans'][$bulanInt] ?? null;
            if ($data && ! ($data['lunas'] ?? false)) {
                $this->selectedItems[$key] = [
                    'spp_id' => $sppId,
                    'bulan' => $bulanInt,
                    'tagihan_id' => $data['tagihan_id'],
                ];
            }
        }
    }

    /**
     * Pilih semua tunggakan dari bulan pertama hingga bulan berjalan.
     */
    public function bayarSemuaTunggakan(): void
    {
        $this->selectedItems = [];
        $currentMonth = now()->month;

        foreach ($this->sppMatrix as $sppId => $data) {
            if ($data['is_bulanan']) {
                for ($b = 1; $b <= $currentMonth; $b++) {
                    if (! $data['bulans'][$b]['lunas']) {
                        $this->selectedItems[$sppId . '_' . $b] = [
                            'spp_id' => (int) $sppId,
                            'bulan' => $b,
                            'tagihan_id' => $data['bulans'][$b]['tagihan_id'],
                        ];
                    }
                }
            } else {
                if (! $data['lunas']) {
                    $this->selectedItems[$sppId . '_sekali'] = [
                        'spp_id' => (int) $sppId,
                        'bulan' => null,
                        'tagihan_id' => $data['tagihan_id'],
                    ];
                }
            }
        }

        if (empty($this->selectedItems)) {
            $this->dispatch('notify', ['type' => 'info', 'message' => '✅ Semua tagihan sudah lunas!']);
        } else {
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => count($this->selectedItems) . ' item tunggakan dipilih.',
            ]);
        }
    }

    // ── Computed Totals ───────────────────────────────────────

    public function getTotalBayar(): float
    {
        $total = 0;
        foreach ($this->selectedItems as $item) {
            // Jumlahkan nominal per tagihan (bisa beda tarif antar semester)
            $sppId = $item['spp_id'];
            if ($item['bulan'] !== null) {
                $total += $this->sppMatrix[$sppId]['bulans'][$item['bulan']]['nominal'] ?? 0;
            } else {
                $total += $this->sppMatrix[$sppId]['nominal'] ?? 0;
            }
        }

        return $total;
    }

    // ── Payment Processing ────────────────────────────────────

    public function prosesBayar(): void
    {
        if (! $this->selectedSiswaId) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Pilih siswa terlebih dahulu.']);

            return;
        }

        if (empty($this->selectedItems)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Pilih minimal 1 tagihan untuk dibayar.']);

            return;
        }

        $ids = [];

        foreach ($this->selectedItems as $item) {
            $tagihan = Tagihan::find($item['tagihan_id']);
            if (! $tagihan || $tagihan->status === 'Lunas') {
                continue;
            }

            // Bayar sisa tagihan, bukan nominal penuh (cegah overpayment)
            $nominal = $tagihan->sisa_tagihan;

            if ($nominal <= 0) {
                continue;
            }

            // Catat pembayaran
            $pembayaran = $tagihan->bayar(
                nominal: $nominal,
                metode: 'Tunai',
            );

            $ids[] = $pembayaran->id;
        }

        $this->lastPembayaranIds = $ids;
        $this->selectedItems = [];
        $this->loadPaymentMatrix();
        $this->showKuitansiModal = true;
        $this->dispatch('open-modal', 'kuitansi-modal');

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => count($ids) . ' tagihan berhasil dicatat! 🎉',
        ]);
    }

    public function openKuitansi(): void
    {
        if ($this->lastPembayaranIds) {
            $this->dispatch('open-pdf-tab', [
                'url' => route('admin.keuangan.kuitansi.cetak', [
                    'ids' => implode(',', $this->lastPembayaranIds),
                ]),
            ]);
            $this->showKuitansiModal = false;
        }
    }

    public function closeKuitansiModal(): void
    {
        $this->showKuitansiModal = false;
        $this->lastPembayaranIds = null;
        $this->dispatch('close-modal', 'kuitansi-modal');
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        // Riwayat pembayaran siswa terpilih (10 terakhir)
        $riwayat = collect();
        if ($this->selectedSiswaId) {
            $riwayat = Pembayaran::with('tagihan')
                ->whereHas('tagihan', fn($q) => $q->where('siswa_id', $this->selectedSiswaId))
                ->latest('tanggal_bayar')
                ->limit(10)
                ->get();
        }

        return view('livewire.admin.keuangan.transaksi-pembayaran-index', [
            'totalBayar' => $this->getTotalBayar(),
            'riwayat' => $riwayat,
        ]);
    }
}
