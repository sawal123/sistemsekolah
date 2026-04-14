<?php

namespace App\Livewire\Admin\Kbm;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\KalenderAkademik;
use Carbon\Carbon;
use Livewire\WithFileUploads;
use App\Services\GeminiAiScanner;
use Exception;

#[Layout('layouts.admin')]
#[Title('Rekap Absensi')]
class RekapAbsensiIndex extends Component
{
    use WithFileUploads;

    #[Url]
    public $filterKelas;
    
    #[Url]
    public $filterBulan;

    #[Url]
    public $filterTahun;

    public $absensiData = [];
    
    // Scanner AI State
    public $fotoKertas;
    public $isScanning = false;

    public function mount()
    {
        if (!$this->filterBulan) $this->filterBulan = date('m');
        if (!$this->filterTahun) $this->filterTahun = date('Y');
        
        $user = auth()->user();
        if ($user->hasRole('guru') && $user->guru) {
            $kelasWali = Kelas::where('wali_kelas_id', $user->guru->id)->first();
            if ($kelasWali && !$this->filterKelas) {
                $this->filterKelas = $kelasWali->id;
            }
        }
    }

    public function loadData()
    {
        $this->absensiData = [];
        
        if ($this->filterKelas && $this->filterBulan && $this->filterTahun) {
            $startDate = Carbon::createFromDate($this->filterTahun, $this->filterBulan, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $absensis = Absensi::whereHas('siswa', function($q) {
                $q->where('kelas_id', $this->filterKelas);
            })
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

            foreach ($absensis as $ab) {
                $this->absensiData[$ab->siswa_id][$ab->tanggal->format('Y-m-d')] = $ab->status;
            }
        }
    }

    public function updateAbsensi($siswaId, $tanggal, $status)
    {
        if (KalenderAkademik::isHariLibur($tanggal)) {
            $this->dispatch('notify', title: 'Aksi Ditolak', message: 'Tidak dapat mengisi absensi pada Hari Libur / Minggu.', type: 'danger');
            return;
        }

        if (empty($status) || $status == '') {
            Absensi::where('siswa_id', $siswaId)->where('tanggal', $tanggal)->delete();
        } else {
            Absensi::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'tanggal' => $tanggal,
                ],
                [
                    'status' => $status,
                ]
            );
        }

        $this->dispatch('notify', title: 'Tersimpan', message: 'Data absensi berhasil diperbarui.', type: 'success');
    }

    public function scanAbsensi()
    {
        $this->validate([
            'fotoKertas' => 'required|image|max:8192', // Max 8MB
        ]);

        if (!$this->filterKelas || !$this->filterBulan || !$this->filterTahun) {
            $this->dispatch('notify', title: 'Gagal', message: 'Harap pilih Kelas, Bulan, dan Tahun terlebih dahulu.', type: 'warning');
            return;
        }

        $this->isScanning = true;

        try {
            // --- Optimasi Gambar: Resize untuk Kecepatan AI & Mencegah 504 Timeout ---
            $imagePath = $this->fotoKertas->getRealPath();
            $mimeType = $this->fotoKertas->getMimeType();
            
            // Buat resource gambar berdasarkan mime
            if ($mimeType == 'image/jpeg' || $mimeType == 'image/jpg') {
                $img = imagecreatefromjpeg($imagePath);
            } elseif ($mimeType == 'image/png') {
                $img = imagecreatefrompng($imagePath);
            } else {
                $img = imagecreatefromstring(file_get_contents($imagePath));
            }

            if ($img) {
                $width = imagesx($img);
                $height = imagesy($img);
                $maxDim = 1600; // Ukuran optimal untuk Gemini vision
                
                if ($width > $maxDim || $height > $maxDim) {
                    $ratio = $width / $height;
                    if ($ratio > 1) {
                        $newWidth = $maxDim;
                        $newHeight = $maxDim / $ratio;
                    } else {
                        $newHeight = $maxDim;
                        $newWidth = $maxDim * $ratio;
                    }
                    $resizedImg = imagecreatetruecolor($newWidth, $newHeight);
                    imagecopyresampled($resizedImg, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    
                    // Simpan ke buffer
                    ob_start();
                    imagejpeg($resizedImg, null, 80); // Kualitas 80 cukup untuk OCR
                    $imageData = ob_get_clean();
                    
                    imagedestroy($resizedImg);
                } else {
                    $imageData = file_get_contents($imagePath);
                }
                imagedestroy($img);
            } else {
                $imageData = file_get_contents($imagePath);
            }

            $base64Image = base64_encode($imageData);

            // 1. Ekstrak Daftar Siswa target
            $siswas = Siswa::with('user')->where('kelas_id', $this->filterKelas)->get()->sortBy('user.name')->values();
            $daftarSiswaTarget = [];
            foreach ($siswas as $idx => $s) {
                // Memberitahu AI struktur No.Urut => [ID, NAMA]
                $daftarSiswaTarget[$idx + 1] = [
                    'database_siswa_id' => $s->id,
                    'nama_lengkap' => $s->user->name
                ];
            }

            // 2. Ekstrak Daftar Libur di bulan ini
            $tanggalLibur = [];
            $daysInMonth = Carbon::createFromDate($this->filterTahun, $this->filterBulan, 1)->daysInMonth;
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $dateStr = Carbon::createFromDate($this->filterTahun, $this->filterBulan, $i)->format('Y-m-d');
                if (KalenderAkademik::isHariLibur($dateStr)) {
                    $tanggalLibur[] = $dateStr;
                }
            }

            // 3. Panggil Gemini
            $hasilJsonArray = GeminiAiScanner::scanAbsenMatriks(
                $base64Image, 
                $mimeType, 
                $daftarSiswaTarget, 
                $this->filterTahun, 
                $this->filterBulan, 
                $tanggalLibur
            );

            // 4. Update Massal ke Database (Lewati Hari Libur)
            $countUpdate = 0;
            
            if (empty($hasilJsonArray)) {
                throw new Exception("Mata AI tidak menemukan data yang cocok. Pastikan Nama Siswa di kertas sesuai dengan daftar kelas yang dipilih.");
            }

            foreach ($hasilJsonArray as $siswaId => $dataAbsenBulan) {
                foreach ($dataAbsenBulan as $item) {
                    $tanggal = $item['tanggal'] ?? null;
                    $status = $item['status'] ?? null;
                    
                    if ($tanggal && $status) {
                        // Anti-Bypass: Meskipun AI ngaco, backend tetap mem-blokir hari libur
                        if (!in_array($tanggal, $tanggalLibur)) {
                            Absensi::updateOrCreate(
                                ['siswa_id' => $siswaId, 'tanggal' => $tanggal],
                                ['status' => $status]
                            );
                            $countUpdate++;
                        }
                    }
                }
            }

            // Reset File & Refresh data
            $this->fotoKertas = null;
            $this->loadData();
            $this->dispatch('close-modal', 'modal-scanner-ai');
            
            if ($countUpdate > 0) {
                $this->dispatch('notify', 
                    title: 'Pemindaian Selesai', 
                    message: "Berhasil memproses $countUpdate data. PENTING: Mohon periksa kembali keselarasan data di tabel sebelum lanjut.", 
                    type: 'success'
                );
            } else {
                $this->dispatch('notify', 
                    title: 'Peringatan', 
                    message: "Pemindaian selesai tapi tidak ada data yang masuk. Pastikan tanda di kertas terbaca jelas.", 
                    type: 'warning'
                );
            }
            
        } catch (Exception $e) {
            $this->dispatch('notify', title: 'AI Error', message: $e->getMessage(), type: 'danger');
        }

        $this->isScanning = false;
    }

    public function render()
    {
        // Panggil loadData untuk inisialisasi / rekap refresh (jika tidak ada state)
        if (empty($this->absensiData)) {
            $this->loadData();
        }

        $user = auth()->user();
        if ($user->hasRole('guru') && $user->guru) {
            $listKelas = Kelas::where('wali_kelas_id', $user->guru->id)->get();
        } else {
            $listKelas = Kelas::orderBy('jenjang')->orderBy('nama_kelas')->get();
        }

        $siswas = [];
        $kalender = [];
        $hariEfektif = 0;

        if ($this->filterKelas && $this->filterBulan && $this->filterTahun) {
            $siswas = Siswa::with('user')
                ->where('kelas_id', $this->filterKelas)
                ->get()
                ->sortBy('user.name')
                ->values();

            $daysInMonth = Carbon::createFromDate($this->filterTahun, $this->filterBulan, 1)->daysInMonth;
            
            // Build Matrix dates
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $dateStr = Carbon::createFromDate($this->filterTahun, $this->filterBulan, $i)->format('Y-m-d');
                $isLibur = KalenderAkademik::isHariLibur($dateStr);
                
                $kalender[$i] = [
                    'tanggal' => $dateStr,
                    'is_libur' => $isLibur,
                    'is_minggu' => Carbon::parse($dateStr)->isSunday(),
                ];

                if (!$isLibur) {
                    $hariEfektif++;
                }
            }
        }

        return view('livewire.admin.kbm.rekap-absensi-index', compact('listKelas', 'siswas', 'kalender', 'hariEfektif'));
    }
}
