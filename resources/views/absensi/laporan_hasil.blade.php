<x-app-layout>
    <style>
        /* Mengubah tampilan scrollbar hanya untuk tabel ini */
        .custom-scrollbar::-webkit-scrollbar {
            height: 10px;
            /* Tinggi scrollbar horizontal */
            width: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c7c7c7;
            /* Warna scrollbar */
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0a0a0;
            /* Warna saat dihover */
        }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rekapitulasi Absensi Bulanan') }}
            </h2>
            <a href="{{ route('absensi.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm hover:bg-gray-700 transition-colors shadow-sm">
                &larr; Kembali ke Riwayat
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

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

                            <th colspan="6" class="px-2 py-2 text-center font-bold text-gray-900 border-r border-b bg-indigo-50/50">
                                Ringkasan
                            </th>
                        </tr>
                        <tr>
                            @foreach($tanggal_pertemuan as $tgl)
                            <th class="border-r bg-gray-50"></th>
                            @endforeach

                            <th class="px-3 py-1 text-center text-xs font-bold text-green-600 bg-indigo-50/50 border-r" title="Hadir">H</th>
                            <th class="px-3 py-1 text-center text-xs font-bold text-blue-600 bg-indigo-50/50 border-r" title="Sakit">S</th>
                            <th class="px-3 py-1 text-center text-xs font-bold text-yellow-600 bg-indigo-50/50 border-r" title="Izin">I</th>
                            <th class="px-3 py-1 text-center text-xs font-bold text-red-600 bg-indigo-50/50 border-r" title="Alpha">A</th>
                            <th class="px-3 py-1 text-center text-xs font-bold text-gray-700 bg-indigo-50/50 border-r" title="Total Input">T</th>
                            <th class="px-3 py-1 text-center text-xs font-bold text-indigo-700 bg-indigo-50/50 border-r" title="Persentase">%</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($siswa as $index => $s)
                        @php
                            // Perhitungan tetap dilakukan di awal
                            $h = 0; $sa = 0; $i = 0; $a = 0;
                            foreach($tanggal_pertemuan as $tgl) {
                                $status_cek = $rekap[$s->id_siswa][$tgl] ?? '-';
                                if($status_cek == 'H') $h++;
                                elseif($status_cek == 'S') $sa++;
                                elseif($status_cek == 'I') $i++;
                                elseif($status_cek == 'A') $a++;
                            }
                            $total_data = $h + $sa + $i + $a;
                            $total_pertemuan = count($tanggal_pertemuan);
                            $pembagi = $total_pertemuan > 0 ? $total_pertemuan : 1;
                            $persen = round(($h / $pembagi) * 100);

                            $colorPersen = 'text-green-600';
                            if($persen < 75) $colorPersen='text-red-600' ;
                            elseif($persen < 90) $colorPersen='text-yellow-600' ;
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
                                    '-' => 'text-gray-300',
                                    default => ''
                                };
                                @endphp
                                <td class="px-1 py-2 text-center border-r border-gray-100 {{ $bgClass }}">
                                    {{ $status }}
                                </td>
                            @endforeach

                            <td class="px-2 py-2 text-center font-bold text-green-600 bg-indigo-50/20 border-r">{{ $h }}</td>
                            <td class="px-2 py-2 text-center font-bold text-blue-600 bg-indigo-50/20 border-r">{{ $sa }}</td>
                            <td class="px-2 py-2 text-center font-bold text-yellow-600 bg-indigo-50/20 border-r">{{ $i }}</td>
                            <td class="px-2 py-2 text-center font-bold text-red-600 bg-indigo-50/20 border-r">{{ $a }}</td>
                            <td class="px-2 py-2 text-center font-bold text-gray-700 bg-indigo-50/20 border-r">{{ $total_data }}</td>
                            <td class="px-2 py-2 text-center font-bold bg-indigo-50/20 border-r {{ $colorPersen }}">{{ $persen }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-4 text-xs text-gray-500 border-t border-gray-200 flex flex-col sm:flex-row justify-between gap-4">
                <div>
                    <p class="font-bold mb-1">Keterangan:</p>
                    <div class="flex flex-wrap gap-3">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> H = Hadir</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> S = Sakit</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> I = Izin</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> A = Alpha</span>
                    </div>
                </div>
                <div class="text-right">
                    <p>Total Pertemuan (Jadwal): <strong>{{ count($tanggal_pertemuan) }} Hari</strong></p>
                    <p class="text-[10px] mt-1 text-gray-400">
                        * T = Total Input Data (H+S+I+A).<br>
                        * % = (Hadir / Total Pertemuan) x 100.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>