<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('absensi.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';


Route::prefix('absensi')->name('absensi.')->group(function () {
    Route::get('/', [AbsensiController::class, 'index'])->name('index');
    Route::get('/buat-baru', [AbsensiController::class, 'create'])->name('create');
    Route::post('/cek-lembar', [AbsensiController::class, 'cekLembar'])->name('cek');
    Route::post('/store', [AbsensiController::class, 'store'])->name('store');
    Route::get('/edit/{id_kelas}/{id_mapel}/{tanggal}', [AbsensiController::class, 'edit'])->name('edit');
    });
