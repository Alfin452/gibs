<x-absen-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Presensi Siswa (HRT)') }}
        </h2>
    </x-slot>

    <div class="py-6 overflow-x-hidden w-full max-w-[100vw]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">

            <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 p-4 sm:p-6 w-full min-w-0">

                {{-- FILTER & TOMBOL IMPORT SECTION --}}
                <form method="GET" action="{{ route('hrt.time.index') }}" class="flex flex-wrap gap-4 items-end mb-6 w-full">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                        <select name="bulan" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                                </option>
                                @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <select name="tahun" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            @for($y=date('Y'); $y>=date('Y')-2; $y--)
                            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                            Terapkan Filter
                        </button>
                        <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Upload Excel
                        </button>
                    </div>
                </form>

                <div class="mb-4 text-sm text-gray-500 bg-blue-50 p-3 rounded-lg border border-blue-100 flex items-start sm:items-center gap-3 w-full">
                    <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="leading-relaxed">
                        <strong>Tips:</strong> Anda bisa langsung mengupload file excel HRT time anda. Angka 1 otomatis jadi H, p jadi I, l jadi L (Libur).
                    </div>
                </div>

                {{-- KETERANGAN KODE (LEGENDA) --}}
                <div class="mb-5 flex flex-wrap gap-4 items-center bg-gray-50/50 p-3 rounded-lg border border-gray-100">
                    <span class="text-sm font-semibold text-gray-600 mr-2">Keterangan Kode:</span>
                    <div class="flex items-center gap-1.5">
                        <span class="w-6 h-6 rounded flex items-center justify-center bg-green-50 text-green-700 font-bold text-xs border border-green-200 shadow-sm">H</span>
                        <span class="text-sm text-gray-600">= Hadir</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-6 h-6 rounded flex items-center justify-center bg-yellow-50 text-yellow-700 font-bold text-xs border border-yellow-200 shadow-sm">S</span>
                        <span class="text-sm text-gray-600">= Sakit</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-6 h-6 rounded flex items-center justify-center bg-blue-50 text-blue-700 font-bold text-xs border border-blue-200 shadow-sm">I</span>
                        <span class="text-sm text-gray-600">= Izin</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-6 h-6 rounded flex items-center justify-center bg-red-50 text-red-700 font-bold text-xs border border-red-200 shadow-sm">A</span>
                        <span class="text-sm text-gray-600">= Alpha</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-6 h-6 rounded flex items-center justify-center bg-gray-200 text-gray-700 font-bold text-xs border border-gray-300 shadow-sm">L</span>
                        <span class="text-sm text-gray-600">= Libur</span>
                    </div>
                </div>

                {{-- FORM ABSENSI --}}
                <form action="#" method="POST" id="form-absensi" class="w-full">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">

                    {{-- TOMBOL AKSI CEPAT HARI INI --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <div class="text-sm text-gray-700 font-medium mb-2 sm:mb-0">
                            <span class="font-bold text-gray-900">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" onclick="setAllForToday('H')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Set Hadir Semua
                            </button>
                            <button type="button" onclick="setAllForToday('L')" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Set Libur Semua
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 w-full max-w-full">
                        <div class="w-full overflow-x-auto border border-gray-200 rounded-lg pb-4 relative custom-scrollbar">
                            <table class="w-full min-w-max text-sm text-left whitespace-nowrap" id="absen-table">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        {{-- Kolom Kiri Sticky --}}
                                        <th scope="col" class="px-2 py-3 sticky left-0 z-20 bg-gray-50 border-r border-gray-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] text-center align-middle">No</th>
                                        <th scope="col" class="px-4 py-3 sticky left-[40px] z-20 bg-gray-50 border-r border-gray-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] align-middle">Nama Siswa</th>

                                        {{-- Kolom Tanggal Tengah --}}
                                        @foreach($dates as $date)
                                        @php
                                        $dayName = \Carbon\Carbon::parse($date['full_date'])->locale('id')->isoFormat('ddd');
                                        @endphp
                                        <th scope="col" class="px-2 py-2 border-r border-gray-200 text-center min-w-[40px] align-middle {{ $date['is_sunday'] ? 'bg-red-50 text-red-500' : '' }}">
                                            <div class="text-[10px] font-normal opacity-75 mb-0.5 tracking-wide">{{ $dayName }}</div>
                                            <div class="font-bold text-sm">{{ $date['tgl'] }}</div>
                                        </th>
                                        @endforeach

                                        {{-- Kolom Rekap Kanan --}}
                                        <th scope="col" class="px-3 py-3 text-center border-l-2 border-gray-300 bg-green-50 text-green-700 align-middle">H</th>
                                        <th scope="col" class="px-3 py-3 text-center bg-yellow-50 text-yellow-700 align-middle">S</th>
                                        <th scope="col" class="px-3 py-3 text-center bg-blue-50 text-blue-700 align-middle">I</th>
                                        <th scope="col" class="px-3 py-3 text-center bg-red-50 text-red-700 align-middle">A</th>
                                        <th scope="col" class="px-3 py-3 text-center bg-gray-100 text-gray-700 align-middle border-l border-gray-200">L</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siswa as $index => $s)
                                    <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors student-row">
                                        {{-- Kiri Sticky --}}
                                        <td class="px-2 py-2 sticky left-0 z-10 bg-white group-hover:bg-gray-50 border-r border-gray-200 font-medium text-center">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-4 py-2 sticky left-[40px] z-10 bg-white group-hover:bg-gray-50 border-r border-gray-200 font-medium">
                                            {{ $s->nama_siswa ?? $s->nama }}
                                        </td>

                                        {{-- Input Tengah --}}
                                        @foreach($dates as $date)
                                        @php
                                        $key = $s->id_siswa . '_' . $date['tgl'];
                                        $val = $kehadiran[$key] ?? '';
                                        @endphp
                                        <td class="px-1 py-1 border-r border-gray-100 text-center {{ $date['is_sunday'] ? 'bg-gray-100' : '' }}">
                                            @if($date['is_sunday'])
                                            <div class="w-full text-center text-gray-300 text-xs">-</div>
                                            @else
                                            <input type="text"
                                                name="absen[{{ $s->id_siswa }}][{{ $date['full_date'] }}]"
                                                value="{{ $val }}"
                                                maxlength="1"
                                                class="paste-target w-8 h-8 text-center text-sm font-bold uppercase rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 p-0 transition-colors duration-200"
                                                oninput="
                                                    let v = this.value.toLowerCase();
                                                    if(v === '1' || v === 'h') this.value = 'H';
                                                    else if(v === 'p' || v === 'i') this.value = 'I';
                                                    else if(v === 's') this.value = 'S';
                                                    else if(v === 'a') this.value = 'A';
                                                    else if(v === 'l') this.value = 'L';
                                                    else this.value = '';
                                                    updateRekap(this);
                                                    updateColor(this);
                                                ">
                                            @endif
                                        </td>
                                        @endforeach

                                        {{-- Rekap Ujung Kanan --}}
                                        <td class="px-3 py-2 text-center border-l-2 border-gray-300 font-bold text-green-600 total-h">0</td>
                                        <td class="px-3 py-2 text-center font-bold text-yellow-600 total-s">0</td>
                                        <td class="px-3 py-2 text-center font-bold text-blue-600 total-i">0</td>
                                        <td class="px-3 py-2 text-center font-bold text-red-600 total-a">0</td>
                                        <td class="px-3 py-2 text-center border-l border-gray-200 font-bold text-gray-600 total-l bg-gray-50">0</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end w-full">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg hover:shadow-green-500/30 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Kehadiran
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div id="importModal" class="fixed inset-0 z-[99999] hidden bg-gray-900 bg-opacity-60 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Import Data dari Excel</h3>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('hrt.time.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Laporan Absen (.xlsx / .csv)</label>
                    <input type="file" name="file_excel" accept=".xlsx, .xls, .csv" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50" required>
                    <p class="text-xs text-gray-500 mt-3 text-justify leading-relaxed">
                        Data yang diimport akan ditargetkan untuk <b>Bulan {{ $bulan }}, Tahun {{ $tahun }}</b>. Sistem otomatis mendeteksi kehadiran (1=Hadir, p=Izin, s=Sakit, a=Alpha, l=Libur).
                    </p>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors">Batal</button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // PINDAHKAN MODAL KE BODY AGAR MENUTUPI SELURUH LAYAR (TERMASUK SIDEBAR)
            const modalElement = document.getElementById('importModal');
            if (modalElement) {
                document.body.appendChild(modalElement);
            }

            // Jalankan fungsi awal untuk merender warna & rekap
            updateRekap();
            document.querySelectorAll('.paste-target').forEach(input => {
                updateColor(input);
            });
        });

        // Fungsi Set Hadir/Libur Semua di Hari Ini
        function setAllForToday(status) {
            let todayDate = '{{ \Carbon\Carbon::now()->format("Y-m-d") }}';
            let inputs = document.querySelectorAll(`input[name$="[${todayDate}]"]`);

            if (inputs.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ditemukan',
                    text: 'Tanggal hari ini tidak ada pada bulan/tahun yang sedang Anda buka di tabel.',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }

            inputs.forEach(input => {
                input.value = status;
                updateColor(input);
            });

            updateRekap();

            let label = status === 'H' ? 'Hadir' : 'Libur';
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: `Seluruh siswa telah di-set ${label} untuk hari ini.`,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            });
        }

        // Fungsi Mewarnai Kotak Input
        function updateColor(input) {
            let val = input.value.toUpperCase();

            input.classList.remove('text-green-700', 'bg-green-50', 'text-yellow-700', 'bg-yellow-50', 'text-blue-700', 'bg-blue-50', 'text-red-700', 'bg-red-50', 'text-gray-700', 'bg-gray-200');

            if (val === 'H') {
                input.classList.add('text-green-700', 'bg-green-50');
            } else if (val === 'S') {
                input.classList.add('text-yellow-700', 'bg-yellow-50');
            } else if (val === 'I') {
                input.classList.add('text-blue-700', 'bg-blue-50');
            } else if (val === 'A') {
                input.classList.add('text-red-700', 'bg-red-50');
            } else if (val === 'L') {
                input.classList.add('text-gray-700', 'bg-gray-200');
            }
        }

        // Fungsi Menghitung Rekap
        function updateRekap(inputElement = null) {
            let rows = document.querySelectorAll('.student-row');
            rows.forEach(row => {
                let inputs = row.querySelectorAll('.paste-target');
                let h = 0,
                    s = 0,
                    i = 0,
                    a = 0,
                    l = 0;

                inputs.forEach(inp => {
                    let val = inp.value.toUpperCase();
                    if (val === 'H') h++;
                    else if (val === 'S') s++;
                    else if (val === 'I') i++;
                    else if (val === 'A') a++;
                    else if (val === 'L') l++;
                });

                row.querySelector('.total-h').innerText = h;
                row.querySelector('.total-s').innerText = s;
                row.querySelector('.total-i').innerText = i;
                row.querySelector('.total-a').innerText = a;
                row.querySelector('.total-l').innerText = l;
            });
        }

        // Script Auto-Paste & Mapping Excel
        document.addEventListener('paste', function(e) {
            let target = e.target;
            if (!target.classList.contains('paste-target')) return;
            e.preventDefault();

            let pasteData = (e.clipboardData || window.clipboardData).getData('text');
            let rows = pasteData.split(/\r\n|\n|\r/);

            let currentTd = target.closest('td');
            let currentRow = target.closest('tr');

            let trs = Array.from(currentRow.closest('tbody').querySelectorAll('tr'));
            let startRowIdx = trs.indexOf(currentRow);
            let tds = Array.from(currentRow.querySelectorAll('td'));
            let startColIdx = tds.indexOf(currentTd);

            for (let i = 0; i < rows.length; i++) {
                if (!rows[i].trim()) continue;

                let cols = rows[i].split('\t');
                let targetRow = trs[startRowIdx + i];
                if (!targetRow) break;

                let targetTds = targetRow.querySelectorAll('td');
                let pasteColIndex = 0;
                let tableColIndex = startColIdx;

                while (pasteColIndex < cols.length) {
                    let targetCol = targetTds[tableColIndex];
                    if (!targetCol) break;

                    let input = targetCol.querySelector('input.paste-target');
                    if (input) {
                        let val = cols[pasteColIndex].trim().toLowerCase();
                        let status = '';

                        if (val === '1' || val === 'h') status = 'H';
                        else if (val === 'p' || val === 'i') status = 'I';
                        else if (val === 's') status = 'S';
                        else if (val === 'a') status = 'A';
                        else if (val === 'l') status = 'L';

                        if (status !== '') {
                            input.value = status;
                        } else {
                            input.value = '';
                        }

                        updateColor(input);
                        pasteColIndex++;
                    }
                    tableColIndex++;
                }
            }

            updateRekap();

            Swal.fire({
                icon: 'success',
                title: 'Data berhasil disalin dan dikonversi otomatis!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            });
        });
    </script>
</x-absen-layout>