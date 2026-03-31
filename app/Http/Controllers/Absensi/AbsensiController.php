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
        $user = \Illuminate\Support\Facades\Auth::user();

        $id_guru = $user->guru ? $user->guru->id_guru : null;

        $query = \App\Models\KehadiranHarian::select(
            'tanggal',
            'id_kelas',
            'id_major',
            'id_mapel',
            'id_guru',
            \Illuminate\Support\Facades\DB::raw('count(*) as total_siswa'),
            \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN status = "H" THEN 1 ELSE 0 END) as hadir'),
            \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN status = "S" THEN 1 ELSE 0 END) as sakit'),
            \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN status = "I" THEN 1 ELSE 0 END) as izin'),
            \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN status = "A" THEN 1 ELSE 0 END) as alpha'),
            \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN status = "L" THEN 1 ELSE 0 END) as libur')
        )
            ->with(['kelas', 'mapel'])
            ->groupBy('tanggal', 'id_kelas', 'id_major', 'id_mapel', 'id_guru')
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

        // --- PERBAIKAN DROPDOWN ---
        $jadwal_guru = \App\Models\Jadwal::where('id_guru', $id_guru)->with(['mapel', 'kelas', 'major'])->get();

        $mapels_list = $jadwal_guru->pluck('mapel')->filter()->unique('id_mapel')->values();

        $kelas_list = collect();
        foreach ($jadwal_guru as $j) {
            if ($j->id_major && $j->major) {
                $kelas_list->push((object)[
                    'id_kelas' => 'M' . $j->id_major,
                    'nama_kelas' => $j->major->nama_major ?? 'Major'
                ]);
            } elseif ($j->id_kelas && $j->kelas) {
                $kelas_list->push((object)[
                    'id_kelas' => $j->id_kelas,
                    'nama_kelas' => $j->kelas->nama_kelas ?? 'Kelas'
                ]);
            }
        }
        $kelas_list = $kelas_list->unique('id_kelas')->values();
        // --------------------------

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


    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'id_mapel' => 'required',
            'status'   => 'required|array',
        ]);

        $tipe = $request->id_major ? 'major' : 'kelas';
        $id_target = $request->id_major ?: $request->id_kelas;

        if (!$this->cekOtorisasiGuru($id_target, $request->id_mapel, $tipe)) {
            abort(403, 'Akses Ditolak: Percobaan manipulasi data terdeteksi.');
        }

        $inputDate = \Carbon\Carbon::parse($request->tanggal)->startOfDay();
        $serverDate = \Carbon\Carbon::now()->startOfDay();

        if ($inputDate->gt($serverDate)) {
            return back()->with('error', 'Manipulasi tanggal terdeteksi. Gunakan waktu server yang valid.');
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user->guru) {
            return back()->with('error', 'Data guru tidak ditemukan untuk akun ini.');
        }
        $id_guru = $user->guru->id_guru;

        $tahun_ajar = \App\Models\TahunAjar::where('status', 'Aktif')->first();
        $id_tahun_ajar = $tahun_ajar ? $tahun_ajar->id_tahun_ajar : 1;

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $siswa_hadir_ids = [];
            $all_siswa_ids = array_keys($request->status);

            foreach ($request->status as $id_siswa => $status_kode) {
                \App\Models\KehadiranHarian::updateOrCreate(
                    [
                        'tanggal'  => $request->tanggal,
                        'id_siswa' => $id_siswa,
                        'id_mapel' => $request->id_mapel,
                    ],
                    [
                        'id_kelas'      => $request->id_kelas ?: null,
                        'id_major'      => $request->id_major ?: null,
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
                \Illuminate\Support\Facades\DB::table('sakit_siswa')
                    ->whereIn('id_siswa', $siswa_hadir_ids)
                    ->where('status_akhir', 'Masih Sakit')
                    ->update([
                        'status_akhir' => 'Kembali ke Kelas',
                        'updated_at' => now()
                    ]);
            }

            // Panggil fungsi rekap bulanan dengan parameter id_major tambahan
            $this->updateRekapBulananMassal($all_siswa_ids, $request->id_mapel, $request->id_kelas ?: null, $request->id_major ?: null, $id_tahun_ajar, $request->tanggal, $id_guru);

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('absensi.index')->with('success', 'Absensi berhasil disimpan dan disinkronkan!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollback();
            // Menampilkan letak error aslinya agar tidak redirect ke halaman Method Not Allowed
            return redirect()->route('absensi.index')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function edit($id_kelas, $id_mapel, $tanggal)
    {
        // Deteksi apakah ID yang dikirim memiliki awalan 'M' (berarti kelas Major)
        $is_major = strpos($id_kelas, 'M') === 0;
        $real_id = $is_major ? substr($id_kelas, 1) : $id_kelas;
        $tipe = $is_major ? 'major' : 'kelas';

        if (!$this->cekOtorisasiGuru($real_id, $id_mapel, $tipe)) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki jadwal untuk mengedit absensi kelas ini.');
        }

        $infoMapel = \App\Models\Mapel::find($id_mapel);

        // Pisahkan logika pencarian data antara Major dan Kelas Reguler
        if ($is_major) {
            $siswa = \App\Models\Siswa::where('id_major', $real_id)->orderBy('nama_siswa', 'asc')->get();
            $major = \App\Models\Major::find($real_id);
            $infoKelas = (object) [
                'id_kelas' => null,
                'id_major' => $real_id, // Penting agar form tau ini adalah major
                'nama_kelas' => $major->nama_major ?? 'Major'
            ];

            // Tarik kehadiran major (dimana id_kelas di DB adalah null)
            $dataKehadiran = \App\Models\KehadiranHarian::where('tanggal', $tanggal)
                ->whereNull('id_kelas')
                ->where('id_mapel', $id_mapel)
                ->get()
                ->keyBy('id_siswa');
        } else {
            $siswa = \App\Models\Siswa::where('id_kelas', $real_id)->orderBy('nama_siswa', 'asc')->get();
            $infoKelas = \App\Models\Kelas::find($real_id);

            // Tarik kehadiran kelas reguler
            $dataKehadiran = \App\Models\KehadiranHarian::where('tanggal', $tanggal)
                ->where('id_kelas', $real_id)
                ->where('id_mapel', $id_mapel)
                ->get()
                ->keyBy('id_siswa');
        }

        $tanggal_absen = \Carbon\Carbon::parse($tanggal)->startOfDay();

        $siswa_masih_sakit_db = \Illuminate\Support\Facades\DB::table('sakit_siswa')
            ->where('status_akhir', 'Masih Sakit')
            ->where('tanggal', '<=', $tanggal)
            ->get();

        $siswa_masih_sakit = collect();
        foreach ($siswa_masih_sakit_db as $sakit) {
            $tgl_mulai = \Carbon\Carbon::parse($sakit->tanggal)->startOfDay();
            $hari_sakit = 0;
            for ($date = $tgl_mulai->copy(); $date->lte($tanggal_absen); $date->addDay()) {
                if (!$date->isSunday()) $hari_sakit++;
            }
            $sakit->durasi_hari = $hari_sakit;
            $siswa_masih_sakit->put($sakit->id_siswa, $sakit);
        }

        $siswa_baru_sembuh_db = \Illuminate\Support\Facades\DB::table('sakit_siswa')
            ->where('status_akhir', 'Kembali ke Kelas')
            ->whereDate('updated_at', \Carbon\Carbon::parse($tanggal)->toDateString())
            ->get();

        $siswa_baru_sembuh = collect();
        foreach ($siswa_baru_sembuh_db as $sembuh) {
            $konfirmasi = \Illuminate\Support\Facades\DB::table('kehadiran_harian')
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

        return view('absensi.form', compact('siswa', 'tanggal', 'infoKelas', 'infoMapel', 'siswa_masih_sakit', 'siswa_baru_sembuh', 'dataKehadiran'));
    }

    // Perhatikan ada tambahan parameter $id_major di sini
    private function updateRekapBulananMassal($siswa_ids, $id_mapel, $id_kelas, $id_major, $id_tahun_ajar, $tanggal, $id_guru)
    {
        $bulan = date('m', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));
        $periode = date('m-Y', strtotime($tanggal));

        $stats = \App\Models\KehadiranHarian::whereIn('id_siswa', $siswa_ids)
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

            \Illuminate\Support\Facades\DB::table('kehadiran_bulanan')->updateOrInsert(
                [
                    'id_siswa' => $id_siswa,
                    'id_mapel' => $id_mapel,
                    'periode'  => $periode
                ],
                [
                    'id_kelas'         => $id_kelas,
                    'id_major'         => $id_major, // <--- DISIMPAN DI SINI
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
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user->guru) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $id_guru_aktif = $user->guru->id_guru;
        $jadwal_guru = \App\Models\Jadwal::where('id_guru', $id_guru_aktif)->with(['mapel', 'kelas', 'major'])->get();

        $mapels = $jadwal_guru->pluck('mapel')->filter()->unique('id_mapel')->values();

        $kelas = collect();
        foreach ($jadwal_guru as $j) {
            if ($j->id_major && $j->major) {
                $kelas->push((object)[
                    'id_kelas' => 'M' . $j->id_major,
                    'nama_kelas' => $j->major->nama_major ?? 'Major'
                ]);
            } elseif ($j->id_kelas && $j->kelas) {
                $kelas->push((object)[
                    'id_kelas' => $j->id_kelas,
                    'nama_kelas' => $j->kelas->nama_kelas ?? 'Kelas'
                ]);
            }
        }
        $kelas = $kelas->unique('id_kelas')->values();

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
        $id_mapel = $request->id_mapel;
        $id_target = $request->id_kelas;

        $is_major = strpos($id_target, 'M') === 0;
        $real_id = $is_major ? substr($id_target, 1) : $id_target;

        if ($is_major) {
            $siswa = \App\Models\Siswa::where('id_major', $real_id)->orderBy('nama_siswa', 'asc')->get();
            $major = \App\Models\Major::find($real_id);
            $infoKelas = (object) ['id_kelas' => null, 'nama_kelas' => $major->nama_major ?? 'Major'];

            $hari_mengajar = \App\Models\Jadwal::where('id_major', $real_id)
                ->where('id_mapel', $id_mapel)
                ->pluck('hari')
                ->toArray();

            $data_absensi = \App\Models\KehadiranHarian::whereNull('id_kelas')
                ->where('id_mapel', $id_mapel)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();
        } else {
            $siswa = \App\Models\Siswa::where('id_kelas', $real_id)->orderBy('nama_siswa', 'asc')->get();
            $infoKelas = \App\Models\Kelas::find($real_id);

            $hari_mengajar = \App\Models\Jadwal::where('id_kelas', $real_id)
                ->where('id_mapel', $id_mapel)
                ->pluck('hari')
                ->toArray();

            $data_absensi = \App\Models\KehadiranHarian::where('id_kelas', $real_id)
                ->where('id_mapel', $id_mapel)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->get();
        }

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

        $rekap = [];
        foreach ($data_absensi as $d) {
            $rekap[$d->id_siswa][$d->tanggal] = $d->status;
        }

        // TAMBAHAN: Menarik data mata pelajaran dari database agar $infoMapel dikenali di Blade
        $infoMapel = \App\Models\Mapel::find($id_mapel);

        return view('absensi.laporan_hasil', compact(
            'siswa',
            'infoKelas',
            'infoMapel', // Sekarang $infoMapel sudah ada isinya
            'bulan',
            'tahun',
            'tanggal_pertemuan',
            'rekap'
        ));
    }
    
    public function cekLembar(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kombinasi_jadwal' => 'required',
        ]);

        $inputDate = \Carbon\Carbon::parse($request->tanggal)->startOfDay();
        $serverDate = \Carbon\Carbon::now()->startOfDay();

        if ($inputDate->gt($serverDate)) {
            return back()->with('error', 'Anda tidak dapat melakukan absensi untuk tanggal di masa depan (Waktu Server).');
        }

        // Pecah string value menjadi 3 bagian (Tipe - ID Target - ID Mapel)
        $ids = explode('-', $request->kombinasi_jadwal);

        if (count($ids) !== 3) {
            return back()->with('error', 'Format jadwal tidak valid.');
        }

        // Ambil data menggunakan indeks array yang benar [0], [1], [2]
        $tipe = trim(strtolower((string) $ids[0]));
        $id_target = trim((string) $ids[1]);
        $id_mapel = trim((string) $ids[2]);
        $tanggal = $request->tanggal;

        if (!$this->cekOtorisasiGuru($id_target, $id_mapel, $tipe)) {
            abort(403, "Akses Ditolak: Anda tidak mengajar di {$tipe} tersebut. (ID Target: {$id_target}, ID Mapel: {$id_mapel})");
        }

        // Cek Duplikasi
        $query_duplikasi = \App\Models\KehadiranHarian::where('tanggal', $tanggal)
            ->where('id_mapel', $id_mapel);

        if ($tipe === 'major') {
            $query_duplikasi->whereNull('id_kelas');
        } else {
            $query_duplikasi->where('id_kelas', $id_target);
        }

        if ($query_duplikasi->exists()) {
            return redirect()->route('absensi.index')->with('warning', 'Absensi untuk tanggal ini sudah ada! Silakan edit di menu riwayat.');
        }

        // Ambil data Siswa sesuai dengan Tipe (Kelas / Major)
        if ($tipe === 'major') {
            $siswa = \App\Models\Siswa::where('id_major', $id_target)->orderBy('nama_siswa', 'asc')->get();
            $major = \App\Models\Major::find($id_target);

            // PERBAIKAN: Menambahkan id_major ke dalam object agar bisa dibaca oleh form.blade.php
            $infoKelas = (object) [
                'id_kelas' => null,
                'id_major' => $id_target,
                'nama_kelas' => $major->nama_major ?? 'Major'
            ];
        } else {
            $siswa = \App\Models\Siswa::where('id_kelas', $id_target)->orderBy('nama_siswa', 'asc')->get();
            $infoKelas = \App\Models\Kelas::find($id_target);
        }

        $infoMapel = \App\Models\Mapel::find($id_mapel);
        $tanggal_absen = \Carbon\Carbon::parse($tanggal)->startOfDay();

        // 1. Siswa yang MASIH SAKIT (Belum ada yang meng-absen H)
        $siswa_masih_sakit_db = \Illuminate\Support\Facades\DB::table('sakit_siswa')
            ->where('status_akhir', 'Masih Sakit')
            ->where('tanggal', '<=', $tanggal)
            ->get();

        $siswa_masih_sakit = collect();
        foreach ($siswa_masih_sakit_db as $sakit) {
            $tgl_mulai = \Carbon\Carbon::parse($sakit->tanggal)->startOfDay();
            $hari_sakit = 0;
            for ($date = $tgl_mulai->copy(); $date->lte($tanggal_absen); $date->addDay()) {
                if (!$date->isSunday()) $hari_sakit++;
            }
            $sakit->durasi_hari = $hari_sakit;
            $siswa_masih_sakit->put($sakit->id_siswa, $sakit);
        }

        // 2. Siswa yang BARU SEMBUH HARI INI
        $siswa_baru_sembuh_db = \Illuminate\Support\Facades\DB::table('sakit_siswa')
            ->where('status_akhir', 'Kembali ke Kelas')
            ->whereDate('updated_at', \Carbon\Carbon::parse($tanggal)->toDateString())
            ->get();

        $siswa_baru_sembuh = collect();
        foreach ($siswa_baru_sembuh_db as $sembuh) {
            $konfirmasi = \Illuminate\Support\Facades\DB::table('kehadiran_harian')
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
            ->whereNotNull('id_kelas')
            ->whereNotNull('id_mapel')
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
            $query->where(function ($q) use ($search) {
                $q->whereHas('kelas', function ($k) use ($search) {
                    $k->where('nama_kelas', 'like', '%' . $search . '%');
                })->orWhereHas('mapel', function ($m) use ($search) {
                    $m->where('nama_mapel', 'like', '%' . $search . '%');
                });
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
        $guru = \Illuminate\Support\Facades\Auth::user()->guru;

        if (!$guru) return false;

        // Paksa semua parameter menjadi string murni agar aman
        $tipe = trim(strtolower((string) $tipe));
        $id_target = trim((string) $id_target);
        $id_mapel = trim((string) $id_mapel);

        $query = \App\Models\Jadwal::where('id_guru', $guru->id_guru)
            ->where('id_mapel', $id_mapel);

        // Bedakan pengecekan berdasarkan tipe (Major atau Kelas)
        if ($tipe === 'major') {
            $query->where('id_major', $id_target);
        } else {
            $query->where('id_kelas', $id_target);
        }

        return $query->exists();
    }
}
