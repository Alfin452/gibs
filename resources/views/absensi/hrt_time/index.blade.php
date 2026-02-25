<x-absen-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 leading-tight">
            {{ __('Input Presensi Siswa (HRT)') }} - Kelas {{ $guru->kelas->nama_kelas ?? 'Tidak Diketahui' }}
        </h2>
    </x-slot>

    <div class="py-2 overflow-x-hidden w-full max-w-[100vw]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-2 w-full">

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
                        <strong>Tips:</strong> Blok tabel (drag cursor) seperti Excel untuk mempaste data. Angka 1 otomatis jadi H, p jadi I, l jadi L. Anda juga bisa ketik H, S, I, A, L saat cell terblok untuk mengisi sekaligus!
                    </div>
                </div>

                <style>
                    /* Style Fullscreen */
                    #tableContainer:fullscreen {
                        padding: 24px;
                        background-color: white;
                        overflow-y: auto;
                    }

                    #tableContainer:-webkit-full-screen {
                        padding: 24px;
                        background-color: white;
                        overflow-y: auto;
                    }

                    /* Mencegah text terblokir saat dragging mouse di tabel */
                    #absen-table {
                        user-select: none;
                    }

                    /* Pointer Excel-like */
                    .paste-target {
                        cursor: cell;
                    }

                    /* Style Khusus untuk cell yang sedang di-blok */
                    .selected-cell {
                        background-color: #dbeafe !important;
                        /* Warna biru muda */
                        box-shadow: inset 0 0 0 2px #3b82f6 !important;
                        /* Border tebal biru */
                        color: #1e3a8a !important;
                        /* Text biru tua */
                        outline: none !important;
                    }

                    /* --- KUSTOMISASI SCROLLBAR AGAR LEBIH BESAR --- */
                    .custom-scrollbar::-webkit-scrollbar {
                        height: 16px;
                        width: 16px;
                    }

                    .custom-scrollbar::-webkit-scrollbar-track {
                        background: #f1f5f9;
                        border-radius: 8px;
                    }

                    .custom-scrollbar::-webkit-scrollbar-thumb {
                        background: #94a3b8;
                        border-radius: 8px;
                        border: 3px solid #f1f5f9;
                    }

                    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                        background: #64748b;
                    }

                    .custom-scrollbar {
                        scrollbar-width: auto;
                        scrollbar-color: #94a3b8 #f1f5f9;
                    }
                </style>

                {{-- FORM ABSENSI --}}
                <form action="{{ route('hrt.time.store') }}" method="POST" id="form-absensi" class="w-full">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">

                    {{-- WRAPPER FULLSCREEN --}}
                    <div id="tableContainer" class="w-full transition-all duration-300 bg-white">

                        {{-- KETERANGAN KODE (LEGENDA) DI DALAM FULLSCREEN --}}
                        <div class="mb-4 flex flex-wrap gap-4 items-center bg-gray-50/50 p-3 rounded-lg border border-gray-100">
                            <span class="text-sm font-semibold text-gray-600 mr-2">Keterangan :</span>
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

                        {{-- TOMBOL AKSI CEPAT HARI INI & FULLSCREEN --}}
                        <div class="flex flex-col sm:flex-row justify-between items-center mb-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div class="text-sm text-gray-700 font-medium mb-2 sm:mb-0">
                                <span class="font-bold text-gray-900">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
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
                                {{-- TOMBOL FULLSCREEN --}}
                                <button type="button" onclick="toggleFullscreen()" class="bg-gray-800 hover:bg-black text-white px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center gap-1.5">
                                    <svg id="fs-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                    </svg>
                                    <span id="fs-text">Fullscreen</span>
                                </button>
                            </div>
                        </div>

                        {{-- TABEL INPUT KEHADIRAN --}}
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
                                                    autocomplete="off"
                                                    data-row="{{ $index }}"
                                                    data-col="{{ $loop->index }}"
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

                        {{-- TOMBOL SIMPAN DI DALAM FULLSCREEN --}}
                        <div class="mt-4 pb-2 flex justify-end w-full">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg hover:shadow-green-500/30 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan Kehadiran
                            </button>
                        </div>

                    </div>
                    {{-- AKHIR WRAPPER FULLSCREEN --}}
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
            // PINDAHKAN MODAL KE BODY AGAR MENUTUPI SELURUH LAYAR
            const modalElement = document.getElementById('importModal');
            if (modalElement) {
                document.body.appendChild(modalElement);
            }

            // Inisialisasi awal render warna & rekap
            updateRekap();
            document.querySelectorAll('.paste-target').forEach(input => {
                updateColor(input);
            });

            // ============================================
            // LOGIC BLOCKING CELLS (SEPERTI EXCEL)
            // ============================================
            let isSelecting = false;
            let startRowIndex = null;
            let startColIndex = null;

            const inputs = document.querySelectorAll('.paste-target');

            inputs.forEach(input => {
                // Saat mouse di-klik (mulai blok)
                input.addEventListener('mousedown', function(e) {
                    isSelecting = true;
                    startRowIndex = parseInt(this.dataset.row);
                    startColIndex = parseInt(this.dataset.col);
                    clearSelection();
                    this.classList.add('selected-cell');
                    this.focus(); // Pastikan fokus agar copy/paste keyboard trigger disini
                });

                // Saat mouse diseret / hover (merender blok kotak)
                input.addEventListener('mouseenter', function(e) {
                    if (isSelecting) {
                        let currentRowIndex = parseInt(this.dataset.row);
                        let currentColIndex = parseInt(this.dataset.col);
                        selectBox(startRowIndex, startColIndex, currentRowIndex, currentColIndex);
                    }
                });
            });

            // Berhenti blok saat klik dilepas
            document.addEventListener('mouseup', function() {
                isSelecting = false;
            });

            // Hapus blok saat klik di luar tabel/input
            document.addEventListener('mousedown', function(e) {
                if (!e.target.classList.contains('paste-target')) {
                    clearSelection();
                }
            });

            function clearSelection() {
                document.querySelectorAll('.selected-cell').forEach(el => {
                    el.classList.remove('selected-cell');
                });
            }

            // Render Kotak yang ter-blok
            function selectBox(r1, c1, r2, c2) {
                clearSelection();
                let minR = Math.min(r1, r2);
                let maxR = Math.max(r1, r2);
                let minC = Math.min(c1, c2);
                let maxC = Math.max(c1, c2);

                for (let r = minR; r <= maxR; r++) {
                    for (let c = minC; c <= maxC; c++) {
                        let cell = document.querySelector(`.paste-target[data-row="${r}"][data-col="${c}"]`);
                        if (cell) {
                            cell.classList.add('selected-cell');
                        }
                    }
                }
            }

            // Fitur Multi-Input/Bulk Edit (Ketik 1 huruf, isi semua yang terblok)
            document.addEventListener('keydown', function(e) {
                let selectedCells = document.querySelectorAll('.selected-cell');

                // Pastikan ada yg terblok & user tidak memencet Ctrl (untuk Copy/Paste)
                if (selectedCells.length > 1 && !e.ctrlKey && !e.metaKey) {
                    let validKeys = ['h', 's', 'i', 'a', 'l', '1', 'p', 'backspace', 'delete'];
                    let key = e.key.toLowerCase();

                    if (validKeys.includes(key)) {
                        e.preventDefault();
                        let val = '';
                        if (key === '1' || key === 'h') val = 'H';
                        else if (key === 'p' || key === 'i') val = 'I';
                        else if (key === 's') val = 'S';
                        else if (key === 'a') val = 'A';
                        else if (key === 'l') val = 'L';
                        else if (key === 'backspace' || key === 'delete') val = '';

                        selectedCells.forEach(cell => {
                            cell.value = val;
                            updateColor(cell);
                        });
                        updateRekap();
                    } else if (key.length === 1) {
                        // Tolak ketikan huruf lain
                        e.preventDefault();
                    }
                }
            });
        });

        // ============================================
        // FITUR FULLSCREEN
        // ============================================
        function toggleFullscreen() {
            let elem = document.getElementById("tableContainer");

            if (!document.fullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        document.addEventListener('fullscreenchange', () => {
            const btnText = document.getElementById('fs-text');
            const fsIcon = document.getElementById('fs-icon');

            if (document.fullscreenElement) {
                btnText.innerText = "Keluar Fullscreen";
                fsIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>`;
            } else {
                btnText.innerText = "Fullscreen";
                fsIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>`;
            }
        });
        // ============================================

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

        function updateColor(input) {
            let val = input.value.toUpperCase();
            input.classList.remove('text-green-700', 'bg-green-50', 'text-yellow-700', 'bg-yellow-50', 'text-blue-700', 'bg-blue-50', 'text-red-700', 'bg-red-50', 'text-gray-700', 'bg-gray-200');

            if (val === 'H') input.classList.add('text-green-700', 'bg-green-50');
            else if (val === 'S') input.classList.add('text-yellow-700', 'bg-yellow-50');
            else if (val === 'I') input.classList.add('text-blue-700', 'bg-blue-50');
            else if (val === 'A') input.classList.add('text-red-700', 'bg-red-50');
            else if (val === 'L') input.classList.add('text-gray-700', 'bg-gray-200');
        }

        function updateRekap() {
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

        // ============================================
        // PASTE / COPY EXCEL LOGIC KE CELL YANG DIBLOK
        // ============================================
        document.addEventListener('paste', function(e) {
            let target = e.target;
            if (!target.classList.contains('paste-target')) return;
            e.preventDefault();

            let pasteData = (e.clipboardData || window.clipboardData).getData('text');
            let rows = pasteData.split(/\r\n|\n|\r/);

            // Deteksi cell mulai dari block (jika diblok) ATAU dari cell yang ter-klik
            let startRowIdx, startColIdx;
            let selectedCells = document.querySelectorAll('.selected-cell');

            if (selectedCells.length > 0) {
                // Cari ujung kiri atas dari blok
                let minRow = 999999;
                let minCol = 999999;
                selectedCells.forEach(cell => {
                    minRow = Math.min(minRow, parseInt(cell.dataset.row));
                    minCol = Math.min(minCol, parseInt(cell.dataset.col));
                });
                startRowIdx = minRow;
                startColIdx = minCol;
            } else {
                startRowIdx = parseInt(target.dataset.row);
                startColIdx = parseInt(target.dataset.col);
            }

            // Loop untuk menempatkan data ke tabel
            for (let i = 0; i < rows.length; i++) {
                if (!rows[i].trim()) continue;

                let cols = rows[i].split('\t');

                for (let j = 0; j < cols.length; j++) {
                    // Seleksi element yang dituju via data-row dan data-col
                    let targetCell = document.querySelector(`.paste-target[data-row="${startRowIdx + i}"][data-col="${startColIdx + j}"]`);

                    if (targetCell) {
                        let val = cols[j].trim().toLowerCase();
                        let status = '';

                        if (val === '1' || val === 'h') status = 'H';
                        else if (val === 'p' || val === 'i') status = 'I';
                        else if (val === 's') status = 'S';
                        else if (val === 'a') status = 'A';
                        else if (val === 'l') status = 'L';

                        if (status !== '') {
                            targetCell.value = status;
                        } else {
                            targetCell.value = '';
                        }

                        updateColor(targetCell);
                    }
                }
            }

            updateRekap();

            Swal.fire({
                icon: 'success',
                title: 'Data berhasil disalin dan ditaruh!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500
            });
        });
    </script>
</x-absen-layout>