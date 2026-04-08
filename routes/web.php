<?php

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
    });

    // ─── Website (Blog) ───
    Route::get('/website/blog-artikel', BlogArtikelIndex::class)->name('admin.website.blog-artikel');
});

// ─── Admin Exclusive Routes ────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    // ─── Data Master ───
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
