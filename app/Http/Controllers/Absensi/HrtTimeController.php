<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KehadiranHrt;
use App\Models\Siswa;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\TahunAjar; 

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

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
            'absen' => 'nullable|array',
        ]);

        $user = Auth::user();

        if (!$user->guru || !$user->guru->is_hrt) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak! Menu ini khusus untuk Homeroom Teacher.');
        }

        $id_kelas = $user->guru->id_kelas;
        $id_guru = $user->guru->id_guru;

        $tahunAjar = TahunAjar::where('status', 'Aktif')->first();
        $id_tahun_ajar = $tahunAjar ? $tahunAjar->id_tahun_ajar : null;

        $absenData = $request->input('absen', []);

        foreach ($absenData as $id_siswa => $dates) {
            foreach ($dates as $tanggal => $status) {
                // Jika input tidak kosong, simpan/update ke database
                if (!empty($status)) {
                    KehadiranHrt::updateOrCreate(
                        [
                            'id_siswa' => $id_siswa,
                            'tanggal' => $tanggal,
                            'id_kelas' => $id_kelas,
                        ],
                        [
                            'id_guru' => $id_guru,
                            'id_tahun_ajar' => $id_tahun_ajar,
                            'status' => strtoupper($status),
                            'keterangan' => 'Manual Input'
                        ]
                    );
                } else {
                    // Jika input dikosongkan (dihapus user), hapus data kehadiran dari DB
                    KehadiranHrt::where('id_siswa', $id_siswa)
                        ->where('tanggal', $tanggal)
                        ->where('id_kelas', $id_kelas)
                        ->delete();
                }
            }
        }

        return redirect()->route('hrt.time.index', ['bulan' => $request->bulan, 'tahun' => $request->tahun])
            ->with('success', 'Data kehadiran berhasil disimpan!');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv',
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $user = Auth::user();
        $id_kelas = $user->guru->id_kelas;
        $id_guru = $user->guru->id_guru;
        $bulan = (int) $request->bulan;
        $tahun = $request->tahun;

        $tahunAjar = TahunAjar::where('status', 'Aktif')->first();
        $id_tahun_ajar = $tahunAjar ? $tahunAjar->id_tahun_ajar : null;

        // Offset kolom di Excel berdasarkan Bulan (sesuai format template Semester 2)
        // Array index mulai dari 0. Jan=3, Feb=40, Mar=74, Apr=111, Mei=147, Jun=184
        $startCols = [
            1 => 3,
            2 => 40,
            3 => 74,
            4 => 111,
            5 => 147,
            6 => 184
        ];

        if (!isset($startCols[$bulan])) {
            return back()->with('error', 'Format bulan ini belum disetting untuk import otomatis (Hanya Jan-Jun).');
        }

        $startCol = $startCols[$bulan];
        $file = $request->file('file_excel');

        // Baca data excel menjadi array
        $data = Excel::toArray([], $file)[0];

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $importedCount = 0;

        foreach ($data as $rowIndex => $row) {
            // Lewati baris header (Baris ke 1 dan 2)
            if ($rowIndex < 2) continue;

            // Kolom 'NAME' ada di index 1
            $nama_siswa = trim($row[1] ?? '');
            if (empty($nama_siswa)) continue;

            // Cari siswa berdasarkan nama
            $siswa = Siswa::where('id_kelas', $id_kelas)
                ->where('nama_siswa', 'like', "%{$nama_siswa}%")
                ->first();

            if ($siswa) {
                for ($i = 0; $i < $daysInMonth; $i++) {
                    $tanggal = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT);
                    $colIndex = $startCol + $i;
                    $val = strtolower(trim($row[$colIndex] ?? ''));

                    // MAPPING: 1->H, a->A, p->I, s->S
                    $status = null;
                    if ($val === '1' || $val === 'h') $status = 'H';
                    elseif ($val === 'a') $status = 'A';
                    elseif ($val === 'p' || $val === 'i') $status = 'I';
                    elseif ($val === 's') $status = 'S';
                    elseif ($val === 'l') $status = 'L';

                    if ($status) {
                        KehadiranHrt::updateOrCreate(
                            [
                                'id_siswa' => $siswa->id_siswa,
                                'tanggal' => $tanggal,
                                'id_kelas' => $id_kelas,
                            ],
                            [
                                'id_guru' => $id_guru,
                                'id_tahun_ajar' => $id_tahun_ajar,
                                'status' => $status,
                                'keterangan' => 'Imported from Excel'
                            ]
                        );
                        $importedCount++;
                    }
                }
            }
        }
        return back()->with('success', "Berhasil mengimport {$importedCount} rekaman absensi.");
    }
}
