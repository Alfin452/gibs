<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rekapitulasi Absensi Bulanan') }}
            </h2>
            <a href="{{ route('absensi.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm hover:bg-gray-700">
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
                    {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th rowspan="2" class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider border-r w-10">No</th>
                            <th rowspan="2" class="px-4 py-3 text-left font-bold text-gray-500 uppercase tracking-wider border-r min-w-[200px] sticky left-0 bg-gray-50 z-10">Nama Siswa</th>

                            @foreach($tanggal_pertemuan as $tgl)
                            <th class="px-2 py-2 text-center font-bold text-gray-700 border-r min-w-[35px]">
                                {{ date('d', strtotime($tgl)) }}
                                <div class="text-[9px] text-gray-400 font-normal uppercase">{{ date('D', strtotime($tgl)) }}</div>
                            </th>
                            @endforeach

                            <th colspan="4" class="px-2 py-2 text-center font-bold text-gray-900 border-b bg-gray-100">Akumulasi</th>
                            <th rowspan="2" class="px-3 py-3 text-center font-bold text-gray-900 bg-gray-100 border-l">%</th>
                        </tr>
                        <tr>
                            <th class="px-2 py-1 text-center text-xs font-bold text-green-600 bg-gray-100 border-r" title="Hadir">H</th>
                            <th class="px-2 py-1 text-center text-xs font-bold text-blue-600 bg-gray-100 border-r" title="Sakit">S</th>
                            <th class="px-2 py-1 text-center text-xs font-bold text-yellow-600 bg-gray-100 border-r" title="Izin">I</th>
                            <th class="px-2 py-1 text-center text-xs font-bold text-red-600 bg-gray-100" title="Alpha">A</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($siswa as $index => $s)
                        @php
                        $h = 0; $sa = 0; $i = 0; $a = 0;
                        $total_pertemuan = count($tanggal_pertemuan);
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-center text-gray-500 border-r">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900 border-r sticky left-0 bg-white z-10 shadow-sm">{{ $s->nama_siswa }}</td>

                            @foreach($tanggal_pertemuan as $tgl)
                            @php
                            $status = $rekap[$s->id_siswa][$tgl] ?? '-';

                            if($status == 'H') $h++;
                            elseif($status == 'S') $sa++;
                            elseif($status == 'I') $i++;
                            elseif($status == 'A') $a++;

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

                            <td class="px-2 py-2 text-center font-bold text-green-600 bg-gray-50 border-r">{{ $h }}</td>
                            <td class="px-2 py-2 text-center font-bold text-blue-600 bg-gray-50 border-r">{{ $sa }}</td>
                            <td class="px-2 py-2 text-center font-bold text-yellow-600 bg-gray-50 border-r">{{ $i }}</td>
                            <td class="px-2 py-2 text-center font-bold text-red-600 bg-gray-50">{{ $a }}</td>

                            @php
                            $persen = $total_pertemuan > 0 ? round(($h / $total_pertemuan) * 100) : 0;
                            $colorPersen = $persen < 75 ? 'text-red-600' : 'text-indigo-600' ;
                                @endphp
                                <td class="px-2 py-2 text-center font-bold bg-gray-100 border-l {{ $colorPersen }}">{{ $persen }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-gray-50 px-6 py-4 text-xs text-gray-500 border-t border-gray-200">
                <p><strong>Keterangan:</strong> H=Hadir, S=Sakit, I=Izin, A=Alpha, (-)=Belum diabsen.</p>
                <p>Kolom tanggal otomatis muncul berdasarkan jadwal mengajar Anda di sistem.</p>
            </div>
        </div>
    </div>
</x-app-layout>