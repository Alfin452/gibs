<?php

use App\Models\User;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Absensi\AbsensiController;
use App\Http\Controllers\Absensi\DashboardController;
use App\Http\Controllers\Absensi\HrtTimeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    // Langsung arahkan ke Dashboard untuk melihat statistik & pengingat
    return redirect()->route('dashboard');
});

// PENTING: Gunakan DashboardController, bukan function() biasa
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/absensi/show/{id_kelas}/{id_mapel}', [App\Http\Controllers\Absensi\AbsensiController::class, 'show'])->name('absensi.show');
    Route::get('/absensi/detail/{id_kelas}/{id_mapel}', [App\Http\Controllers\Absensi\AbsensiController::class, 'detail'])->name('absensi.detail');

    Route::get('/hrt-time', [HrtTimeController::class, 'index'])->name('hrt.time.index');
    Route::post('/hrt-time', [HrtTimeController::class, 'store'])->name('hrt.time.store');
    Route::post('/hrt-time/import', [HrtTimeController::class, 'importExcel'])->name('hrt.time.import'); // Route baru
    // Group Absensi
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

Route::get('/sso-login', function (Request $request) {
    $token = $request->query('token');

    if (!$token) {
        return redirect('/login')->withErrors(['sso' => 'Akses tidak sah.']);
    }

    // Cari user yang memiliki sso_token yang cocok
    $user = User::where('sso_token', $token)->first();

    if ($user) {
        // Otomatis login-kan user tersebut ke Laravel
        Auth::login($user);

        // Langsung hapus tokennya agar aman dan tidak bisa dipakai 2 kali
        $user->update(['sso_token' => null]);

        // Arahkan ke rute dashboard presensi kamu
        // Ganti 'dashboard' dengan nama rute dashboard yang kamu punya
        return redirect()->route('dashboard');
    }

    return redirect('/login')->withErrors(['sso' => 'Token sesi tidak valid. Silakan login kembali melalui menu utama.']);
});

require __DIR__ . '/auth.php';
