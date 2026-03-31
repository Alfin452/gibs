<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
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
            return redirect()->route('dashboard')->with('error', 'Akun Anda tidak terdaftar sebagai Guru!');
        }

        $id_guru_aktif = $user->guru->id_guru;

        // 1. Ambil jadwal beserta relasi kelas, mapel, DAN major
        $jadwal_guru = Jadwal::where('id_guru', $id_guru_aktif)
            ->with(['mapel', 'kelas', 'major'])
            ->orderBy('id_kelas')
            ->orderBy('id_major')
            ->get();

        if ($jadwal_guru->isEmpty()) {
            return redirect()->route('absensi.index')->with('warning', 'Halo ' . $user->guru->nama_guru . ', Anda belum memiliki jadwal mengajar.');
        }

        // 2. GROUPING BARU: Kelompokkan dengan prefix untuk membedakan Kelas dan Major
        $kelompok_jadwal = $jadwal_guru->groupBy(function ($item) {
            // Jika ada id_major, gunakan awalan 'MAJOR-', jika tidak gunakan 'KELAS-'
            if ($item->id_major) {
                return 'MAJOR-' . $item->id_major . '-' . $item->id_mapel;
            } else {
                return 'KELAS-' . $item->id_kelas . '-' . $item->id_mapel;
            }
        });

        return view('absensi.create', compact('kelompok_jadwal'));
    }

    public function cekLembar(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kombinasi_jadwal' => 'required',
        ]);

        $inputDate = Carbon::parse($request->tanggal)->startOfDay();
        $serverDate = Carbon::now()->startOfDay();

        if ($inputDate->gt($serverDate)) {
            return back()->with('error', 'Anda tidak dapat melakukan absensi untuk tanggal di masa depan (Waktu Server).');
        }

        // Pecah string value menjadi 3 bagian (Tipe - ID Target - ID Mapel)
        $ids = explode('-', $request->kombinasi_jadwal);

        if (count($ids) !== 3) {
            return back()->with('error', 'Format jadwal tidak valid.');
        }

        $tipe = $ids;
        $id_target = $ids;
        $id_mapel = $ids;
        $tanggal = $request->tanggal;

        if (!$this->cekOtorisasiGuru($id_target, $id_mapel, $tipe)) {
            abort(403, 'Akses Ditolak: Anda tidak mengajar di kelas/major & mata pelajaran tersebut.');
        }

        // Cek Duplikasi
        $query_duplikasi = KehadiranHarian::where('tanggal', $tanggal)
            ->where('id_mapel', $id_mapel);

        if ($tipe === 'kelas') {
            $query_duplikasi->where('id_kelas', $id_target);
        }

        if ($query_duplikasi->exists()) {
            return redirect()->route('absensi.index')->with('warning', 'Absensi untuk tanggal ini sudah ada! Silakan edit di menu riwayat.');
        }

        // Ambil data Siswa sesuai dengan Tipe (Kelas / Major)
        if ($tipe === 'major') {
            $siswa = Siswa::where('id_major', $id_target)->orderBy('nama_siswa', 'asc')->get();
            // Buat objek dummy agar tampilan form.blade.php tidak error mencari variabel $infoKelas
            $major = \App\Models\Major::find($id_target);
            $infoKelas = (object) ['id_kelas' => null, 'nama_kelas' => $major->nama_major ?? 'Major'];
        } else {
            $siswa = Siswa::where('id_kelas', $id_target)->orderBy('nama_siswa', 'asc')->get();
            $infoKelas = Kelas::find($id_target);
        }

        $infoMapel = Mapel::find($id_mapel);
        $tanggal_absen = Carbon::parse($tanggal)->startOfDay();

        // 1. Siswa yang MASIH SAKIT (Belum ada yang meng-absen H)
        $siswa_masih_sakit_db = DB::table('sakit_siswa')
            ->where('status_akhir', 'Masih Sakit')
            ->where('tanggal', '<=', $tanggal)
            ->get();

        $siswa_masih_sakit = collect();
        foreach ($siswa_masih_sakit_db as $sakit) {
            $tgl_mulai = Carbon::parse($sakit->tanggal)->startOfDay();
            $hari_sakit = 0;
            for ($date = $tgl_mulai->copy(); $date->lte($tanggal_absen); $date->addDay()) {
                if (!$date->isSunday()) $hari_sakit++;
            }
            $sakit->durasi_hari = $hari_sakit;
            $siswa_masih_sakit->put($sakit->id_siswa, $sakit);
        }

        // 2. Siswa yang BARU SEMBUH HARI INI
        $siswa_baru_sembuh_db = DB::table('sakit_siswa')
            ->where('status_akhir', 'Kembali ke Kelas')
            ->whereDate('updated_at', Carbon::parse($tanggal)->toDateString())
            ->get();

        $siswa_baru_sembuh = collect();
        foreach ($siswa_baru_sembuh_db as $sembuh) {
            $konfirmasi = DB::table('kehadiran_harian')
                ->join('guru', 'kehadiran_harian.id_guru', '=', 'guru.id_guru')
                ->join('mapel', 'kehadiran_harian.id_mapel', '=', 'mapel.id_mapel')
                ->where('kehadiran_harian.id_siswa', $sembuh->id_siswa)
                ->where('kehadiran_harian.tanggal', $tanggal)
                ->where('kehadiran_harian.status', 'H')
                ->orderBy('kehadiran_harian.updated_at', 'asc')
                ->select('guru.nama_guru', 'mapel.nama_mapel')
                ->first();

            if ($konfirmasi) {
                $sembuh->nama_guru = $konfirmasi->nama_guru;
                $sembuh->nama_mapel = $konfirmasi->nama_mapel;
                $siswa_baru_sembuh->put($sembuh->id_siswa, $sembuh);
            }
        }

        return view('absensi.form', compact('siswa', 'tanggal', 'infoKelas', 'infoMapel', 'siswa_masih_sakit', 'siswa_baru_sembuh'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_kelas' => 'required',
            'id_mapel' => 'required',
            'status'   => 'required|array',
        ]);

        if (!$this->cekOtorisasiGuru($request->id_kelas, $request->id_mapel)) {
            abort(403, 'Akses Ditolak: Percobaan manipulasi data terdeteksi.');
        }

        $inputDate = Carbon::parse($request->tanggal)->startOfDay();
        $serverDate = Carbon::now()->startOfDay();

        if ($inputDate->gt($serverDate)) {
            return back()->with('error', 'Manipulasi tanggal terdeteksi. Gunakan waktu server yang valid.');
        }

        $user = Auth::user();
        if (!$user->guru) {
            return back()->with('error', 'Data guru tidak ditemukan untuk akun ini.');
        }
        $id_guru = $user->guru->id_guru;

        $tahun_ajar = \App\Models\TahunAjar::where('status', 'Aktif')->first();
        $id_tahun_ajar = $tahun_ajar ? $tahun_ajar->id_tahun_ajar : 1;

        DB::beginTransaction();

        try {
            $siswa_hadir_ids = [];
            $all_siswa_ids = array_keys($request->status);

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

                if ($status_kode === 'H') {
                    $siswa_hadir_ids[] = $id_siswa;
                }
            }

            if (!empty($siswa_hadir_ids)) {
                DB::table('sakit_siswa')
                    ->whereIn('id_siswa', $siswa_hadir_ids)
                    ->where('status_akhir', 'Masih Sakit')
                    ->update([
                        'status_akhir' => 'Kembali ke Kelas',
                        'updated_at' => now()
                    ]);
            }

            $this->updateRekapBulananMassal($all_siswa_ids, $request->id_mapel, $request->id_kelas, $id_tahun_ajar, $request->tanggal, $id_guru);

            DB::commit();

            return redirect()->route('absensi.index')->with('success', 'Absensi berhasil disimpan dan disinkronkan!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id_kelas, $id_mapel, $tanggal)
    {
        if (!$this->cekOtorisasiGuru($id_kelas, $id_mapel)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki jadwal untuk mengedit absensi kelas ini.');
        }

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

        $tanggal_absen = Carbon::parse($tanggal)->startOfDay();

        $siswa_masih_sakit_db = DB::table('sakit_siswa')
            ->where('status_akhir', 'Masih Sakit')
            ->where('tanggal', '<=', $tanggal)
            ->get();

        $siswa_masih_sakit = collect();
        foreach ($siswa_masih_sakit_db as $sakit) {
            $tgl_mulai = Carbon::parse($sakit->tanggal)->startOfDay();
            $hari_sakit = 0;
            for ($date = $tgl_mulai->copy(); $date->lte($tanggal_absen); $date->addDay()) {
                if (!$date->isSunday()) $hari_sakit++;
            }
            $sakit->durasi_hari = $hari_sakit;
            $siswa_masih_sakit->put($sakit->id_siswa, $sakit);
        }

        $siswa_baru_sembuh_db = DB::table('sakit_siswa')
            ->where('status_akhir', 'Kembali ke Kelas')
            ->whereDate('updated_at', Carbon::parse($tanggal)->toDateString()) // Sembuh pada hari absen ini
            ->get();

        $siswa_baru_sembuh = collect();
        foreach ($siswa_baru_sembuh_db as $sembuh) {
            $konfirmasi = DB::table('kehadiran_harian')
                ->join('guru', 'kehadiran_harian.id_guru', '=', 'guru.id_guru')
                ->join('mapel', 'kehadiran_harian.id_mapel', '=', 'mapel.id_mapel')
                ->where('kehadiran_harian.id_siswa', $sembuh->id_siswa)
                ->where('kehadiran_harian.tanggal', $tanggal)
                ->where('kehadiran_harian.status', 'H')
                ->orderBy('kehadiran_harian.updated_at', 'asc') // Cari yang paling awal
                ->select('guru.nama_guru', 'mapel.nama_mapel')
                ->first();

            if ($konfirmasi) {
                $sembuh->nama_guru = $konfirmasi->nama_guru;
                $sembuh->nama_mapel = $konfirmasi->nama_mapel;
                $siswa_baru_sembuh->put($sembuh->id_siswa, $sembuh);
            }
        }

        return view('absensi.form', compact('siswa', 'tanggal', 'infoKelas', 'infoMapel', 'siswa_masih_sakit', 'siswa_baru_sembuh', 'dataKehadiran'));
    }

    private function updateRekapBulananMassal($siswa_ids, $id_mapel, $id_kelas, $id_tahun_ajar, $tanggal, $id_guru)
    {
        $bulan = date('m', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));
        $periode = date('m-Y', strtotime($tanggal));

        $stats = KehadiranHarian::whereIn('id_siswa', $siswa_ids)
            ->where('id_mapel', $id_mapel)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->selectRaw("id_siswa,
                COUNT(CASE WHEN status = 'H' THEN 1 END) as total_hadir,
                COUNT(CASE WHEN status = 'S' THEN 1 END) as total_sakit,
                COUNT(CASE WHEN status = 'I' THEN 1 END) as total_izin,
                COUNT(CASE WHEN status = 'A' THEN 1 END) as total_alpha
            ")
            ->groupBy('id_siswa')
            ->get()
            ->keyBy('id_siswa'); 

        foreach ($siswa_ids as $id_siswa) {
            $statSiswa = $stats->get($id_siswa);

            DB::table('kehadiran_bulanan')->updateOrInsert(
                [
                    'id_siswa' => $id_siswa,
                    'id_mapel' => $id_mapel,
                    'periode'  => $periode
                ],
                [
                    'id_kelas'         => $id_kelas,
                    'id_tahun_ajar'    => $id_tahun_ajar,
                    'hadir'            => $statSiswa ? $statSiswa->total_hadir : 0,
                    'sakit'            => $statSiswa ? $statSiswa->total_sakit : 0,
                    'izin'             => $statSiswa ? $statSiswa->total_izin : 0,
                    'tanpa_keterangan' => $statSiswa ? $statSiswa->total_alpha : 0,
                    'id_guru'          => $id_guru,
                    'is_lock'          => 0
                ]
            );
        }
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
        $id_major = $request->id_major;
        $id_mapel = $request->id_mapel;
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        // Cek jadwal berdasarkan Major atau Kelas
        $query_jadwal = Jadwal::where('id_mapel', $id_mapel);
        if ($id_major) {
            $query_jadwal->where('id_major', $id_major);
        } else {
            $query_jadwal->where('id_kelas', $id_kelas);
        }

        $jadwal_hari = $query_jadwal->pluck('hari')->toArray();

        if (empty($jadwal_hari)) {
            return response()->json([]);
        }

        $list_tanggal = [];
        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $start_date = date('Y-m-d', mktime(0, 0, 0, $bulan, 1, $tahun));
        $end_date = date('Y-m-d', mktime(0, 0, 0, $bulan, $jumlah_hari, $tahun));

        // Tarik data absen untuk mengecek tanggal mana yang sudah diisi
        $absen_query = KehadiranHarian::where('id_mapel', $id_mapel)
            ->whereBetween('tanggal', [$start_date, $end_date]);

        if ($id_kelas) {
            $absen_query->where('id_kelas', $id_kelas);
        }

        $data_absen = $absen_query->pluck('tanggal')->toArray();

        $sudah_absen_array = array_map(function ($tgl) {
            return date('Y-m-d', strtotime($tgl));
        }, $data_absen);

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
                $sudah_absen = in_array($date, $sudah_absen_array);
                $list_tanggal[] = [
                    'tanggal' => $date,
                    'hari' => $hari_indo,
                    'tampilan' => date('d F Y', strtotime($date)) . " ($hari_indo)",
                    'status' => $sudah_absen ? 'sudah' : 'belum'
                ];
            }
        }

        return response()->json($list_tanggal);
    }

    public function daftarKelas(Request $request)
    {
        $user = Auth::user();

        if (!$user->guru) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak terdaftar sebagai Guru.');
        }

        $id_guru = $user->guru->id_guru;

        // QUERY BARU: Ambil kombinasi unik (Kelas + Mapel) dari Jadwal
        // Kita gunakan distinct() agar jika ada jadwal Senin & Kamis, tetap muncul 1 kartu saja.
        $query = Jadwal::where('id_guru', $id_guru)
            ->select('id_kelas', 'id_mapel') // Hanya ambil kolom grouping
            ->distinct() // Pastikan unik
            ->with([
                'mapel',
                'kelas' => function ($q) {
                    $q->withCount('siswa'); // Hitung jumlah siswa sekalian
                }
            ]);

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('kelas', function ($q) use ($search) {
                $q->where('nama_kelas', 'like', '%' . $search . '%');
            })->orWhereHas('mapel', function ($m) use ($search) {
                $m->where('nama_mapel', 'like', '%' . $search . '%');
            });
        }

        // Paginate hasil
        $daftar_kelas = $query->paginate(12)->withQueryString();

        return view('absensi.kelas.index', compact('daftar_kelas'));
    }

    public function show(Request $request, $id_kelas, $id_mapel)
    {
        if (!$this->cekOtorisasiGuru($id_kelas, $id_mapel)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk melihat data kelas ini.');
        }
        
        $guru = Auth::user()->guru;

        $kelas = Kelas::findOrFail($id_kelas);
        $mapel = Mapel::findOrFail($id_mapel);

        // 1. Ambil data Siswa sesuai filter search
        $query = Siswa::where('id_kelas', $id_kelas);
        if ($request->filled('search')) {
            $query->where('nama_siswa', 'like', '%' . $request->search . '%');
        }
        $siswa = $query->orderBy('nama_siswa', 'asc')->get();

        $semuaAbsen = KehadiranHarian::where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->where('id_guru', $guru->id_guru)
            ->select('id_siswa', 'tanggal', 'status', 'keterangan') // Ambil kolom yang butuh saja
            ->get();

        $absenGrouped = $semuaAbsen->groupBy('id_siswa');

        foreach ($siswa as $s) {
            $absenSiswa = $absenGrouped->get($s->id_siswa, collect());

            $s->total_hadir = $absenSiswa->where('status', 'H')->count();
            $s->total_sakit = $absenSiswa->where('status', 'S')->count();
            $s->total_izin  = $absenSiswa->where('status', 'I')->count();
            $s->total_alpha = $absenSiswa->where('status', 'A')->count();

            $s->list_keterangan = $absenSiswa->filter(function ($item) {
                return !is_null($item->keterangan) && trim($item->keterangan) !== '';
            })
                ->sortByDesc('tanggal')
                ->values(); // Reset index array

            $total_data = $s->total_hadir + $s->total_sakit + $s->total_izin + $s->total_alpha;

            $s->persentase = $total_data > 0
                ? round(($s->total_hadir / $total_data) * 100)
                : 0;
        }

        return view('absensi.show', compact('kelas', 'mapel', 'siswa'));
    }

    private function cekOtorisasiGuru($id_target, $id_mapel, $tipe = 'kelas')
    {
        $guru = Auth::user()->guru;
        if (!$guru) return false;

        $query = \App\Models\Jadwal::where('id_guru', $guru->id_guru)
            ->where('id_mapel', $id_mapel);

        if ($tipe === 'major') {
            $query->where('id_major', $id_target);
        } else {
            $query->where('id_kelas', $id_target);
        }

        return $query->exists();
    }
}