<?php

namespace App\Livewire\Admin\Keuangan;

use App\Models\PembayaranSpp;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Transaksi Pembayaran SPP')]
class TransaksiPembayaranIndex extends Component
{
    // ── Search State ──────────────────────────────────────────
    public string $searchQuery   = '';
    public bool   $showDropdown  = false;

    // ── Selected Student ──────────────────────────────────────
    public ?int   $selectedSiswaId   = null;
    public array  $selectedSiswaData = [];
    public int    $selectedTahun;

    // ── Payment Matrix & Selection ────────────────────────────
    public array  $sppMatrix     = [];
    // key: "{sppId}_{bulan|'sekali'}" → ['spp_id' => int, 'bulan' => int|null]
    public array  $selectedItems = [];

    // ── Post-Payment ──────────────────────────────────────────
    public ?array $lastPembayaranIds  = null;
    public bool   $showKuitansiModal  = false;

    public function mount(): void
    {
        $this->selectedTahun = (int) now()->year;
    }

    // ── Computed: Live Search Results ──────────────────────────

    #[Computed]
    public function hasilPencarian(): array
    {
        if (strlen(trim($this->searchQuery)) < 2) {
            return [];
        }

        return Siswa::with(['kelas', 'user'])
            ->where('status', 'Aktif')
            ->where(function ($q) {
                $q->where('nisn', 'like', '%' . $this->searchQuery . '%')
                    ->orWhere('nis', 'like', '%' . $this->searchQuery . '%')
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', '%' . $this->searchQuery . '%'));
            })
            ->limit(8)
            ->get()
            ->map(fn ($s) => [
                'id'         => $s->id,
                'nama'       => $s->user?->name ?? '-',
                'nisn'       => $s->nisn,
                'kelas'      => $s->kelas?->nama_kelas ?? 'Belum Ada Kelas',
                'jenjang'    => $s->jenjang,
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
        $siswa = Siswa::with(['kelas', 'user'])->findOrFail($id);

        $this->selectedSiswaId = $id;
        $this->selectedSiswaData = [
            'id'      => $siswa->id,
            'nama'    => $siswa->user?->name ?? '-',
            'nisn'    => $siswa->nisn,
            'nis'     => $siswa->nis,
            'jenjang' => $siswa->jenjang,
            'kelas'   => $siswa->kelas?->nama_kelas ?? 'Belum Ada Kelas',
            'status'  => $siswa->status,
        ];

        $this->searchQuery   = '';
        $this->showDropdown  = false;
        $this->selectedItems = [];
        $this->loadPaymentMatrix();
    }

    public function clearSiswa(): void
    {
        $this->selectedSiswaId   = null;
        $this->selectedSiswaData = [];
        $this->sppMatrix         = [];
        $this->selectedItems     = [];
        $this->lastPembayaranIds = null;
        $this->searchQuery       = '';
        $this->showDropdown      = false;
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

        $jenjang     = $this->selectedSiswaData['jenjang'] ?? null;
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();

        $spps = Spp::when($tahunAjaran, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaran->id))
            ->where(function ($q) use ($jenjang) {
                $q->where('jenjang', $jenjang)->orWhere('jenjang', 'Semua');
            })
            ->orderBy('kategori')
            ->get();

        $matrix = [];

        foreach ($spps as $spp) {
            if ($spp->kategori === 'SPP Bulanan') {
                // ── Tipe Bulanan: matriks 12 bulan
                $pembayarans = PembayaranSpp::where('siswa_id', $this->selectedSiswaId)
                    ->where('spp_id', $spp->id)
                    ->where('tahun', $this->selectedTahun)
                    ->whereNotNull('bulan')
                    ->where('status', 'Lunas')
                    ->get()
                    ->keyBy('bulan');

                $bulans = [];
                for ($b = 1; $b <= 12; $b++) {
                    $p = $pembayarans->get($b);
                    $bulans[$b] = [
                        'lunas'   => $p !== null,
                        'id'      => $p?->id,
                        'tanggal' => $p ? Carbon::parse($p->tanggal_bayar)->format('d/m/Y') : null,
                        'petugas' => $p?->user?->name,
                    ];
                }

                $matrix[$spp->id] = [
                    'id'         => $spp->id,
                    'kategori'   => $spp->kategori,
                    'nominal'    => (float) $spp->nominal,
                    'is_bulanan' => true,
                    'bulans'     => $bulans,
                ];
            } else {
                // ── Tipe Sekali Bayar
                $pembayaran = PembayaranSpp::where('siswa_id', $this->selectedSiswaId)
                    ->where('spp_id', $spp->id)
                    ->where('tahun', $this->selectedTahun)
                    ->where('status', 'Lunas')
                    ->first();

                $matrix[$spp->id] = [
                    'id'            => $spp->id,
                    'kategori'      => $spp->kategori,
                    'nominal'       => (float) $spp->nominal,
                    'is_bulanan'    => false,
                    'lunas'         => $pembayaran !== null,
                    'pembayaran_id' => $pembayaran?->id,
                    'tanggal'       => $pembayaran ? Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') : null,
                ];
            }
        }

        $this->sppMatrix = $matrix;
    }

    // ── Item Selection ────────────────────────────────────────

    /**
     * Toggle pilih/batalkan bulan untuk dibayar.
     * $bulan: integer (1-12) untuk SPP Bulanan, string 'sekali' untuk non-bulanan
     */
    public function toggleItem(int $sppId, $bulan): void
    {
        $key = $sppId . '_' . $bulan;

        if (isset($this->selectedItems[$key])) {
            unset($this->selectedItems[$key]);
            return;
        }

        if ($bulan === 'sekali') {
            // Cek belum lunas
            if (! ($this->sppMatrix[$sppId]['lunas'] ?? false)) {
                $this->selectedItems[$key] = ['spp_id' => $sppId, 'bulan' => null];
            }
        } else {
            $bulanInt = (int) $bulan;
            if (! ($this->sppMatrix[$sppId]['bulans'][$bulanInt]['lunas'] ?? false)) {
                $this->selectedItems[$key] = ['spp_id' => $sppId, 'bulan' => $bulanInt];
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
                            'bulan'  => $b,
                        ];
                    }
                }
            } else {
                if (! $data['lunas']) {
                    $this->selectedItems[$sppId . '_sekali'] = [
                        'spp_id' => (int) $sppId,
                        'bulan'  => null,
                    ];
                }
            }
        }

        if (empty($this->selectedItems)) {
            $this->dispatch('notify', ['type' => 'info', 'message' => '✅ Semua tagihan sudah lunas!']);
        } else {
            $this->dispatch('notify', [
                'type'    => 'info',
                'message' => count($this->selectedItems) . ' item tunggakan dipilih.',
            ]);
        }
    }

    // ── Computed Totals ───────────────────────────────────────

    public function getTotalBayar(): float
    {
        $total = 0;
        foreach ($this->selectedItems as $item) {
            $sppId = $item['spp_id'];
            if (isset($this->sppMatrix[$sppId])) {
                $total += $this->sppMatrix[$sppId]['nominal'];
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
            $spp = Spp::find($item['spp_id']);
            if (! $spp) {
                continue;
            }

            $pembayaran = PembayaranSpp::create([
                'siswa_id'    => $this->selectedSiswaId,
                'spp_id'      => $item['spp_id'],
                'user_id'     => auth()->id(),
                'tahun'       => $this->selectedTahun,
                'bulan'       => $item['bulan'],
                'tanggal_bayar' => now(),
                'jumlah_bayar' => $spp->nominal,
                'potongan'    => 0,
                'status'      => 'Lunas',
                'keterangan'  => null,
            ]);

            $ids[] = $pembayaran->id;
        }

        $this->lastPembayaranIds = $ids;
        $this->selectedItems     = [];
        $this->loadPaymentMatrix();
        $this->showKuitansiModal = true;
        $this->dispatch('open-modal', 'kuitansi-modal');

        $this->dispatch('notify', [
            'type'    => 'success',
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
            $riwayat = \App\Models\PembayaranSpp::with(['spp'])
                ->where('siswa_id', $this->selectedSiswaId)
                ->where('status', 'Lunas')
                ->latest('tanggal_bayar')
                ->limit(10)
                ->get();
        }

        return view('livewire.admin.keuangan.transaksi-pembayaran-index', [
            'totalBayar' => $this->getTotalBayar(),
            'riwayat'    => $riwayat,
        ]);
    }
}
