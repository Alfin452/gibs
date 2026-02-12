<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Lembar Absensi Siswa') }}
            </h2>
            <div class="flex items-center gap-3 text-sm text-right">
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 text-left">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Kelas</p>
                    <p class="font-bold text-indigo-600">{{ $infoKelas->nama_kelas }}</p>
                </div>
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 text-left">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Tanggal</p>
                    <p class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-1 w-full px-4 sm:px-6 lg:px-1">

        <div class="bg-indigo-600 rounded-t-xl p-6 flex flex-col md:flex-row justify-between items-center gap-4 shadow-lg">
            <div class="text-white">
                <p class="text-xs font-bold text-indigo-200 uppercase tracking-wider mb-1">Mata Pelajaran</p>
                <h3 class="text-2xl font-bold">{{ $infoMapel->nama_mapel }}</h3>
            </div>

            <div class="flex gap-2 bg-indigo-700/50 p-1.5 rounded-lg backdrop-blur-sm">
                <button type="button" onclick="setSemua('H')" class="bg-white text-green-700 px-4 py-2 rounded-md text-xs font-bold hover:bg-green-50 shadow-sm transition-all flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Set Semua Hadir
                </button>
                <button type="button" onclick="setSemua('L')" class="text-white px-4 py-2 rounded-md text-xs font-bold hover:bg-indigo-600 transition-all border border-transparent hover:border-indigo-400">
                    Set Hari Libur
                </button>
            </div>
        </div>

        <form action="{{ route('absensi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <input type="hidden" name="id_kelas" value="{{ $infoKelas->id_kelas }}">
            <input type="hidden" name="id_mapel" value="{{ $infoMapel->id_mapel }}">

            <div class="bg-white shadow-xl rounded-b-xl border border-t-0 border-gray-200">
                
                <div class="w-full overflow-hidden rounded-b-xl">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-16 bg-gray-50">
                                    No
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50">
                                    Nama Siswa
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-80 bg-gray-50">
                                    Status Kehadiran
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50">
                                    Keterangan (Opsional)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($siswa as $index => $s)

                            @php
                            $statusDB = isset($dataKehadiran) ? ($dataKehadiran[$s->id_siswa]->status ?? 'H') : 'H';
                            $ketDB = isset($dataKehadiran) ? ($dataKehadiran[$s->id_siswa]->keterangan ?? '') : '';
                            @endphp

                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 font-medium align-middle">
                                    {{ $index + 1 }}
                                </td>
                                
                                <td class="px-6 py-4 align-middle">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">
                                            {{ $s->nama_siswa }}
                                        </span>
                                        <span class="text-xs text-gray-400 font-mono mt-0.5">
                                            NIS: {{ $s->nis }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 align-middle">
                                    <div class="flex justify-center items-center gap-1 bg-gray-100 p-1.5 rounded-lg border border-gray-200 shadow-inner">

                                        @foreach(['H' => ['green', 'Hadir'], 'S' => ['blue', 'Sakit'], 'I' => ['yellow', 'Izin'], 'A' => ['red', 'Alpha'], 'L' => ['gray', 'Libur']] as $code => $style)
                                        <label class="cursor-pointer relative group/radio">
                                            <input type="radio" name="status[{{ $s->id_siswa }}]" value="{{ $code }}" 
                                                class="peer sr-only" {{ $statusDB == $code ? 'checked' : '' }}>
                                            
                                            <div class="w-10 h-8 flex items-center justify-center rounded text-xs font-bold text-gray-500 
                                                hover:bg-white hover:text-gray-700
                                                peer-checked:bg-{{ $style[0] }}-500 peer-checked:text-white peer-checked:shadow-md 
                                                transition-all duration-200">
                                                {{ $code }}
                                            </div>

                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-[10px] rounded opacity-0 group-hover/radio:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-20">
                                                {{ $style[1] }}
                                            </div>
                                        </label>
                                        @endforeach

                                    </div>
                                </td>

                                <td class="px-6 py-4 align-middle">
                                    <input type="text" name="keterangan[{{ $s->id_siswa }}]" value="{{ $ketDB }}" placeholder="Tulis catatan..."
                                        class="w-full text-sm border-gray-200 bg-gray-50 focus:bg-white rounded-md focus:border-indigo-500 focus:ring-indigo-500 transition-all placeholder-gray-400">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-between items-center rounded-b-xl">
                    <p class="text-sm text-gray-500 italic hidden sm:block">
                        Pastikan semua data siswa telah dicek sebelum menyimpan.
                    </p>
                    <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg shadow-indigo-600/20 transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Simpan Absensi
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function setSemua(status) {
            const radios = document.querySelectorAll(`input[value="${status}"]`);
            radios.forEach(radio => {
                radio.checked = true;
            });
        }
    </script>
</x-app-layout>