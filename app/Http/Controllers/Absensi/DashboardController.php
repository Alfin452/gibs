<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\Siswa;
use App\Models\KehadiranHarian;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Force Bahasa Indonesia untuk manipulasi tanggal di Controller ini
        Carbon::setLocale('id');

        $user = Auth::user();
        $guru = $user->guru;

        if (!$guru) {
            return view('absensi.dashboard', [
                'error' => 'Akun tidak terhubung dengan data Guru.',
                'total_siswa' => 0,
                'total_kelas' => 0,
                'total_mapel' => 0,
                'progress' => 0,
                'tunggakan_absen' => [], // Fix variabel name agar konsisten
                'sudah_absen' => 0,
                'total_wajib_absen' => 0
            ]);
        }

        $id_guru = $guru->id_guru;

        // --- DATA STATISTIK ---
        $jadwal_semua = Jadwal::where('id_guru', $id_guru)->get();
        $total_kelas = $jadwal_semua->pluck('id_kelas')->unique()->count();
        $total_mapel = $jadwal_semua->pluck('id_mapel')->unique()->count();

        $kelas_ids = $jadwal_semua->pluck('id_kelas')->unique();
        $total_siswa = Siswa::whereIn('id_kelas', $kelas_ids)->count();

        // --- LOGIKA PROGRESS & PENGINGAT ---
        $bulan_ini = Carbon::now()->month;
        $tahun_ini = Carbon::now()->year;

        $start_date = Carbon::createFromDate($tahun_ini, $bulan_ini, 1);
        $end_date = Carbon::now();

        $total_wajib_absen = 0;
        $sudah_absen = 0;
        $tunggakan_absen = [];

        // Loop tanggal
        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            if ($date->isSunday()) continue;

            // Kita gunakan translatedFormat('l') untuk mendapatkan nama hari (Senin, Selasa...)
            // Pastikan database kolom 'hari' isinya: Senin, Selasa, Rabu, Kamis, Jumat, Sabtu
            $nama_hari_indo = $date->translatedFormat('l');
            $tanggal_sql = $date->format('Y-m-d');

            // Cari jadwal pada hari tersebut
            // Note: Pastikan data di DB kolom 'hari' sesuai dengan output $nama_hari_indo
            $jadwal_harian = $jadwal_semua->where('hari', $nama_hari_indo);

            foreach ($jadwal_harian as $jadwal) {
                $total_wajib_absen++;

                $cek = KehadiranHarian::where('id_guru', $id_guru)
                    ->where('id_kelas', $jadwal->id_kelas)
                    ->where('id_mapel', $jadwal->id_mapel)
                    ->whereDate('tanggal', $tanggal_sql)
                    ->exists();

                if ($cek) {
                    $sudah_absen++;
                } else {
                    $tunggakan_absen[] = [
                        'tanggal' => $date->translatedFormat('d F Y'),
                        'hari' => $nama_hari_indo,
                        'kelas' => $jadwal->kelas->nama_kelas ?? '-',
                        'mapel' => $jadwal->mapel->nama_mapel ?? '-',
                        'jam' => Carbon::parse($jadwal->jam_mulai)->format('H:i'),

                        'link' => route('absensi.edit', [
                            'id_kelas' => $jadwal->id_kelas,
                            'id_mapel' => $jadwal->id_mapel,
                            'tanggal' => $tanggal_sql // Format: Y-m-d
                        ])
                    ];
                }
            }
        }

        $progress = ($total_wajib_absen > 0) ? round(($sudah_absen / $total_wajib_absen) * 100) : 0;

        return view('absensi.dashboard', compact(
            'guru',
            'total_siswa',
            'total_kelas',
            'total_mapel',
            'progress',
            'tunggakan_absen',
            'sudah_absen',
            'total_wajib_absen'
        ));
    }
}
