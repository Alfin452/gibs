<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KehadiranHrt;
use App\Models\Siswa;

class HrtTimeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->guru || !$user->guru->is_hrt) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak! Menu ini khusus untuk Homeroom Teacher.');
        }

        $guru = $user->guru;
        $id_kelas = $guru->id_kelas;

        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $dates = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dateString = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $dayOfWeek = date('N', strtotime($dateString)); // 1 (Senin) - 7 (Minggu)

            $dates[] = [
                'tgl' => $i,
                'is_sunday' => ($dayOfWeek == 7),
                'full_date' => $dateString
            ];
        }

        $siswa = Siswa::where('id_kelas', $id_kelas)->orderBy('nama_siswa', 'asc')->get();

        $kehadiranRaw = KehadiranHrt::where('id_kelas', $id_kelas)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $kehadiran = [];
        foreach ($kehadiranRaw as $absen) {
            $tgl = date('j', strtotime($absen->tanggal)); // Ambil angka tanggalnya saja (1-31)
            $kehadiran[$absen->id_siswa . '_' . $tgl] = $absen->status;
        }

        return view('absensi.hrt_time.index', compact('guru', 'id_kelas', 'bulan', 'tahun', 'dates', 'siswa', 'kehadiran'));
    }
}
