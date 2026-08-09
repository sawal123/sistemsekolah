<?php

namespace App\Livewire\Admin\Keuangan;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Laporan Keuangan SPP')]
class LaporanKeuanganIndex extends Component
{
    use WithPagination;

    public string $activeTab = 'laporan'; // 'laporan' | 'tunggakan'

    // ── Filters ───────────────────────────────────────────────
    public string $filterDateMulai = '';

    public string $filterDateSelesai = '';

    public string $filterKelas = '';

    public string $filterJenjang = '';

    public int $filterTahun;

    public int $perPage = 15;

    public function mount(): void
    {
        $this->filterTahun = (int) now()->year;
        $this->filterDateMulai = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateSelesai = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedFilterKelas(): void
    {
        $this->resetPage();
    }

    public function updatedFilterJenjang(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDateMulai(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDateSelesai(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        // ── Summary Cards ─────────────────────────────────────
        $totalHariIni = Pembayaran::whereDate('tanggal_bayar', today())
            ->sum('nominal');

        $totalBulanIni = Pembayaran::whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('nominal');

        $totalTahunIni = Pembayaran::whereYear('tanggal_bayar', $this->filterTahun)
            ->sum('nominal');

        // ── Target & Efektivitas ──────────────────────────────
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        $targetTagihan = $this->hitungTargetTagihan($tahunAjaran);
        $efektivitas = $targetTagihan > 0
            ? min(round(($totalTahunIni / $targetTagihan) * 100, 1), 100)
            : 0;

        // ── Laporan Pembayaran (tabel dengan filter) ──────────
        $laporanQuery = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.spp', 'petugas']);

        if ($this->filterDateMulai) {
            $laporanQuery->whereDate('tanggal_bayar', '>=', $this->filterDateMulai);
        }
        if ($this->filterDateSelesai) {
            $laporanQuery->whereDate('tanggal_bayar', '<=', $this->filterDateSelesai);
        }
        if ($this->filterJenjang) {
            $laporanQuery->whereHas('tagihan.siswa', fn($q) => $q->where('jenjang', $this->filterJenjang));
        }
        if ($this->filterKelas) {
            $laporanQuery->whereHas('tagihan.siswa', fn($q) => $q->where('kelas_id', $this->filterKelas));
        }

        $laporan = $laporanQuery->latest('tanggal_bayar')->paginate($this->perPage);

        // ── Daftar Tunggakan ──────────────────────────────────
        $siswasBelumBayar = collect();
        $totalNominalTunggakan = 0;
        $totalTunggakanCount = 0;

        if ($this->activeTab === 'tunggakan' && $tahunAjaran) {
            [$fullList, $totalNominalTunggakan] = $this->hitungTunggakan($tahunAjaran);
            $totalTunggakanCount = $fullList->count();

            // Manual Pagination for the collection
            $currentPage = Paginator::resolveCurrentPage();
            $currentItems = $fullList->slice(($currentPage - 1) * $this->perPage, $this->perPage)->all();

            $siswasBelumBayar = new LengthAwarePaginator(
                $currentItems,
                $totalTunggakanCount,
                $this->perPage,
                $currentPage,
                ['path' => Paginator::resolveCurrentPath()]
            );
        }

        $kelass = Kelas::orderBy('nama_kelas')->get();

        return view('livewire.admin.keuangan.laporan-keuangan-index', [
            'totalHariIni' => $totalHariIni,
            'totalBulanIni' => $totalBulanIni,
            'totalTahunIni' => $totalTahunIni,
            'targetTagihan' => $targetTagihan,
            'efektivitas' => $efektivitas,
            'laporan' => $laporan,
            'siswasBelumBayar' => $siswasBelumBayar,
            'totalTunggakanCount' => $totalTunggakanCount,
            'totalNominalTunggakan' => $totalNominalTunggakan,
            'kelass' => $kelass,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────

    private function hitungTargetTagihan(?TahunAjaran $tahunAjaran): float
    {
        if (! $tahunAjaran) {
            return 0;
        }

        $target = 0;
        $bulanBerjalan = now()->month;

        $spps = Spp::where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('kategori', 'SPP Bulanan')
            ->active()
            ->get();

        $siswaAktif = Siswa::where('status', 'Aktif')->get();
        foreach ($siswaAktif as $siswa) {
            $spp = $spps
                ->filter(fn($tarif) => in_array($tarif->jenjang, [$siswa->jenjang, 'Semua'], true))
                ->filter(fn($tarif) => $tarif->jurusan_id === null || $tarif->jurusan_id === $siswa->jurusan_id)
                ->sortByDesc(fn($tarif) => ($tarif->jenjang === $siswa->jenjang ? 2 : 0) + ($tarif->jurusan_id ? 1 : 0))
                ->first();

            if ($spp) {
                $target += $spp->nominal * $bulanBerjalan;
            }
        }

        return $target;
    }

    private function hitungTunggakan(?TahunAjaran $tahunAjaran): array
    {
        if (! $tahunAjaran) {
            return [collect(), 0];
        }

        // Load semua siswa aktif dengan filter
        $siswaAktif = Siswa::with(['user', 'kelas', 'jurusan'])
            ->where('status', 'Aktif')
            ->when($this->filterJenjang, fn($q) => $q->where('jenjang', $this->filterJenjang))
            ->when($this->filterKelas, fn($q) => $q->where('kelas_id', $this->filterKelas))
            ->get();

        if ($siswaAktif->isEmpty()) {
            return [collect(), 0];
        }

        $sppBulanan = Spp::where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('kategori', 'SPP Bulanan')
            ->active()
            ->get();

        // Load semua tagihan SPP Bulanan untuk tahun ini
        $allTagihans = Tagihan::whereIn('siswa_id', $siswaAktif->pluck('id'))
            ->where('tahun', $this->filterTahun)
            ->whereNotNull('bulan')
            ->whereIn('spp_id', $sppBulanan->pluck('id'))
            ->get()
            ->groupBy('siswa_id');

        $result = collect();
        $totalTunggakan = 0;
        $bulanMax = now()->month;

        foreach ($siswaAktif as $siswa) {
            $spp = $sppBulanan
                ->filter(fn ($tarif) => in_array($tarif->jenjang, [$siswa->jenjang, 'Semua'], true))
                ->filter(fn ($tarif) => $tarif->jurusan_id === null || $tarif->jurusan_id === $siswa->jurusan_id)
                ->sortByDesc(fn ($tarif) => ($tarif->jenjang === $siswa->jenjang ? 2 : 0) + ($tarif->jurusan_id ? 1 : 0))
                ->first();
            if (! $spp) {
                continue;
            }

            $siswaTagihans = $allTagihans->get($siswa->id) ?? collect();
            $tagihanByBulan = $siswaTagihans
                ->where('spp_id', $spp->id)
                ->keyBy('bulan');

            $bulanTunggakan = [];
            $nominalTunggakan = 0;

            for ($b = 1; $b <= $bulanMax; $b++) {
                $tagihan = $tagihanByBulan->get($b);

                if ($tagihan && $tagihan->status === 'Lunas') {
                    continue; // Bulan ini sudah lunas
                }

                $bulanTunggakan[] = $b;

                if ($tagihan && $tagihan->status === 'Lunas Sebagian') {
                    // Hanya hitung sisa yang belum terbayar
                    $nominalTunggakan += $tagihan->sisa_tagihan;
                } else {
                    // Belum ada pembayaran sama sekali
                    $nominalTunggakan += $spp->nominal;
                }
            }

            if (! empty($bulanTunggakan)) {
                $result->push([
                    'siswa' => $siswa,
                    'bulan_tunggakan' => $bulanTunggakan,
                    'jumlah_bulan' => count($bulanTunggakan),
                    'nominal_per_bulan' => $spp->nominal,
                    'total_tunggakan' => $nominalTunggakan,
                ]);
                $totalTunggakan += $nominalTunggakan;
            }
        }

        // Urutkan: terbanyak tunggakan dulu
        $result = $result->sortByDesc('jumlah_bulan')->values();

        return [$result, $totalTunggakan];
    }
}
