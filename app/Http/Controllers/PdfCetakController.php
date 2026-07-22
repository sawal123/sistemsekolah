<?php

namespace App\Http\Controllers;

use App\Models\Rapor;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfCetakController extends Controller
{
    public function cetak(Request $request, $siswa_id)
    {
        $taId = $request->query('ta');
        if (!$taId) {
            abort(404, 'Tahun Ajaran tidak spesifik.');
        }

        $ta = TahunAjaran::find($taId);
        $siswa = Siswa::with(['user', 'kelas.wali_kelas.user'])->findOrFail($siswa_id);
        
        $rapor = Rapor::where('siswa_id', $siswa_id)
            ->where('tahun_ajaran_id', $taId)
            ->first();

        // Load Nilai dari Siswa
        $nilais = $siswa->nilais()->with('mapel')->where('tahun_ajaran_id', $taId)->get();

        // Pengelompokan Data Mapel jika ada kategori, namun default kita lemparkan flat
        $kkmDefault = 75; // Diambil dari rata-rata kkm jika nihil

        // QR Code Generator (Base64 agar bisa dirender oleh DomPDF)
        $urlValidasi = url('/rapor/validasi/' . ($rapor ? $rapor->id : 'preview'));
        // Menggunakan library SimpleQrCode
        $qrCodeBase64 = base64_encode(QrCode::format('svg')->size(100)->generate($urlValidasi));

        // Peringkat Kolektif
        $peringkat = '-';
        if ($rapor && $rapor->rata_rata_nilai > 0) {
            $semuaRapor = Rapor::where('kelas_id', $siswa->kelas_id)
                ->where('tahun_ajaran_id', $taId)
                ->get();
            
            $rankings = [];
            foreach ($semuaRapor as $rp) {
                // Untuk menghindari N+1 kita harus idealnya eager load tapi untk cetakan tunggal ini dimaafkan
                $rankings[$rp->siswa_id] = $rp->rata_rata_nilai;
            }
            arsort($rankings);
            $r = 1;
            foreach ($rankings as $sId => $avg) {
                if ($sId == $siswa_id) {
                    $peringkat = $r;
                    break;
                }
                $r++;
            }
        }

        // Tembak Data ke View PDF Baru
        $pdf = Pdf::loadView('rapor-pdf', compact(
            'siswa', 'ta', 'rapor', 'nilais', 'qrCodeBase64', 'peringkat'
        ))->setPaper('A4', 'portrait');

        $tahunAman = str_replace(['/', '\\'], '-', $ta->tahun);
        $fileName = 'Rapor_' . str_replace(' ', '_', $siswa->user->name) . '_' . $tahunAman . '.pdf';
        
        // return $pdf->download($fileName); // Jika ingin langsung download
        return $pdf->stream($fileName); // Jika ingin preview di browser dahulu
    }
    public function cetakTemplateAbsen($kelasId, $bulan, $tahun)
    {
        $kelas = \App\Models\Kelas::with('wali_kelas.user')->findOrFail($kelasId);
        $siswas = \App\Models\Siswa::with('user')
            ->where('kelas_id', $kelasId)
            ->get()
            ->sortBy('user.name')
            ->values();

        $kalender = [];
        $daysInMonth = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dateStr = \Carbon\Carbon::createFromDate($tahun, $bulan, $i)->format('Y-m-d');
            $kalender[$i] = [
                'tanggal' => $dateStr,
                'is_libur' => \App\Models\KalenderAkademik::isHariLibur($dateStr),
                'is_minggu' => \Carbon\Carbon::parse($dateStr)->isSunday(),
            ];
        }

        // Nama Bulan Bahasa Indonesia
        $namaBulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $namaBulan = $namaBulanList[(int)$bulan] ?? 'Bulan';

        $pdf = Pdf::loadView('template-absen-pdf', compact(
            'kelas', 'siswas', 'kalender', 'namaBulan', 'tahun', 'daysInMonth', 'bulan'
        ))->setPaper('legal', 'landscape');

        $fileName = 'Template_Absen_' . str_replace(' ', '_', $kelas->nama_kelas) . '_' . $namaBulan . '_' . $tahun . '.pdf';
        
        return $pdf->stream($fileName);
    }
}
