<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KehadiranHarian;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    // 1. MENAMPILKAN HALAMAN UTAMA (REKAP PER TANGGAL)
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

    // 2. MENAMPILKAN FORM FILTER (PILIH MAPEL & KELAS)
    public function create()
    {
        // Ambil data untuk Dropdown
        $mapels = Mapel::all();
        $kelas = Kelas::all();

        return view('absensi.create', compact('mapels', 'kelas'));
    }

    // 3. LOGIKA UNTUK MENAMPILKAN LEMBAR ABSENSI (LIST SISWA)
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

        // Ambil info detail mapel & kelas buat judul
        $infoKelas = Kelas::find($id_kelas);
        $infoMapel = Mapel::find($id_mapel);

        // Kirim data ke view Form Input
        return view('absensi.form', compact('siswa', 'tanggal', 'infoKelas', 'infoMapel'));
    }

    // ... (kode atas tetap sama)

    // 4. SIMPAN DATA ABSENSI (LOGIKA UTAMA)
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tanggal' => 'required|date',
            'id_kelas' => 'required',
            'id_mapel' => 'required',
            'status'   => 'required|array', // Array status per siswa
        ]);

        // 2. Setup Data Default
        // Karena kita "Developer Mode" (Bypass Login), kita hardcode ID Guru = 1 (Pak Budi)
        // Nanti kalau sudah fix login, ganti jadi: Auth::user()->guru->id_guru
        $id_guru = 1;

        // Cari Tahun Ajar yang Aktif
        $tahun_ajar = \App\Models\TahunAjar::where('status', 'Aktif')->first();
        $id_tahun_ajar = $tahun_ajar ? $tahun_ajar->id_tahun_ajar : 1; // Fallback ke 1 kalau tidak ketemu

        // Gunakan Database Transaction biar aman (kalau gagal satu, batal semua)
        DB::beginTransaction();

        try {
            // 3. Loop setiap siswa yang diabsen
            foreach ($request->status as $id_siswa => $status_kode) {

                // A. SIMPAN KE TABEL HARIAN (TABEL BARU)
                // Kita pakai updateOrCreate supaya kalau diedit tidak dobel
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

                // B. SINKRONISASI KE TABEL LAMA (kehadiran_bulanan)
                // Ini supaya Erapor Native tetap bisa baca rekapnya
                $this->updateRekapBulanan($id_siswa, $request->id_mapel, $request->id_kelas, $id_tahun_ajar, $request->tanggal, $id_guru);
            }

            DB::commit(); // Simpan permanen jika sukses

            return redirect()->route('absensi.index')->with('success', 'Absensi berhasil disimpan dan disinkronkan ke Erapor!');
        } catch (\Exception $e) {
            DB::rollback(); // Batalkan semua jika ada error
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * FUNGSI TAMBAHAN: updateRekapBulanan
     * Tugasnya menghitung ulang jumlah H/S/I/A di bulan tersebut lalu update tabel lama.
     */
    private function updateRekapBulanan($id_siswa, $id_mapel, $id_kelas, $id_tahun_ajar, $tanggal, $id_guru)
    {
        // 1. Tentukan Periode (Format MM-YYYY sesuai tabel native)
        $periode = date('m-Y', strtotime($tanggal));

        // 2. Hitung jumlah H/S/I/A dari tabel harian untuk bulan ini
        // NOTE: Status 'L' (Libur) TIDAK DIHITUNG di sini agar tidak merusak persentase kehadiran siswa
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

        // 3. Update tabel lama (kehadiran_bulanan)
        // Kita pakai model KehadiranBulanan (Pastikan model ini sudah dibuat)
        // Jika belum ada modelnya, kita pakai Query Builder biasa biar aman
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
                'is_lock'          => 0 // Default tidak dikunci
            ]
        );
    }
}
