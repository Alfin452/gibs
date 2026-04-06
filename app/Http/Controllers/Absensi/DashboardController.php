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
    /**
     * Dashboard Index
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
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
                'tunggakan_absen' => [],
                'sudah_absen' => 0,
                'total_wajib_absen' => 0
            ]);
        }

        $id_guru = $guru->id_guru;

        // --- DATA STATISTIK ---
        $jadwal_semua = Jadwal::where('id_guru', $id_guru)->get();
        
        // PERBAIKAN: Hitung total kelas dengan membedakan antara id_kelas dan id_major
        $total_kelas = $jadwal_semua->map(function ($jadwal) {
            return $jadwal->id_major ? 'M_' . $jadwal->id_major : 'K_' . $jadwal->id_kelas;
        })->filter(function ($item) {
            return $item !== 'M_' && $item !== 'K_'; // Memastikan data yang kosong/null tidak ikut terhitung
        })->unique()->count();

        $total_mapel = $jadwal_semua->pluck('id_mapel')->unique()->count();

        // Tambahkan filter() agar nilai null dari jadwal major tidak ikut masuk ke query whereIn
        $kelas_ids = $jadwal_semua->pluck('id_kelas')->filter()->unique();
        $total_siswa = Siswa::whereIn('id_kelas', $kelas_ids)->count();

        $kelas_hrt = null;
        $jumlah_siswa_hrt = 0;

        if ($guru->is_hrt == 1 && $guru->id_kelas != null) {
            $kelas_hrt = \App\Models\Kelas::find($guru->id_kelas);
            if ($kelas_hrt) {
                $jumlah_siswa_hrt = Siswa::where('id_kelas', $guru->id_kelas)->count();
            }
        }

        $bulan_ini = Carbon::now()->month;
        $tahun_ini = Carbon::now()->year;

        $start_date = Carbon::createFromDate($tahun_ini, $bulan_ini, 1);
        $end_date = Carbon::now();

        // Format tanggal untuk pencarian database
        $start_date_sql = $start_date->format('Y-m-d');
        $end_date_sql = $end_date->format('Y-m-d');

        $data_absensi_bulan_ini = KehadiranHarian::where('id_guru', $id_guru)
            ->whereBetween('tanggal', [$start_date_sql, $end_date_sql])
            ->select('tanggal', 'id_kelas', 'id_mapel', 'id_major')
            ->get();

        // Buat Hash Map (Array Asosiatif) untuk pencarian cepat
        $map_absensi = [];
        foreach ($data_absensi_bulan_ini as $absen) {
            $tgl = Carbon::parse($absen->tanggal)->format('Y-m-d');
            $target_id = $absen->id_major ? 'M' . $absen->id_major : $absen->id_kelas;
            $key = $tgl . '_' . $target_id . '_' . $absen->id_mapel;
            $map_absensi[$key] = true;
        }
        // =========================================================================

        $total_wajib_absen = 0;
        $sudah_absen = 0;
        $tunggakan_absen = [];

        // Loop tanggal
        for ($date = $start_date->copy(); $date->lte($end_date); $date->addDay()) {
            if ($date->isSunday()) continue;

            $nama_hari_indo = $date->translatedFormat('l');
            $tanggal_sql = $date->format('Y-m-d');

            $jadwal_harian = $jadwal_semua->where('hari', $nama_hari_indo);

            foreach ($jadwal_harian as $jadwal) {
                $total_wajib_absen++;

                // =========================================================================
                // PERBAIKAN 2: Cek data dari memori RAM (array $map_absensi)
                // =========================================================================
                $target_id = $jadwal->id_major ? 'M' . $jadwal->id_major : $jadwal->id_kelas;
                $key_cek = $tanggal_sql . '_' . $target_id . '_' . $jadwal->id_mapel;
                $cek = isset($map_absensi[$key_cek]);

                if ($cek) {
                    $sudah_absen++;
                } else {
                    $nama_kelas_tampil = $jadwal->id_major ? ($jadwal->major->nama_major ?? 'Major') : ($jadwal->kelas->nama_kelas ?? '-');

                    $link_url = '#';
                    if ($target_id && $jadwal->id_mapel) {
                        try {
                            $link_url = route('absensi.edit', [
                                'id_kelas' => $target_id,
                                'id_mapel' => $jadwal->id_mapel,
                                'tanggal' => $tanggal_sql
                            ]);
                        } catch (\Exception $e) {
                            $link_url = '#';
                        }
                    }

                    $tunggakan_absen[] = [
                        'tanggal' => $date->translatedFormat('d F Y'),
                        'hari' => $nama_hari_indo,
                        'kelas' => $nama_kelas_tampil,
                        'mapel' => $jadwal->mapel->nama_mapel ?? '-',
                        'jam' => Carbon::parse($jadwal->jam_mulai)->format('H:i'),
                        'link' => $link_url
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
            'total_wajib_absen',
            'kelas_hrt',
            'jumlah_siswa_hrt'
        ));
    }
}
