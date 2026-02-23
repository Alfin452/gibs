<x-absen-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Kehadiran HRT (Wali Kelas)') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-[100rem] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100 p-6">

                {{-- FILTER SECTION --}}
                <form method="GET" action="{{ route('hrt.time.index') }}" class="flex flex-wrap gap-4 items-end mb-6">
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
                    <div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                            Terapkan Filter
                        </button>
                    </div>
                </form>

                <div class="mb-3 text-sm text-gray-500 bg-blue-50 p-3 rounded-lg border border-blue-100 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><strong>Tips:</strong> Anda bisa langsung men-copy rentang data absensi dari Excel dan mem-pastenya (Ctrl+V) ke dalam kotak input di bawah. Rekapitulasi di ujung kanan akan otomatis terhitung.</span>
                </div>

                {{-- TABLE SECTION (Horizontal Scroll) --}}
                <form action="#" method="POST" id="form-absensi">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">

                    <div class="overflow-x-auto custom-scrollbar border border-gray-200 rounded-lg pb-4 relative">
                        <table class="w-full text-sm text-left whitespace-nowrap" id="absen-table">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    {{-- Kolom Kiri Sticky --}}
                                    <th scope="col" class="px-4 py-3 sticky left-0 z-20 bg-gray-50 border-r border-gray-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">No</th>
                                    <th scope="col" class="px-4 py-3 sticky left-[50px] z-20 bg-gray-50 border-r border-gray-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">Nama Siswa</th>

                                    {{-- Kolom Tanggal Tengah --}}
                                    @foreach($dates as $date)
                                    <th scope="col" class="px-2 py-3 border-r border-gray-200 text-center min-w-[40px] {{ $date['is_sunday'] ? 'bg-red-50 text-red-500' : '' }}">
                                        {{ $date['tgl'] }}
                                    </th>
                                    @endforeach

                                    {{-- Kolom Rekap Kanan (Tetap ikut scroll, ada di paling ujung) --}}
                                    <th scope="col" class="px-3 py-3 text-center border-l-2 border-gray-300 bg-green-50 text-green-700">H</th>
                                    <th scope="col" class="px-3 py-3 text-center bg-yellow-50 text-yellow-700">S</th>
                                    <th scope="col" class="px-3 py-3 text-center bg-blue-50 text-blue-700">I</th>
                                    <th scope="col" class="px-3 py-3 text-center bg-red-50 text-red-700">A</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswa as $index => $s)
                                <tr class="bg-white border-b border-gray-100 hover:bg-gray-50 transition-colors student-row">
                                    {{-- Kiri Sticky --}}
                                    <td class="px-4 py-2 sticky left-0 z-10 bg-white group-hover:bg-gray-50 border-r border-gray-200 font-medium">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-2 sticky left-[50px] z-10 bg-white group-hover:bg-gray-50 border-r border-gray-200 font-medium truncate max-w-[200px]">
                                        {{ $s->nama_siswa ?? $s->nama }}
                                    </td>

                                    {{-- Input Tengah --}}
                                    @foreach($dates as $date)
                                    @php
                                    $key = $s->id_siswa . '_' . $date['tgl'];
                                    $val = $kehadiran[$key] ?? '';
                                    @endphp
                                    <td class="px-1 py-1 border-r border-gray-100 {{ $date['is_sunday'] ? 'bg-gray-100' : '' }}">
                                        @if($date['is_sunday'])
                                        <div class="w-full text-center text-gray-300 text-xs">-</div>
                                        @else
                                        <input type="text"
                                            name="absen[{{ $s->id_siswa }}][{{ $date['full_date'] }}]"
                                            value="{{ $val }}"
                                            maxlength="1"
                                            class="paste-target w-8 h-8 text-center text-sm font-bold uppercase rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 p-0"
                                            oninput="this.value = this.value.toUpperCase().replace(/[^HSIA]/g, ''); updateRekap(this)">
                                        @endif
                                    </td>
                                    @endforeach

                                    {{-- Rekap Ujung Kanan --}}
                                    <td class="px-3 py-2 text-center border-l-2 border-gray-300 font-bold text-green-600 total-h">0</td>
                                    <td class="px-3 py-2 text-center font-bold text-yellow-600 total-s">0</td>
                                    <td class="px-3 py-2 text-center font-bold text-blue-600 total-i">0</td>
                                    <td class="px-3 py-2 text-center font-bold text-red-600 total-a">0</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end">
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

    <script>
        // 1. Fungsi Menghitung Rekap (H, S, I, A) secara Realtime per Baris
        function updateRekap(inputElement = null) {
            let rows = document.querySelectorAll('.student-row');
            rows.forEach(row => {
                let inputs = row.querySelectorAll('.paste-target');
                let h = 0,
                    s = 0,
                    i = 0,
                    a = 0;

                inputs.forEach(inp => {
                    let val = inp.value.toUpperCase();
                    if (val === 'H') h++;
                    else if (val === 'S') s++;
                    else if (val === 'I') i++;
                    else if (val === 'A') a++;
                });

                row.querySelector('.total-h').innerText = h;
                row.querySelector('.total-s').innerText = s;
                row.querySelector('.total-i').innerText = i;
                row.querySelector('.total-a').innerText = a;
            });
        }

        // Hitung rekap saat halaman pertama kali diload (untuk data yg sudah ada di db)
        document.addEventListener('DOMContentLoaded', function() {
            updateRekap();
        });

        // 2. Script Auto-Paste dari Excel
        document.addEventListener('paste', function(e) {
            let target = e.target;
            // Jika paste bukan di kotak input absen, abaikan
            if (!target.classList.contains('paste-target')) return;
            e.preventDefault();

            // Ambil data dari clipboard (text raw dari excel, terpisahkan Tab dan Enter)
            let pasteData = (e.clipboardData || window.clipboardData).getData('text');
            let rows = pasteData.split(/\r\n|\n|\r/);

            let currentTd = target.closest('td');
            let currentRow = target.closest('tr');

            // Dapatkan index baris (siswa) saat ini dan index kolom (tanggal) saat ini
            let trs = Array.from(currentRow.closest('tbody').querySelectorAll('tr'));
            let startRowIdx = trs.indexOf(currentRow);

            let tds = Array.from(currentRow.querySelectorAll('td'));
            let startColIdx = tds.indexOf(currentTd);

            // Mulai distribusikan data
            for (let i = 0; i < rows.length; i++) {
                if (!rows[i].trim()) continue; // Skip baris kosong

                let cols = rows[i].split('\t');
                let targetRow = trs[startRowIdx + i];
                if (!targetRow) break; // Jika baris siswa habis, berhenti

                let targetTds = targetRow.querySelectorAll('td');

                let pasteColIndex = 0; // index data dari Excel
                let tableColIndex = startColIdx; // index kolom di tabel kita

                while (pasteColIndex < cols.length) {
                    let targetCol = targetTds[tableColIndex];
                    if (!targetCol) break; // Jika kolom tanggal di web habis, berhenti

                    // Cek apakah di dalam TD ini ada input absen
                    let input = targetCol.querySelector('input.paste-target');
                    if (input) {
                        // Bersihkan spasi dan pastikan format benar, jika salah kosongkan
                        let val = cols[pasteColIndex].trim().toUpperCase();
                        if (['H', 'S', 'I', 'A'].includes(val)) {
                            input.value = val;
                        }
                        pasteColIndex++; // Lanjut data excel ke kanan
                    }
                    // Maju ke kolom (TD) berikutnya (termasuk melewati hari minggu)
                    tableColIndex++;
                }
            }

            // Update ulang semua angka rekap (H,S,I,A) setelah proses paste selesai
            updateRekap();

            // Berikan feedback visual (Opsional)
            Swal.fire({
                icon: 'success',
                title: 'Data berhasil disalin!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        });
    </script>
</x-app-layout>