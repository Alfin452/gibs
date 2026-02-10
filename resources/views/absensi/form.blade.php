<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Lembar Absensi Siswa') }}
            </h2>
            <div class="text-sm text-right">
                <p class="font-bold text-indigo-600">{{ $infoKelas->nama_kelas }}</p>
                <p class="text-gray-500">{{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto">

        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-6 rounded-r-lg flex justify-between items-center">
            <div>
                <p class="text-sm text-indigo-700 font-bold uppercase tracking-wider">Mata Pelajaran</p>
                <h3 class="text-xl font-bold text-gray-900">{{ $infoMapel->nama_mapel }}</h3>
            </div>

            <div class="flex gap-2">
                <button type="button" onclick="setSemua('H')" class="bg-white text-green-600 border border-green-200 px-3 py-1 rounded text-xs font-bold hover:bg-green-50">
                    Set Semua Hadir
                </button>
                <button type="button" onclick="setSemua('L')" class="bg-white text-gray-500 border border-gray-200 px-3 py-1 rounded text-xs font-bold hover:bg-gray-50">
                    Set Hari Libur
                </button>
            </div>
        </div>

        <form action="{{ route('absensi.store') }}" method="POST">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <input type="hidden" name="id_kelas" value="{{ $infoKelas->id_kelas }}">
            <input type="hidden" name="id_mapel" value="{{ $infoMapel->id_mapel }}">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50">
                                <th class="px-4 py-3 w-10">No</th>
                                <th class="px-4 py-3">Nama Siswa</th>
                                <th class="px-4 py-3 text-center w-64">Status Kehadiran</th>
                                <th class="px-4 py-3">Keterangan (Opsional)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($siswa as $index => $s)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900">{{ $s->nama_siswa }}</p>
                                    <p class="text-xs text-gray-400">{{ $s->nis }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-1 bg-gray-100 p-1 rounded-lg">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="status[{{ $s->id_siswa }}]" value="H" class="peer sr-only radio-status" checked>
                                            <div class="px-3 py-1 rounded text-xs font-bold text-gray-500 peer-checked:bg-green-500 peer-checked:text-white transition-all">H</div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="status[{{ $s->id_siswa }}]" value="S" class="peer sr-only radio-status">
                                            <div class="px-3 py-1 rounded text-xs font-bold text-gray-500 peer-checked:bg-blue-500 peer-checked:text-white transition-all">S</div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="status[{{ $s->id_siswa }}]" value="I" class="peer sr-only radio-status">
                                            <div class="px-3 py-1 rounded text-xs font-bold text-gray-500 peer-checked:bg-yellow-500 peer-checked:text-white transition-all">I</div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="status[{{ $s->id_siswa }}]" value="A" class="peer sr-only radio-status">
                                            <div class="px-3 py-1 rounded text-xs font-bold text-gray-500 peer-checked:bg-red-500 peer-checked:text-white transition-all">A</div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="radio" name="status[{{ $s->id_siswa }}]" value="L" class="peer sr-only radio-status">
                                            <div class="px-3 py-1 rounded text-xs font-bold text-gray-500 peer-checked:bg-gray-500 peer-checked:text-white transition-all">L</div>
                                        </label>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="keterangan[{{ $s->id_siswa }}]" placeholder="Catatan..."
                                        class="w-full text-sm border-gray-200 rounded focus:border-indigo-500 focus:ring-indigo-500">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg shadow-indigo-600/20 transition-transform transform hover:scale-105">
                        Simpan Data Absensi
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function setSemua(status) {
            // Cari semua radio button dengan value sesuai status (H/L/dll)
            const radios = document.querySelectorAll(`input[value="${status}"]`);
            radios.forEach(radio => {
                radio.checked = true;
            });
        }
    </script>
</x-app-layout>