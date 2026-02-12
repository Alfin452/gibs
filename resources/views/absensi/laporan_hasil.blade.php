<x-app-layout>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 10px;
            width: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c7c7c7;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0a0a0;
        }
    </style>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Rekapitulasi Absensi Bulanan') }}
            </h2>

            <a href="{{ route('absensi.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Riwayat
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-1">

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-800">{{ $infoKelas->nama_kelas }}</h3>
                <p class="text-gray-500 font-medium">{{ $infoMapel->nama_mapel }}</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-400 uppercase tracking-wider font-semibold">Periode</div>
                <div class="text-xl font-bold text-indigo-600">
                    {{ \Carbon\Carbon::createFromDate($tahun, (int)$bulan, 1)->translatedFormat('F') }} {{ $tahun }}
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
            <div class="overflow-x-auto custom-scrollbar pb-2 relative w-full">
                <table class="w-full divide-y divide-gray-200 text-sm whitespace-nowrap">
                    <thead class="bg-gray-50">
                        <tr>
                            <th rowspan="2" class="px-2 py-3 text-center font-bold text-gray-500 uppercase tracking-wider border-r w-14 min-w-[3.5rem] sticky left-0 z-30 bg-gray-50">
                                No
                            </th>
                            <th rowspan="2" class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider border-r min-w-[250px] sticky left-14 z-30 bg-gray-50 shadow-md">
                                Nama Siswa
                            </th>

                            @foreach($tanggal_pertemuan as $tgl)
                            <th class="px-2 py-2 text-center font-bold text-gray-700 border-r min-w-[45px]">
                                {{ date('d', strtotime($tgl)) }}
                                <div class="text-[9px] text-gray-400 font-normal uppercase mt-0.5">
                                    {{ \Carbon\Carbon::parse($tgl)->translatedFormat('D') }}
                                </div>
                            </th>
                            @endforeach

                            <th colspan="7" class="px-2 py-2 text-center font-bold text-gray-900 border-b-2 border-indigo-200 bg-indigo-50 border-l-4 border-l-indigo-200">
                                Ringkasan Akumulasi
                            </th>
                        </tr>
                        <tr>
                            @foreach($tanggal_pertemuan as $tgl)
                            <th class="border-r bg-gray-50 h-8"></th>
                            @endforeach

                            <th class="px-3 py-2 text-center text-xs font-bold text-green-700 bg-indigo-50 border-r border-indigo-100 border-l-4 border-l-indigo-200" title="Hadir">H</th>
                            <th class="px-3 py-2 text-center text-xs font-bold text-blue-700 bg-indigo-50 border-r border-indigo-100" title="Sakit">S</th>
                            <th class="px-3 py-2 text-center text-xs font-bold text-yellow-700 bg-indigo-50 border-r border-indigo-100" title="Izin">I</th>
                            <th class="px-3 py-2 text-center text-xs font-bold text-red-700 bg-indigo-50 border-r border-indigo-100" title="Alpha">A</th>
                            <th class="px-3 py-2 text-center text-xs font-bold text-gray-600 bg-indigo-50 border-r border-indigo-100" title="Libur">L</th>
                            <th class="px-3 py-2 text-center text-xs font-bold text-gray-900 bg-indigo-100/50 border-r border-indigo-100" title="Total Data">Total</th>
                            <th class="px-3 py-2 text-center text-xs font-bold text-indigo-700 bg-indigo-100/50" title="Persentase Kehadiran">Persentase %</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($siswa as $index => $s)
                        @php
                        $h = 0; $sa = 0; $i = 0; $a = 0; $l = 0;
                        $total_pertemuan_jadwal = count($tanggal_pertemuan);

                        // Hitung Statistik
                        foreach($tanggal_pertemuan as $tgl) {
                        $status = $rekap[$s->id_siswa][$tgl] ?? '-';
                        if($status == 'H') $h++;
                        elseif($status == 'S') $sa++;
                        elseif($status == 'I') $i++;
                        elseif($status == 'A') $a++;
                        elseif($status == 'L') $l++;
                        }

                        // Total Data Masuk (H+S+I+A+L)
                        $total_input = $h + $sa + $i + $a + $l;

                        // Rumus Persentase: (Hadir / (Total Jadwal - Libur)) * 100
                        // Libur tidak dianggap sebagai hari efektif belajar
                        $hari_efektif = $total_pertemuan_jadwal - $l;
                        $pembagi = $hari_efektif > 0 ? $hari_efektif : 1;
                        $persen = round(($h / $pembagi) * 100);

                        // Warna Persentase
                        $colorPersen = $persen < 75 ? 'text-red-600 font-extrabold' : ($persen < 90 ? 'text-yellow-600 font-bold' : 'text-green-600 font-bold' );
                            @endphp

                            <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-2 py-3 text-center text-gray-500 border-r sticky left-0 z-20 bg-white group-hover:bg-gray-50">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 border-r sticky left-14 z-20 bg-white shadow-md group-hover:bg-gray-50">
                                {{ $s->nama_siswa }}
                            </td>

                            @foreach($tanggal_pertemuan as $tgl)
                            @php
                            $status = $rekap[$s->id_siswa][$tgl] ?? '-';
                            $bgClass = match($status) {
                            'H' => 'bg-green-50 text-green-700 font-bold',
                            'S' => 'bg-blue-50 text-blue-700 font-bold',
                            'I' => 'bg-yellow-50 text-yellow-700 font-bold',
                            'A' => 'bg-red-50 text-red-700 font-bold',
                            'L' => 'bg-gray-200 text-gray-500 font-bold', // Warna Libur
                            '-' => 'text-gray-300',
                            default => ''
                            };
                            @endphp
                            <td class="px-1 py-2 text-center border-r border-gray-100 {{ $bgClass }}">
                                {{ $status }}
                            </td>
                            @endforeach

                            <td class="px-2 py-2 text-center font-bold text-green-600 bg-indigo-50/30 border-r border-indigo-100 border-l-4 border-l-indigo-200">{{ $h }}</td>
                            <td class="px-2 py-2 text-center font-bold text-blue-600 bg-indigo-50/30 border-r border-indigo-100">{{ $sa }}</td>
                            <td class="px-2 py-2 text-center font-bold text-yellow-600 bg-indigo-50/30 border-r border-indigo-100">{{ $i }}</td>
                            <td class="px-2 py-2 text-center font-bold text-red-600 bg-indigo-50/30 border-r border-indigo-100">{{ $a }}</td>
                            <td class="px-2 py-2 text-center font-bold text-gray-500 bg-indigo-50/30 border-r border-indigo-100">{{ $l }}</td>

                            <td class="px-2 py-2 text-center font-bold text-gray-800 bg-indigo-100/30 border-r border-indigo-100">{{ $total_input }}</td>
                            <td class="px-2 py-2 text-center bg-indigo-100/30 {{ $colorPersen }}">{{ $persen }}</td>
                            </tr>
                            @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-4 text-xs text-gray-500 border-t border-gray-200 flex flex-col sm:flex-row justify-between gap-4">
                <div>
                    <p class="font-bold mb-1 text-gray-700">Keterangan Kode:</p>
                    <div class="flex flex-wrap gap-4">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> H = Hadir</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> S = Sakit</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> I = Izin</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> A = Alpha</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gray-500"></span> L = Libur</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="mb-1">Total Pertemuan Efektif: <strong>{{ count($tanggal_pertemuan) }} Hari</strong> (Termasuk Libur)</p>
                    <div class="text-[10px] text-gray-400 space-y-0.5">
                        <p>* Total = Penjumlahan semua data (H+S+I+A+L).</p>
                        <p>* Persentase (%) = (Hadir / (Total Jadwal - Libur)) x 100.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>