<?php

namespace App\Livewire\Admin\Keuangan;

use App\Models\Kelas;
use App\Models\PembayaranSpp;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
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
    public string $filterDateMulai   = '';
    public string $filterDateSelesai = '';
    public string $filterKelas       = '';
    public string $filterJenjang     = '';
    public int    $filterTahun;
    public int    $perPage = 15;

    public function mount(): void
    {
        $this->filterTahun       = (int) now()->year;
        $this->filterDateMulai   = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateSelesai = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedFilterKelas(): void  { $this->resetPage(); }
    public function updatedFilterJenjang(): void { $this->resetPage(); }
    public function updatedFilterDateMulai(): void  { $this->resetPage(); }
    public function updatedFilterDateSelesai(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function render()
    {
        // ── Summary Cards ─────────────────────────────────────
        $totalHariIni = PembayaranSpp::where('status', 'Lunas')
            ->whereDate('tanggal_bayar', today())
            ->sum(DB::raw('jumlah_bayar - potongan'));

        $totalBulanIni = PembayaranSpp::where('status', 'Lunas')
            ->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum(DB::raw('jumlah_bayar - potongan'));

        $totalTahunIni = PembayaranSpp::where('status', 'Lunas')
            ->whereYear('tanggal_bayar', $this->filterTahun)
            ->sum(DB::raw('jumlah_bayar - potongan'));

        // ── Target & Efektivitas ──────────────────────────────
        $tahunAjaran  = TahunAjaran::where('is_active', true)->first();
        $targetTagihan = $this->hitungTargetTagihan($tahunAjaran);
        $efektivitas   = $targetTagihan > 0
            ? min(round(($totalTahunIni / $targetTagihan) * 100, 1), 100)
            : 0;

        // ── Laporan Pembayaran (tabel dengan filter) ──────────
        $laporanQuery = PembayaranSpp::with(['siswa.user', 'siswa.kelas', 'spp', 'user'])
            ->where('status', 'Lunas');

        if ($this->filterDateMulai) {
            $laporanQuery->whereDate('tanggal_bayar', '>=', $this->filterDateMulai);
        }
        if ($this->filterDateSelesai) {
            $laporanQuery->whereDate('tanggal_bayar', '<=', $this->filterDateSelesai);
        }
        if ($this->filterJenjang) {
            $laporanQuery->whereHas('siswa', fn ($q) => $q->where('jenjang', $this->filterJenjang));
        }
        if ($this->filterKelas) {
            $laporanQuery->whereHas('siswa', fn ($q) => $q->where('kelas_id', $this->filterKelas));
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
            'totalHariIni'         => $totalHariIni,
            'totalBulanIni'        => $totalBulanIni,
            'totalTahunIni'        => $totalTahunIni,
            'targetTagihan'        => $targetTagihan,
            'efektivitas'          => $efektivitas,
            'laporan'              => $laporan,
            'siswasBelumBayar'     => $siswasBelumBayar,
            'totalTunggakanCount'  => $totalTunggakanCount,
            'totalNominalTunggakan'=> $totalNominalTunggakan,
            'kelass'               => $kelass,
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
            ->get();

        foreach ($spps as $spp) {
            $jumlahSiswa = Siswa::where('status', 'Aktif')
                ->when($spp->jenjang !== 'Semua', fn ($q) => $q->where('jenjang', $spp->jenjang))
                ->count();
            $target += $spp->nominal * $jumlahSiswa * $bulanBerjalan;
        }

        return $target;
    }

    private function hitungTunggakan(?TahunAjaran $tahunAjaran): array
    {
        if (! $tahunAjaran) {
            return [collect(), 0];
        }

        // Load semua siswa aktif dengan filter
        $siswaAktif = Siswa::with(['user', 'kelas'])
            ->where('status', 'Aktif')
            ->when($this->filterJenjang, fn ($q) => $q->where('jenjang', $this->filterJenjang))
            ->when($this->filterKelas, fn ($q) => $q->where('kelas_id', $this->filterKelas))
            ->get();

        if ($siswaAktif->isEmpty()) {
            return [collect(), 0];
        }

        // Buat map SPP per jenjang (efisien)
        $sppMap = Spp::where('tahun_ajaran_id', $tahunAjaran->id)
            ->where('kategori', 'SPP Bulanan')
            ->get()
            ->keyBy('jenjang');

        // Load semua pembayaran SPP Bulanan untuk tahun ini sekaligus
        $allPembayarans = PembayaranSpp::whereIn('siswa_id', $siswaAktif->pluck('id'))
            ->where('tahun', $this->filterTahun)
            ->where('status', 'Lunas')
            ->whereNotNull('bulan')
            ->whereIn('spp_id', $sppMap->pluck('id'))
            ->get()
            ->groupBy('siswa_id');

        $result = collect();
        $totalTunggakan = 0;
        $bulanMax = now()->month;

        foreach ($siswaAktif as $siswa) {
            // Tentukan SPP yang berlaku: prioritas jenjang spesifik, fallback ke 'Semua'
            $spp = $sppMap->get($siswa->jenjang) ?? $sppMap->get('Semua');
            if (! $spp) {
                continue;
            }

            $paidMonths = ($allPembayarans->get($siswa->id) ?? collect())
                ->where('spp_id', $spp->id)
                ->pluck('bulan')
                ->toArray();

            $bulanTunggakan = [];
            for ($b = 1; $b <= $bulanMax; $b++) {
                if (! in_array($b, $paidMonths)) {
                    $bulanTunggakan[] = $b;
                }
            }

            if (! empty($bulanTunggakan)) {
                $nominal = $spp->nominal * count($bulanTunggakan);
                $result->push([
                    'siswa'           => $siswa,
                    'bulan_tunggakan' => $bulanTunggakan,
                    'jumlah_bulan'    => count($bulanTunggakan),
                    'nominal_per_bulan' => $spp->nominal,
                    'total_tunggakan' => $nominal,
                ]);
                $totalTunggakan += $nominal;
            }
        }

        // Urutkan: terbanyak tunggakan dulu
        $result = $result->sortByDesc('jumlah_bulan')->values();

        return [$result, $totalTunggakan];
    }
}
