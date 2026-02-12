<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// 1. HALAMAN UTAMA (ROOT)
Route::get('/', function () {
    // Jika belum login, lempar ke halaman login
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    // Jika sudah login, cek role
    $user = Auth::user();
    if ($user->role === 'guru') {
        return redirect()->route('absensi.index');
    }

    // Role selain guru ke dashboard biasa
    return redirect()->route('dashboard');
});

// 2. DASHBOARD UMUM
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. GROUP ROUTE YANG BUTUH LOGIN
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // === ROUTE ABSENSI (HANYA GURU) ===
    // Tambahkan middleware cek role kalau perlu, tapi auth saja cukup dulu
    Route::prefix('absensi')->name('absensi.')->group(function () {
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        Route::get('/buat-baru', [AbsensiController::class, 'create'])->name('create');
        Route::post('/cek-lembar', [AbsensiController::class, 'cekLembar'])->name('cek');
        Route::post('/store', [AbsensiController::class, 'store'])->name('store');
        Route::get('/edit/{id_kelas}/{id_mapel}/{tanggal}', [AbsensiController::class, 'edit'])->name('edit');
        Route::get('/laporan/view', [AbsensiController::class, 'prosesLaporan'])->name('laporan.view');
        Route::get('/laporan', [AbsensiController::class, 'laporan'])->name('laporan');
        Route::get('/get-tanggal', [AbsensiController::class, 'getTanggalAvailable'])->name('get-tanggal');
        });
});

require __DIR__ . '/auth.php';
