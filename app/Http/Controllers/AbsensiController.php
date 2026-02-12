<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KehadiranHarian;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    public function index()
    {
        $riwayat = KehadiranHarian::select(
            'tanggal',
            'id_kelas',
            'id_mapel',
            'id_guru',
            DB::raw('count(*) as total_siswa'),
            DB::raw('SUM(CASE WHEN status = "H" THEN 1 ELSE 0 END) as hadir'),
            DB::raw('SUM(CASE WHEN status = "S" THEN 1 ELSE 0 END) as sakit'),
            DB::raw('SUM(CASE WHEN status = "I" THEN 1 ELSE 0 END) as izin'),
            DB::raw('SUM(CASE WHEN status = "A" THEN 1 ELSE 0 END) as alpha'),
            DB::raw('SUM(CASE WHEN status = "L" THEN 1 ELSE 0 END) as libur')
        )
            ->with(['kelas', 'mapel']) 
            ->groupBy('tanggal', 'id_kelas', 'id_mapel', 'id_guru')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('absensi.index', compact('riwayat'));
    }

    public function create()
    {
        $id_guru_aktif = 15;

        $jadwal_guru = Jadwal::where('id_guru', $id_guru_aktif)
            ->with(['mapel', 'kelas']) // Load relasinya
            ->get();

        if ($jadwal_guru->isEmpty()) {
            return redirect()->route('absensi.index')->with('warning', 'Guru ID ' . $id_guru_aktif . ' tidak memiliki jadwal mengajar!');
        }

        $mapels = $jadwal_guru->pluck('mapel')->unique('id_mapel')->values();

        $kelas = $jadwal_guru->pluck('kelas')->unique('id_kelas')->values();

        return view('absensi.create', compact('mapels', 'kelas'));
    }

    //kode deploy jaga2
    // public function create()
    // {
    //     $user = Auth::user();

    //     if (!$user->guru) {
    //         // Kalau bukan guru (misal admin murni atau siswa), tolak akses
    //         return redirect()->route('dashboard')->with('error', 'Akun Anda tidak terdaftar sebagai Guru!');
    //     }

    //     // AMBIL ID GURU DARI SESI LOGIN
    //     $id_guru_aktif = $user->guru->id_guru;

    //     // Ambil Jadwal milik guru 
    //     $jadwal_guru = Jadwal::where('id_guru', $id_guru_aktif)
    //         ->with(['mapel', 'kelas'])
    //         ->get();

    //     if ($jadwal_guru->isEmpty()) {
    //         return redirect()->route('absensi.index')->with('warning', 'Halo ' . $user->guru->nama_guru . ', Anda belum memiliki jadwal mengajar di sistem.');
    //     }

    //     // Ambil Mapel & Kelas unik dari jadwal
    //     $mapels = $jadwal_guru->pluck('mapel')->unique('id_mapel')->values();
    //     $kelas = $jadwal_guru->pluck('kelas')->unique('id_kelas')->values();

    //     return view('absensi.create', compact('mapels', 'kelas'));
    // }

    public function cekLembar(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_mapel' => 'required',
            'id_kelas' => 'required',
        ]);

        $tanggal = $request->tanggal;
        $id_kelas = $request->id_kelas;
        $id_mapel = $request->id_mapel;

        $sudah_absen = KehadiranHarian::where('tanggal', $tanggal)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->exists();

        if ($sudah_absen) {
            return redirect()->route('absensi.index')->with('warning', 'Absensi untuk tanggal ini sudah ada! (Fitur Edit coming soon)');
        }

        $siswa = Siswa::where('id_kelas', $id_kelas)
            ->orderBy('nama_siswa', 'asc')
            ->get();

        $infoKelas = Kelas::find($id_kelas);
        $infoMapel = Mapel::find($id_mapel);

        return view('absensi.form', compact('siswa', 'tanggal', 'infoKelas', 'infoMapel'));
    }


    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tanggal' => 'required|date',
            'id_kelas' => 'required',
            'id_mapel' => 'required',
            'status'   => 'required|array', 
        ]);

        // 2. Setup Data Default
        $id_guru = 15;

        $tahun_ajar = \App\Models\TahunAjar::where('status', 'Aktif')->first();
        $id_tahun_ajar = $tahun_ajar ? $tahun_ajar->id_tahun_ajar : 1; // Fallback ke 1 kalau tidak ketemu

        DB::beginTransaction();

        try {
            foreach ($request->status as $id_siswa => $status_kode) {

                KehadiranHarian::updateOrCreate(
                    [
                        'tanggal'  => $request->tanggal,
                        'id_siswa' => $id_siswa,
                        'id_mapel' => $request->id_mapel, // Absen per mapel
                    ],
                    [
                        'id_kelas'      => $request->id_kelas,
                        'id_guru'       => $id_guru,
                        'id_tahun_ajar' => $id_tahun_ajar,
                        'status'        => $status_kode, // H, S, I, A, atau L
                        'keterangan'    => $request->keterangan[$id_siswa] ?? null,
                    ]
                );

                $this->updateRekapBulanan($id_siswa, $request->id_mapel, $request->id_kelas, $id_tahun_ajar, $request->tanggal, $id_guru);
            }

            DB::commit(); 

            return redirect()->route('absensi.index')->with('success', 'Absensi berhasil disimpan dan disinkronkan ke Erapor!');
        } catch (\Exception $e) {
            DB::rollback(); 
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // public function store(Request $request)
    // {
    //     // 1. Validasi Input
    //     $request->validate([
    //         'tanggal' => 'required|date',
    //         'id_kelas' => 'required',
    //         'id_mapel' => 'required',
    //         'status'   => 'required|array',
    //     ]);

    //     // 2. AMBIL ID GURU DARI USER LOGIN
    //     // Safety check lagi jaga-jaga
    //     if (!Auth::user()->guru) {
    //         return back()->with('error', 'Data guru tidak ditemukan.');
    //     }
    //     $id_guru = Auth::user()->guru->id_guru;

    //     // 3. Cari Tahun Ajar Aktif
    //     $tahun_ajar = TahunAjar::where('status', 'Aktif')->first();
    //     $id_tahun_ajar = $tahun_ajar ? $tahun_ajar->id_tahun_ajar : 1;

    //     DB::beginTransaction();

    //     try {
    //         foreach ($request->status as $id_siswa => $status_kode) {

    //             // A. Simpan ke Tabel Harian
    //             KehadiranHarian::updateOrCreate(
    //                 [
    //                     'tanggal'  => $request->tanggal,
    //                     'id_siswa' => $id_siswa,
    //                     'id_mapel' => $request->id_mapel,
    //                 ],
    //                 [
    //                     'id_kelas'      => $request->id_kelas,
    //                     'id_guru'       => $id_guru, // <-- Pakai ID Guru Login
    //                     'id_tahun_ajar' => $id_tahun_ajar,
    //                     'status'        => $status_kode,
    //                     'keterangan'    => $request->keterangan[$id_siswa] ?? null,
    //                 ]
    //             );

    //             // B. Sinkronisasi ke Tabel Lama
    //             $this->updateRekapBulanan($id_siswa, $request->id_mapel, $request->id_kelas, $id_tahun_ajar, $request->tanggal, $id_guru);
    //         }

    //         DB::commit();
    //         return redirect()->route('absensi.index')->with('success', 'Absensi berhasil disimpan!');
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    public function edit($id_kelas, $id_mapel, $tanggal)
    {
        // 1. Ambil Info Kelas & Mapel untuk Judul
        $infoKelas = Kelas::find($id_kelas);
        $infoMapel = Mapel::find($id_mapel);

        // 2. Ambil Daftar Siswa di Kelas tersebut
        $siswa = Siswa::where('id_kelas', $id_kelas)
            ->orderBy('nama_siswa', 'asc')
            ->get();

        // 3. AMBIL DATA KEHADIRAN YANG SUDAH ADA
        $dataKehadiran = KehadiranHarian::where('tanggal', $tanggal)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->get()
            ->keyBy('id_siswa'); 

        return view('absensi.form', compact('siswa', 'tanggal', 'infoKelas', 'infoMapel', 'dataKehadiran'));
    }

    private function updateRekapBulanan($id_siswa, $id_mapel, $id_kelas, $id_tahun_ajar, $tanggal, $id_guru)
    {
        $periode = date('m-Y', strtotime($tanggal));

        $stats = KehadiranHarian::where('id_siswa', $id_siswa)
            ->where('id_mapel', $id_mapel)
            ->whereRaw("DATE_FORMAT(tanggal, '%m-%Y') = ?", [$periode])
            ->selectRaw("
                COUNT(CASE WHEN status = 'H' THEN 1 END) as total_hadir,
                COUNT(CASE WHEN status = 'S' THEN 1 END) as total_sakit,
                COUNT(CASE WHEN status = 'I' THEN 1 END) as total_izin,
                COUNT(CASE WHEN status = 'A' THEN 1 END) as total_alpha
            ")
            ->first();

        
        DB::table('kehadiran_bulanan')->updateOrInsert(
            [
                'id_siswa' => $id_siswa,
                'id_mapel' => $id_mapel,
                'periode'  => $periode
            ],
            [
                'id_kelas'         => $id_kelas,
                'id_tahun_ajar'    => $id_tahun_ajar,
                'hadir'            => $stats->total_hadir,
                'sakit'            => $stats->total_sakit,
                'izin'             => $stats->total_izin,
                'tanpa_keterangan' => $stats->total_alpha,
                'id_guru'          => $id_guru,
                'is_lock'          => 0 
            ]
        );
    }

    public function laporan()
    {
        $user = Auth::user();

        if (!$user->guru) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $id_guru_aktif = $user->guru->id_guru;
        $jadwal_guru = Jadwal::where('id_guru', $id_guru_aktif)->with(['mapel', 'kelas'])->get();

        $mapels = $jadwal_guru->pluck('mapel')->unique('id_mapel')->values();
        $kelas = $jadwal_guru->pluck('kelas')->unique('id_kelas')->values();

        return view('absensi.laporan_filter', compact('mapels', 'kelas'));
    }

    public function prosesLaporan(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
            'id_mapel' => 'required',
            'id_kelas' => 'required',
        ]);

        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $id_kelas = $request->id_kelas;
        $id_mapel = $request->id_mapel;

        $siswa = Siswa::where('id_kelas', $id_kelas)->orderBy('nama_siswa', 'asc')->get();
        $infoKelas = Kelas::find($id_kelas);
        $infoMapel = Mapel::find($id_mapel);

        $id_guru = Auth::user()->guru->id_guru;
        $hari_mengajar = Jadwal::where('id_guru', $id_guru)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->pluck('hari')
            ->toArray();

        $tanggal_pertemuan = [];
        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        for ($d = 1; $d <= $jumlah_hari; $d++) {
            $time = mktime(0, 0, 0, $bulan, $d, $tahun);
            $date = date('Y-m-d', $time);
            $nama_hari_inggris = date('l', $time);

            // Mapping Hari
            $map_hari = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            ];
            $hari_indo = $map_hari[$nama_hari_inggris] ?? '';

            if (in_array($hari_indo, $hari_mengajar)) {
                $tanggal_pertemuan[] = $date;
            }
        }

        // Ambil Data Absensi Range Bulan Tersebut
        $data_absensi = KehadiranHarian::where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Mapping Data ke Array [id_siswa][tanggal]
        $rekap = [];
        foreach ($data_absensi as $d) {
            $rekap[$d->id_siswa][$d->tanggal] = $d->status;
        }

        // Tampilkan View Matriks
        return view('absensi.laporan_hasil', compact(
            'siswa',
            'infoKelas',
            'infoMapel',
            'bulan',
            'tahun',
            'tanggal_pertemuan',
            'rekap'
        ));
    }
}
