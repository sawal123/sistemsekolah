<?php

namespace App\Http\Controllers;

use App\Models\PembayaranSpp;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KeuanganPdfController extends Controller
{
    /**
     * Generate kuitansi PDF untuk satu atau beberapa pembayaran SPP.
     * URL param: ?ids=1,2,3
     */
    public function cetakKuitansi(Request $request): Response
    {
        $ids = array_filter(array_map('intval', explode(',', $request->string('ids', '0'))));

        abort_if(empty($ids), 404, 'ID pembayaran tidak ditemukan.');

        $pembayarans = PembayaranSpp::with([
            'siswa.user',
            'siswa.kelas',
            'spp.tahunAjaran',
            'user',
        ])->whereIn('id', $ids)->get();

        abort_if($pembayarans->isEmpty(), 404, 'Data pembayaran tidak ditemukan.');

        // ── Dynamic Logo/Nama Sekolah dari Settings ────────────
        $settings    = Setting::whereIn('key', ['nama_sekolah', 'logo', 'alamat', 'telepon'])->pluck('value', 'key');
        $namaSekolah = $settings->get('nama_sekolah', 'Sistem Informasi Sekolah');
        $logoKey     = $settings->get('logo');
        $logoPath    = null;

        if ($logoKey) {
            $fullPath = public_path('storage/' . $logoKey);
            if (file_exists($fullPath)) {
                $logoPath = $fullPath;
            }
        }

        $nomorKuitansi = 'KW-' . now()->format('Ymd') . '-' . str_pad($pembayarans->first()->id, 5, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('pdf.kuitansi-spp', [
            'pembayarans'   => $pembayarans,
            'namaSekolah'   => $namaSekolah,
            'logoPath'      => $logoPath,
            'alamat'        => $settings->get('alamat', ''),
            'telepon'       => $settings->get('telepon', ''),
            'nomorKuitansi' => $nomorKuitansi,
            'tanggalCetak'  => now()->translatedFormat('d F Y'),
            'petugas'       => auth()->user()?->name ?? 'Admin',
        ])->setPaper('A5', 'portrait');

        return $pdf->stream('kuitansi-spp-' . now()->format('Ymd-His') . '.pdf');
    }
}
