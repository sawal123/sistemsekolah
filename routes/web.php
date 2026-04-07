<?php

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

// ─── Admin Routes (protected) ──────────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});
