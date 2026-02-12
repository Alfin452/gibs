<?php

namespace App\Http\Controllers;

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
        $user = Auth::user();

        // Pastikan user terhubung dengan data Guru
        $guru = $user->guru;

        if (!$guru) {
            return view('dashboard', [
                'error' => 'Akun tidak terhubung dengan data Guru.',
                'total_siswa' => 0,
                'total_kelas' => 0,
                'total_mapel' => 0,
                'progress' => 0,
                'tunggakan' => []
            ]);
        }

        $id_guru = $guru->id_guru;

        // 1. DATA STATISTIK UTAMA
        // Ambil semua jadwal guru ini
        $jadwal_semua = Jadwal::where('id_guru', $id_guru)->get();

        // Hitung Kelas unik & Mapel unik
        $total_kelas = $jadwal_semua->pluck('id_kelas')->unique()->count();
        $total_mapel = $jadwal_semua->pluck('id_mapel')->unique()->count();

        // Hitung Total Siswa (dari kelas yang diajar)
        $kelas_ids = $jadwal_semua->pluck('id_kelas')->unique();
        $total_siswa = Siswa::whereIn('id_kelas', $kelas_ids)->count();

        // 2. LOGIKA PROGRESS & PENGINGAT (Bulan Ini)
        $bulan_ini = Carbon::now()->month;
        $tahun_ini = Carbon::now()->year;
        $hari_ini = Carbon::now();

        // Buat rentang tanggal dari awal bulan sampai hari ini
        $start_date = Carbon::createFromDate($tahun_ini, $bulan_ini, 1);
        $end_date = Carbon::now(); // Sampai hari ini saja

        $total_wajib_absen = 0;
        $sudah_absen = 0;
        $tunggakan_absen = [];

        // Mapping hari Inggris ke Indonesia (sesuai database jadwal)
        $map_hari = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        // Loop setiap hari dari tanggal 1 sampai hari ini
        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {

            // Jangan hitung hari Minggu (opsional)
            if ($date->isSunday()) continue;

            $nama_hari = $map_hari[$date->format('l')];
            $tanggal_sql = $date->format('Y-m-d');

            // Cari jadwal guru pada hari tersebut
            $jadwal_harian = $jadwal_semua->where('hari', $nama_hari);

            foreach ($jadwal_harian as $jadwal) {
                $total_wajib_absen++;

                // Cek apakah sudah ada input di kehadiran_harian
                $cek = KehadiranHarian::where('id_guru', $id_guru)
                    ->where('id_kelas', $jadwal->id_kelas)
                    ->where('id_mapel', $jadwal->id_mapel)
                    ->whereDate('tanggal', $tanggal_sql)
                    ->exists();

                if ($cek) {
                    $sudah_absen++;
                } else {
                    // Masukkan ke daftar tunggakan/pengingat
                    $tunggakan_absen[] = [
                        'tanggal' => $date->translatedFormat('d F Y'),
                        'hari' => $nama_hari,
                        'kelas' => $jadwal->kelas->nama_kelas ?? '-',
                        'mapel' => $jadwal->mapel->nama_mapel ?? '-',
                        'jam' => \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i'),
                        // Link untuk input cepat
                        'link' => route('absensi.create') // Nanti bisa dikembangkan auto-fill
                    ];
                }
            }
        }

        // Hitung Persentase Progress
        $progress = ($total_wajib_absen > 0) ? round(($sudah_absen / $total_wajib_absen) * 100) : 0;

        return view('dashboard', compact(
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
