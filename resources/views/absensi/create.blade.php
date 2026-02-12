<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 leading-tight">
            {{ __('Input Kehadiran') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">

    <div class="py-2">
        <div class="max-w-[85rem] mx-auto px-2 sm:px-6 lg:px-2">

            <form action="{{ route('absensi.cek') }}" method="POST">
                @csrf

                <div class="bg-white border border-gray-100 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] overflow-hidden">

                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Parameter Absensi</h3>
                            <p class="text-sm text-gray-500">Pilih Mapel & Kelas, lalu pilih tanggal di kalender.</p>
                        </div>

                        <div id="status-indicator" class="hidden inline-flex items-center px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-100 animate-pulse">
                            <svg class="animate-spin -ml-1 mr-2 h-3 w-3 text-indigo-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mencari Jadwal...
                        </div>
                    </div>

                    <div class="p-6 md:p-8 space-y-8">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Bulan</label>
                                <div class="relative">
                                    <select id="bulan" class="block w-full rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 transition-all cursor-pointer filter-trigger shadow-sm hover:border-gray-300">
                                        @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Tahun</label>
                                <div class="relative">
                                    <select id="tahun" class="block w-full rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 transition-all cursor-pointer filter-trigger shadow-sm hover:border-gray-300">
                                        @php $cy = date('Y'); @endphp
                                        <option value="{{ $cy }}">{{ $cy }}</option>
                                        <option value="{{ $cy-1 }}">{{ $cy-1 }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Mata Pelajaran</label>
                                <div class="relative">
                                    <select name="id_mapel" id="id_mapel" required class="block w-full rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 transition-all cursor-pointer filter-trigger shadow-sm hover:border-gray-300">
                                        <option value="">-- Pilih Mapel --</option>
                                        @if(isset($mapels) && count($mapels) > 0)
                                        @foreach($mapels as $m)
                                        <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }}</option>
                                        @endforeach
                                        @else
                                        <option value="" disabled>Data Mapel Tidak Ditemukan</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Kelas Target</label>
                                <div class="relative">
                                    <select name="id_kelas" id="id_kelas" required class="block w-full rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 transition-all cursor-pointer filter-trigger shadow-sm hover:border-gray-300">
                                        <option value="">-- Pilih Kelas --</option>
                                        @if(isset($kelas) && count($kelas) > 0)
                                        @foreach($kelas as $k)
                                        <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                                        @endforeach
                                        @else
                                        <option value="" disabled>Data Kelas Tidak Ditemukan</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="kombinasi_jadwal" id="kombinasi_jadwal">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-6">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 ml-1">Pilih Tanggal Pertemuan (Kalender)</label>

                            <div class="flex flex-col md:flex-row gap-4 items-stretch">
                                <div class="relative flex-grow">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" name="tanggal" id="tanggal" required disabled
                                        class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed shadow-inner focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 font-medium transition-all"
                                        placeholder="-- Pilih Mapel & Kelas dahulu --">
                                </div>

                                <button type="submit" id="btn-submit" disabled class="md:w-auto w-full inline-flex items-center justify-center px-8 py-3 border border-transparent text-sm font-bold rounded-xl text-white bg-gray-300 cursor-not-allowed transition-all shadow-none focus:outline-none focus:ring-4 focus:ring-indigo-100 gap-2">
                                    <span>Buka Absensi</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </button>
                            </div>
                            <p id="helper-text" class="text-xs text-gray-400 mt-2 ml-1">Kalender akan aktif otomatis setelah filter dipilih.</p>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Referensi Elemen DOM
            const mapelSelect = document.getElementById('id_mapel');
            const kelasSelect = document.getElementById('id_kelas');
            const bulanSelect = document.getElementById('bulan');
            const tahunSelect = document.getElementById('tahun');
            const tanggalInput = document.getElementById('tanggal');
            const btnSubmit = document.getElementById('btn-submit');
            const kombinasiInput = document.getElementById('kombinasi_jadwal');

            const statusIndicator = document.getElementById('status-indicator');
            const helperText = document.getElementById('helper-text');

            // 1. Inisialisasi Flatpickr
            // HAPUS konfigurasi 'disable' di sini agar tidak bentrok.
            // Kita andalkan atribut 'disabled' pada tag <input> HTML.
            let fpInstance = flatpickr(tanggalInput, {
                locale: 'id',
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "l, d F Y",
                // Kita tidak set 'disable' di sini, kita atur dinamis nanti
                onChange: function(selectedDates, dateStr, instance) {
                    if (dateStr) {
                        enableSubmit();
                    } else {
                        disableSubmit();
                    }
                }
            });

            // 2. Fungsi Utama Fetch Data Jadwal
            async function fetchTanggal() {
                const mapel = mapelSelect.value;
                const kelas = kelasSelect.value;
                const bulan = bulanSelect.value;
                const tahun = tahunSelect.value;

                // Update input hidden untuk keperluan controller
                if (mapel && kelas) {
                    kombinasiInput.value = `${kelas}-${mapel}`;
                } else {
                    kombinasiInput.value = "";
                }

                // Jika filter belum lengkap, reset dan return
                if (!mapel || !kelas) {
                    resetUI();
                    return;
                }

                setLoading(true);

                try {
                    // Request ke Server
                    const url = `{{ route('absensi.get-tanggal') }}?id_mapel=${mapel}&id_kelas=${kelas}&bulan=${bulan}&tahun=${tahun}`;

                    const response = await fetch(url);

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    const allowedDates = [];

                    // Logika Penerapan Tanggal
                    if (data && data.length > 0) {
                        // KASUS A: Jadwal Ditemukan
                        data.forEach(item => {
                            allowedDates.push(item.tanggal);
                        });

                        // Set Tanggal yang boleh dipilih
                        fpInstance.set('enable', allowedDates);

                        updateStatus(`${allowedDates.length} Tanggal Jadwal Ditemukan`, 'green');
                    } else {
                        // KASUS B: Jadwal Kosong
                        // Kita izinkan SEMUA tanggal agar user tidak terblokir
                        fpInstance.set('enable', []); // Enable All

                        updateStatus('Jadwal spesifik tidak ditemukan. Mode bebas aktif.', 'orange');
                    }

                    // PENTING: Buka Kunci Input Secara Visual & Fungsional
                    enableInputVisual();

                } catch (error) {
                    console.error('Fetch Error:', error);
                    // KASUS C: Error Koneksi
                    // Tetap buka input sebagai fallback
                    fpInstance.set('enable', []);
                    enableInputVisual();

                    updateStatus('Gagal mengambil jadwal (Mode Bebas).', 'red');
                } finally {
                    setLoading(false);
                }
            }

            // --- Helper Functions ---

            function enableInputVisual() {
                // 1. Buka input asli
                tanggalInput.disabled = false;

                // 2. Buka input visual Flatpickr (Alt Input)
                // Ini yang dilihat oleh user, jadi kelas CSS harus diubah di sini
                if (fpInstance.altInput) {
                    fpInstance.altInput.disabled = false;
                    fpInstance.altInput.classList.remove('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
                    fpInstance.altInput.classList.add('bg-white', 'text-gray-900', 'cursor-pointer', 'border-indigo-300');
                } else {
                    // Fallback jika altInput tidak ada (jarang terjadi jika altInput: true)
                    tanggalInput.classList.remove('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
                    tanggalInput.classList.add('bg-white', 'text-gray-900', 'cursor-pointer', 'border-indigo-300');
                }

                tanggalInput.placeholder = "Klik untuk pilih tanggal...";
                if (fpInstance.altInput) fpInstance.altInput.placeholder = "Klik untuk pilih tanggal...";
            }

            function disableInputVisual() {
                tanggalInput.disabled = true;

                if (fpInstance.altInput) {
                    fpInstance.altInput.disabled = true;
                    fpInstance.altInput.classList.add('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
                    fpInstance.altInput.classList.remove('bg-white', 'text-gray-900', 'cursor-pointer', 'border-indigo-300');
                    fpInstance.altInput.placeholder = "-- Pilih Mapel & Kelas dahulu --";
                }

                tanggalInput.classList.add('bg-gray-50', 'text-gray-400', 'cursor-not-allowed');
                tanggalInput.classList.remove('bg-white', 'text-gray-900', 'cursor-pointer', 'border-indigo-300');
                tanggalInput.placeholder = "-- Pilih Mapel & Kelas dahulu --";
            }

            function setLoading(isLoading) {
                if (isLoading) {
                    statusIndicator.classList.remove('hidden');

                    // Kunci sementara loading
                    tanggalInput.disabled = true;
                    if (fpInstance.altInput) {
                        fpInstance.altInput.disabled = true;
                        fpInstance.altInput.placeholder = "Sedang mengecek jadwal...";
                    }

                } else {
                    statusIndicator.classList.add('hidden');
                    // Kita tidak otomatis buka di sini, karena 'enableInputVisual' dipanggil di blok try/catch
                }
            }

            function updateStatus(text, color) {
                helperText.textContent = text;
                if (color === 'green') {
                    helperText.className = 'text-xs mt-2 ml-1 text-indigo-600 font-bold';
                } else if (color === 'orange') {
                    helperText.className = 'text-xs mt-2 ml-1 text-orange-500 font-bold';
                } else if (color === 'red') {
                    helperText.className = 'text-xs mt-2 ml-1 text-red-500 font-bold';
                } else {
                    helperText.className = 'text-xs mt-2 ml-1 text-gray-500';
                }
            }

            function resetUI() {
                disableInputVisual();
                disableSubmit();
                fpInstance.clear();

                helperText.textContent = 'Jadwal akan muncul otomatis setelah filter dipilih.';
                helperText.className = 'text-xs text-gray-400 mt-2 ml-1';
            }

            function enableSubmit() {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('bg-gray-300', 'cursor-not-allowed', 'shadow-none', 'text-white');
                btnSubmit.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'shadow-lg', 'shadow-indigo-200', 'text-white', 'transform', 'hover:-translate-y-0.5');
            }

            function disableSubmit() {
                btnSubmit.disabled = true;
                btnSubmit.classList.add('bg-gray-300', 'cursor-not-allowed', 'shadow-none', 'text-white');
                btnSubmit.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'shadow-lg', 'shadow-indigo-200', 'transform', 'hover:-translate-y-0.5');
            }

            // Event Listeners
            const filters = document.querySelectorAll('.filter-trigger');
            filters.forEach(el => el.addEventListener('change', fetchTanggal));

            // Initial State Check
            resetUI();
        });
    </script>
</x-app-layout>