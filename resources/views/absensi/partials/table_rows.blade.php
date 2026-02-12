@forelse($riwayat as $data)
<tr class="hover:bg-gray-50/80 transition-colors duration-200 border-b border-gray-100 last:border-0 group">

    <td class="px-6 py-5 whitespace-nowrap">
        <div class="flex flex-col">
            <span class="text-sm font-bold text-gray-900">
                {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d M Y') }}
            </span>
            <span class="text-xs text-gray-500 font-medium mt-0.5">
                {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('l') }}
            </span>
        </div>
    </td>

    <td class="px-6 py-5 whitespace-nowrap">
        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
            {{ $data->kelas->nama_kelas ?? '-' }}
        </span>
    </td>

    <td class="px-6 py-5 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-700">
            {{ $data->mapel->nama_mapel ?? '-' }}
        </div>
    </td>

    <td class="px-2 py-5 whitespace-nowrap text-center">
        <span class="text-sm {{ $data->hadir > 0 ? 'font-bold text-emerald-600' : 'text-gray-300' }}">
            {{ $data->hadir }}
        </span>
    </td>
    <td class="px-2 py-5 whitespace-nowrap text-center">
        <span class="text-sm {{ $data->sakit > 0 ? 'font-bold text-blue-600' : 'text-gray-300' }}">
            {{ $data->sakit }}
        </span>
    </td>
    <td class="px-2 py-5 whitespace-nowrap text-center">
        <span class="text-sm {{ $data->izin > 0 ? 'font-bold text-yellow-600' : 'text-gray-300' }}">
            {{ $data->izin }}
        </span>
    </td>
    <td class="px-2 py-5 whitespace-nowrap text-center">
        <span class="text-sm {{ $data->alpha > 0 ? 'font-bold text-red-600' : 'text-gray-300' }}">
            {{ $data->alpha }}
        </span>
    </td>

    <td class="px-6 py-5 whitespace-nowrap text-center">
        <div class="flex flex-col lg:flex-row items-center justify-center gap-3 opacity-90 group-hover:opacity-100 transition-opacity">

            <a href="{{ route('absensi.edit', ['id_kelas' => $data->id_kelas, 'id_mapel' => $data->id_mapel, 'tanggal' => $data->tanggal]) }}"
                class="px-4 py-1.5 rounded-lg text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 hover:border-indigo-300 transition-all shadow-sm w-full lg:w-auto">
                Edit
            </a>

            @php
            $bln = date('m', strtotime($data->tanggal));
            $thn = date('Y', strtotime($data->tanggal));
            @endphp
            <a href="{{ route('absensi.laporan.view', ['bulan' => $bln, 'tahun' => $thn, 'id_kelas' => $data->id_kelas, 'id_mapel' => $data->id_mapel]) }}"
                class="px-4 py-1.5 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300 transition-all shadow-sm w-full lg:w-auto">
                Rekap
            </a>

        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="px-6 py-12 text-center">
        <div class="flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <h3 class="text-gray-900 font-medium text-base">Belum ada data</h3>
            <p class="text-gray-500 text-sm mt-1">Silakan input absensi baru atau ubah filter pencarian.</p>
        </div>
    </td>
</tr>
@endforelse