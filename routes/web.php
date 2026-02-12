<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    if ($user->role === 'guru') {
        return redirect()->route('absensi.index');
    }

    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('absensi')->name('absensi.')->group(function () {
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        Route::get('/buat-baru', [AbsensiController::class, 'create'])->name('create');
        Route::post('/cek-lembar', [AbsensiController::class, 'cekLembar'])->name('cek');
        Route::post('/store', [AbsensiController::class, 'store'])->name('store');
        Route::get('/edit/{id_kelas}/{id_mapel}/{tanggal}', [AbsensiController::class, 'edit'])->name('edit');
        Route::get('/laporan/view', [AbsensiController::class, 'prosesLaporan'])->name('laporan.view');
        Route::get('/laporan', [AbsensiController::class, 'laporan'])->name('laporan');
        Route::get('/get-tanggal', [AbsensiController::class, 'getTanggalAvailable'])->name('get-tanggal');
        Route::get('/daftar-kelas', [AbsensiController::class, 'daftarKelas'])->name('daftar-kelas');
    });
});

require __DIR__ . '/auth.php';
