<?php

use App\Livewire\Admin\Akademik\KalenderAkademikIndex;
use App\Livewire\Admin\Alumni\JejakAlumniIndex;
use App\Livewire\Admin\Civitas\DataGuruDetail;
use App\Livewire\Admin\Civitas\DataGuruForm;
use App\Livewire\Admin\Civitas\DataGuruIndex;
use App\Livewire\Admin\Civitas\DataPenggunaIndex;
use App\Livewire\Admin\Civitas\DataSiswaIndex;
use App\Livewire\Admin\DataMaster\DataKelasIndex;
use App\Livewire\Admin\DataMaster\JurusanIndex;
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
use App\Livewire\Admin\Keuangan\PaymentSubmissionReviewIndex;
use App\Http\Controllers\PaymentSubmissionProofController;
use App\Livewire\Admin\Ppdb\PendaftaranMuridBaruIndex;
use App\Livewire\Admin\Website\BlogArtikelForm;
use App\Livewire\Admin\Website\BlogArtikelIndex;
use App\Livewire\Admin\Website\GaleriSliderIndex;
use App\Livewire\Admin\Website\MediaAssetIndex;
use App\Livewire\Admin\Website\PengaturanUmumIndex;
use App\Livewire\Admin\Website\VisiMisiIndex;
use App\Livewire\Admin\Website\WebsiteVisitIndex;
use App\Livewire\Admin\Website\WebsitePopupIndex;
use App\Http\Controllers\VisitorPopupController;
use App\Livewire\Admin\Website\StructureOrganizationIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Assistant\HelpChat;
use App\Livewire\Dashboard;
use App\Livewire\Admin\Fasilitas\FasilitasIndex;
use App\Livewire\Landing\HomePage;
use App\Livewire\Landing\AboutPage;
use App\Livewire\Landing\AcademicPage;
use App\Livewire\Landing\BlogDetailPage;
use App\Livewire\Landing\BlogPage;
use App\Livewire\Landing\ContactPage;
use App\Livewire\Landing\FacilitiesPage;
use App\Livewire\Landing\PpdbPage;
use App\Livewire\Landing\PublicPage;
use App\Livewire\Landing\StaffingPage;
use App\Livewire\Student\StudentAcademicIndex;
use App\Livewire\Student\StudentDashboard;
use App\Livewire\Student\StudentPaymentIndex;
use App\Livewire\Student\StudentProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->middleware('track.website')->name('home');
Route::post('/informasi-modal/{popup}/tampil', [VisitorPopupController::class, 'store'])
    ->middleware('throttle:30,1')->name('landing.popup.impression');
Route::middleware('track.website')->name('landing.')->group(function () {
    foreach (array_keys(PublicPage::PAGES) as $page) {
        Route::get('/'.$page, PublicPage::class)->defaults('page', $page)->name($page);
    }
    Route::get('/tentang', AboutPage::class)->name('about');
    Route::get('/akademik/{program}', AcademicPage::class)
        ->whereIn('program', ['ipa', 'ips', 'bahasa', 'kurikulum'])
        ->name('academic');
    Route::get('/fasilitas-sekolah', FacilitiesPage::class)->name('facilities');
    Route::get('/kepegawaian/{section}', StaffingPage::class)
        ->whereIn('section', ['guru', 'tata-usaha', 'struktur-organisasi'])
        ->name('staffing');
    Route::get('/blog', BlogPage::class)->name('blog');
    Route::get('/blog/{slug}', BlogDetailPage::class)->name('blog.show');
    Route::get('/ppdb', PpdbPage::class)->defaults('section', 'informasi')->name('ppdb');
    Route::get('/ppdb/{section}', PpdbPage::class)
        ->whereIn('section', ['syarat', 'jalur', 'biaya', 'daftar'])
        ->name('ppdb.section');
    Route::get('/kontak', ContactPage::class)->name('contact');
});

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
    Route::get('/bantuan-ai', HelpChat::class)->name('assistant.help');

    // ─── Profil & Pengaturan Akun ───
    Route::get('/profil', App\Livewire\Admin\Profile\ProfileIndex::class)->name('admin.profile');
    Route::get('/pengaturan', App\Livewire\Admin\Profile\SettingsIndex::class)->name('admin.settings');

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
    Route::get('/website/blog-artikel/create', BlogArtikelForm::class)->name('admin.website.blog-artikel.create');
    Route::get('/website/blog-artikel/{post}/edit', BlogArtikelForm::class)->name('admin.website.blog-artikel.edit');
});

Route::middleware(['auth', 'role:siswa', 'student.profile'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', StudentDashboard::class)->name('dashboard');
    Route::get('/pembayaran', StudentPaymentIndex::class)->name('payments');
    Route::get('/akademik', StudentAcademicIndex::class)->name('academic');
    Route::get('/profil', StudentProfile::class)->name('profile');
    Route::get('/pengajuan-pembayaran/{submission}/bukti', [PaymentSubmissionProofController::class, 'show'])
        ->name('payment-submissions.proof');
});

// ─── Admin Exclusive Routes ────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    // ─── Fasilitas Sekolah ───
    Route::get('/fasilitas', FasilitasIndex::class)->name('admin.fasilitas');

    Route::prefix('ppdb')->name('admin.ppdb.')->group(function () {
        Route::get('/pendaftaran-murid-baru', PendaftaranMuridBaruIndex::class)->name('pendaftaran-murid-baru');
    });

    // ─── Data Master ───
    Route::prefix('data-master')->name('admin.data-master.')->group(function () {
        Route::get('/tahun-ajaran', TahunAjaranIndex::class)->name('tahun-ajaran');
        Route::get('/mata-pelajaran', MataPelajaranIndex::class)->name('mata-pelajaran');
        Route::get('/data-kelas', DataKelasIndex::class)->name('data-kelas');
        Route::get('/jurusan', JurusanIndex::class)->name('jurusan');
    });

    // ─── Civitas Akademik ───
    Route::prefix('civitas')->name('admin.civitas.')->group(function () {
        Route::get('/data-siswa', DataSiswaIndex::class)->name('data-siswa');
        Route::get('/data-guru', DataGuruIndex::class)->name('data-guru');
        Route::get('/data-guru/create', DataGuruForm::class)->name('data-guru.create');
        Route::get('/data-guru/{guru}/edit', DataGuruForm::class)->name('data-guru.edit');
        Route::get('/data-guru/{guru}', DataGuruDetail::class)->name('data-guru.detail');
        Route::get('/data-pengguna', DataPenggunaIndex::class)->name('data-pengguna');
    });

    // ─── Keuangan ───
    Route::prefix('keuangan')->name('admin.keuangan.')->group(function () {
        Route::get('/master-spp', MasterSppIndex::class)->name('master-spp');
        Route::get('/transaksi-pembayaran', TransaksiPembayaranIndex::class)->name('transaksi-pembayaran');
        Route::get('/laporan-keuangan', LaporanKeuanganIndex::class)->name('laporan-keuangan');
        Route::get('/verifikasi-pembayaran', PaymentSubmissionReviewIndex::class)->name('verifikasi-pembayaran');
        Route::get('/kuitansi/cetak', [KeuanganPdfController::class, 'cetakKuitansi'])->name('kuitansi.cetak');
    });

    Route::get('/keuangan/pengajuan-pembayaran/{submission}/bukti', [PaymentSubmissionProofController::class, 'show'])
        ->name('admin.payment-submissions.proof');

    // ─── Kelulusan & Alumni ───
    Route::prefix('alumni')->name('admin.alumni.')->group(function () {
        Route::get('/jejak-alumni', JejakAlumniIndex::class)->name('jejak-alumni');
    });

    // ─── Website (Restricted) ───
    Route::prefix('website')->name('admin.website.')->group(function () {
        Route::get('/asset-media', MediaAssetIndex::class)->name('asset-media');
        Route::get('/galeri-slider', GaleriSliderIndex::class)->name('galeri-slider');
        Route::get('/modal-pengunjung', WebsitePopupIndex::class)->name('modal-pengunjung');
        Route::get('/pengaturan-umum', PengaturanUmumIndex::class)->name('pengaturan-umum');
        Route::get('/visi-misi', VisiMisiIndex::class)->name('visi-misi');
        Route::get('/struktur-organisasi', StructureOrganizationIndex::class)->name('struktur-organisasi');
        Route::get('/pengunjung', WebsiteVisitIndex::class)->name('pengunjung');
    });
});

Route::fallback(function () {
    abort(404);
})->middleware('track.website');
