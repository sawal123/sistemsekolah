<?php

use App\Livewire\Admin\Akademik\KalenderAkademikIndex;
use App\Livewire\Admin\Alumni\JejakAlumniIndex;
use App\Livewire\Admin\Civitas\DataGuruIndex;
use App\Livewire\Admin\Civitas\DataPenggunaIndex;
use App\Livewire\Admin\Civitas\DataSiswaIndex;
use App\Livewire\Admin\DataMaster\DataKelasIndex;
use App\Livewire\Admin\DataMaster\MataPelajaranIndex;
use App\Livewire\Admin\DataMaster\TahunAjaranIndex;
use App\Livewire\Admin\Kbm\ERaporIndex;
use App\Livewire\Admin\Kbm\JadwalPelajaranIndex;
use App\Livewire\Admin\Kbm\ManajemenNilaiIndex;
use App\Livewire\Admin\Kbm\RekapAbsensiIndex;
use App\Http\Controllers\KeuanganPdfController;
use App\Livewire\Admin\Keuangan\LaporanKeuanganIndex;
use App\Livewire\Admin\Keuangan\MasterSppIndex;
use App\Livewire\Admin\Keuangan\TransaksiPembayaranIndex;
use App\Livewire\Admin\Website\BlogArtikelIndex;
use App\Livewire\Admin\Website\GaleriSliderIndex;
use App\Livewire\Admin\Website\PengaturanUmumIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ─── Auth Routes ──────────────────────────────────────────────
Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout')->middleware('auth');

// ─── Unauthorized ──────────────────────────────────────────────
Route::get('/unauthorized', function () {
    return view('errors.unauthorized');
})->name('unauthorized');

// ─── Admin & Guru Shared Routes (protected) ───────────────
Route::middleware(['auth', 'role:admin|guru'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // ─── KBM & Laporan ───
    Route::prefix('kbm')->name('admin.kbm.')->group(function () {
        Route::get('/jadwal-pelajaran', JadwalPelajaranIndex::class)->name('jadwal-pelajaran');
        Route::get('/rekap-absensi', RekapAbsensiIndex::class)->name('rekap-absensi');
        Route::get('/manajemen-nilai', ManajemenNilaiIndex::class)->name('manajemen-nilai');
        Route::get('/e-rapor', ERaporIndex::class)->name('e-rapor');
        Route::get('/kalender-akademik', KalenderAkademikIndex::class)->name('kalender-akademik');
        Route::get('/e-rapor/{siswa_id}/cetak', [App\Http\Controllers\PdfCetakController::class, 'cetak'])->name('e-rapor.cetak');
        Route::get('/rekap-absensi/cetak-template/{kelas}/{bulan}/{tahun}', [App\Http\Controllers\PdfCetakController::class, 'cetakTemplateAbsen'])->name('rekap-absensi.cetak-template');
    });

    // ─── Website (Blog) ───
    Route::get('/website/blog-artikel', BlogArtikelIndex::class)->name('admin.website.blog-artikel');
});

// ─── Admin Exclusive Routes ────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    // ─── Civitas Akademik ───
    Route::prefix('civitas')->name('admin.civitas.')->group(function () {
        Route::get('/data-guru', App\Livewire\Admin\Civitas\DataGuruIndex::class)->name('data-guru');
        Route::get('/data-guru/create', App\Livewire\Admin\Civitas\DataGuruForm::class)->name('data-guru.create');
        Route::get('/data-guru/{guru}/edit', App\Livewire\Admin\Civitas\DataGuruForm::class)->name('data-guru.edit');
        Route::get('/data-guru/{guru}', App\Livewire\Admin\Civitas\DataGuruDetail::class)->name('data-guru.detail');
    });
    Route::prefix('data-master')->name('admin.data-master.')->group(function () {
        Route::get('/tahun-ajaran', TahunAjaranIndex::class)->name('tahun-ajaran');
        Route::get('/mata-pelajaran', MataPelajaranIndex::class)->name('mata-pelajaran');
        Route::get('/data-kelas', DataKelasIndex::class)->name('data-kelas');
    });

    // ─── Civitas Akademik ───
    Route::prefix('civitas')->name('admin.civitas.')->group(function () {
        Route::get('/data-siswa', DataSiswaIndex::class)->name('data-siswa');
        Route::get('/data-guru', DataGuruIndex::class)->name('data-guru');
        Route::get('/data-pengguna', DataPenggunaIndex::class)->name('data-pengguna');
    });

    // ─── Keuangan ───
    Route::prefix('keuangan')->name('admin.keuangan.')->group(function () {
        Route::get('/master-spp', MasterSppIndex::class)->name('master-spp');
        Route::get('/transaksi-pembayaran', TransaksiPembayaranIndex::class)->name('transaksi-pembayaran');
        Route::get('/laporan-keuangan', LaporanKeuanganIndex::class)->name('laporan-keuangan');
        Route::get('/kuitansi/cetak', [KeuanganPdfController::class, 'cetakKuitansi'])->name('kuitansi.cetak');
    });

    // ─── Kelulusan & Alumni ───
    Route::prefix('alumni')->name('admin.alumni.')->group(function () {
        Route::get('/jejak-alumni', JejakAlumniIndex::class)->name('jejak-alumni');
    });

    // ─── Website (Restricted) ───
    Route::prefix('website')->name('admin.website.')->group(function () {
        Route::get('/galeri-slider', GaleriSliderIndex::class)->name('galeri-slider');
        Route::get('/pengaturan-umum', PengaturanUmumIndex::class)->name('pengaturan-umum');
    });
});
