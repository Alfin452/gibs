<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KehadiranHarian;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Wajib ada

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $id_guru = $user->guru ? $user->guru->id_guru : null;

        $query = KehadiranHarian::select(
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
            ->orderBy('tanggal', 'desc');

        if ($id_guru) {
            $query->where('id_guru', $id_guru);
        }


        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('kelas', function ($k) use ($search) {
                    $k->where('nama_kelas', 'like', "%{$search}%");
                })
                    ->orWhereHas('mapel', function ($m) use ($search) {
                        $m->where('nama_mapel', 'like', "%{$search}%");
                    });
            });
        }

        $riwayat = $query->paginate(10)->withQueryString();

        $jadwal_guru = Jadwal::where('id_guru', $id_guru)->with(['mapel', 'kelas'])->get();
        $mapels_list = $jadwal_guru->pluck('mapel')->unique('id_mapel')->values();
        $kelas_list = $jadwal_guru->pluck('kelas')->unique('id_kelas')->values();

        return view('absensi.index', compact('riwayat', 'mapels_list', 'kelas_list'));
    }

    public function create()
    {
        $user = Auth::user();

        if (!$user->guru) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak terdaftar sebagai Guru atau data Guru belum direlasikan!');
        }

        $id_guru_aktif = $user->guru->id_guru;

        $jadwal_guru = Jadwal::where('id_guru', $id_guru_aktif)
            ->with(['mapel', 'kelas'])
            ->get();

        if ($jadwal_guru->isEmpty()) {
            return redirect()->route('absensi.index')->with('warning', 'Halo ' . $user->guru->nama_guru . ', Anda belum memiliki jadwal mengajar di sistem.');
        }

        $mapels = $jadwal_guru->pluck('mapel')->unique('id_mapel')->values();
        $kelas = $jadwal_guru->pluck('kelas')->unique('id_kelas')->values();

        return view('absensi.create', compact('mapels', 'kelas'));
    }

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
            return redirect()->route('absensi.index')->with('warning', 'Absensi untuk tanggal ini sudah ada! Silakan edit di menu riwayat.');
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

        $user = Auth::user();
        if (!$user->guru) {
            return back()->with('error', 'Data guru tidak ditemukan untuk akun ini.');
        }
        $id_guru = $user->guru->id_guru; 

        $tahun_ajar = \App\Models\TahunAjar::where('status', 'Aktif')->first();
        $id_tahun_ajar = $tahun_ajar ? $tahun_ajar->id_tahun_ajar : 1;

        DB::beginTransaction();

        try {
            foreach ($request->status as $id_siswa => $status_kode) {

                KehadiranHarian::updateOrCreate(
                    [
                        'tanggal'  => $request->tanggal,
                        'id_siswa' => $id_siswa,
                        'id_mapel' => $request->id_mapel,
                    ],
                    [
                        'id_kelas'      => $request->id_kelas,
                        'id_guru'       => $id_guru, 
                        'id_tahun_ajar' => $id_tahun_ajar,
                        'status'        => $status_kode,
                        'keterangan'    => $request->keterangan[$id_siswa] ?? null,
                    ]
                );

                $this->updateRekapBulanan($id_siswa, $request->id_mapel, $request->id_kelas, $id_tahun_ajar, $request->tanggal, $id_guru);
            }

            DB::commit();

            return redirect()->route('absensi.index')->with('success', 'Absensi berhasil disimpan dan disinkronkan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id_kelas, $id_mapel, $tanggal)
    {
        $infoKelas = Kelas::find($id_kelas);
        $infoMapel = Mapel::find($id_mapel);

        $siswa = Siswa::where('id_kelas', $id_kelas)
            ->orderBy('nama_siswa', 'asc')
            ->get();

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
                'id_guru'          => $id_guru, // Update penanggung jawab bulan ini
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

        // Ambil jadwal guru login untuk filter awal
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

        $hari_mengajar = Jadwal::where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->pluck('hari')
            ->toArray();

        if (empty($hari_mengajar)) {
            $hari_mengajar = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        }

        $tanggal_pertemuan = [];
        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        for ($d = 1; $d <= $jumlah_hari; $d++) {
            $time = mktime(0, 0, 0, $bulan, $d, $tahun);
            $date = date('Y-m-d', $time);
            $nama_hari_inggris = date('l', $time);

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

        $data_absensi = KehadiranHarian::where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        $rekap = [];
        foreach ($data_absensi as $d) {
            $rekap[$d->id_siswa][$d->tanggal] = $d->status;
        }

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

    public function getTanggalAvailable(Request $request)
    {
        $id_kelas = $request->id_kelas;
        $id_mapel = $request->id_mapel;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $jadwal_hari = Jadwal::where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->pluck('hari')
            ->toArray();

        if (empty($jadwal_hari)) {
            return response()->json([]);
        }

        $list_tanggal = [];
        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        for ($d = 1; $d <= $jumlah_hari; $d++) {
            $time = mktime(0, 0, 0, $bulan, $d, $tahun);
            $date = date('Y-m-d', $time);
            $nama_hari_inggris = date('l', $time);

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

            if (in_array($hari_indo, $jadwal_hari)) {

                $sudah_absen = KehadiranHarian::where('tanggal', $date)
                    ->where('id_kelas', $id_kelas)
                    ->where('id_mapel', $id_mapel)
                    ->exists();

                $list_tanggal[] = [
                    'tanggal' => $date,
                    'hari' => $hari_indo,
                    'tampilan' => date('d F Y', strtotime($date)) . " ($hari_indo)",
                    'status' => $sudah_absen ? 'sudah' : 'belum' // Flag untuk UI
                ];
            }
        }

        return response()->json($list_tanggal);
    }

    public function daftarKelas(Request $request)
    {

        $query = Kelas::withCount('siswa'); // Menghitung jumlah siswa per kelas

        // Fitur Pencarian
        if ($request->has('search')) {
            $query->where('nama_kelas', 'like', '%' . $request->search . '%');
        }

        $kelas = $query->orderBy('nama_kelas', 'asc')->paginate(12);

        // Arahkan ke folder: resources/views/absensi/kelas/index.blade.php
        return view('absensi.kelas.index', compact('kelas'));
    }
}
