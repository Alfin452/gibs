<x-app-layout>
    @php
    \Carbon\Carbon::setLocale('id');
    @endphp
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 leading-tight">
            {{ __('Rekap Kehadiran') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-[85rem] mx-auto px-1 sm:px-6 lg:px-1 space-y-6">

            @if (session('success'))
            <div id="alert-success" class="flex items-center p-4 mb-4 text-emerald-800 border border-emerald-200 rounded-xl bg-emerald-50 shadow-sm transition-all duration-500" role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-emerald-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                </svg>
                <div class="ml-1 text-sm font-medium">
                    <span class="font-bold block text-emerald-900">Berhasil!</span> {{ session('success') }}
                </div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-emerald-50 text-emerald-500 rounded-lg focus:ring-2 focus:ring-emerald-400 p-1.5 hover:bg-emerald-200 inline-flex items-center justify-center h-8 w-8" data-dismiss-target="#alert-success" aria-label="Close" onclick="this.parentElement.remove()">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
            @endif

            @if (session('warning'))
            <div id="alert-warning" class="flex items-center p-4 mb-4 text-yellow-800 border border-yellow-200 rounded-xl bg-yellow-50 shadow-sm" role="alert">
                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-yellow-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z" />
                </svg>
                <div class="ml-1 text-sm font-medium">
                    <span class="font-bold block text-yellow-900">Perhatian!</span> {{ session('warning') }}
                </div>
            </div>
            @endif

            <div class="bg-white border border-gray-100 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] overflow-hidden">
                <div class="p-6 md:p-8">

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-5">
                        <div class="space-y-1">
                            <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight">Riwayat Absensi</h3>
                            <p class="text-sm text-gray-500 font-medium">Monitoring data kehadiran siswa secara real-time.</p>
                        </div>

                        <div class="flex flex-wrap gap-3 w-full md:w-auto">
                            <button onclick="openModal()" class="flex-1 md:flex-none inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 focus:ring-4 focus:ring-emerald-100 transition-all shadow-sm">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Lihat Rekap
                            </button>

                            <a href="{{ route('absensi.create') }}" class="flex-1 md:flex-none inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 shadow-lg shadow-indigo-200 transition-all hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Input Baru
                            </a>
                        </div>
                    </div>

                    <div class="bg-gray-50/50 border border-gray-100 rounded-2xl p-5 mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                            <div class="md:col-span-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Pencarian</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Cari Mata Pelajaran atau Kelas..."
                                        class="pl-10 block w-full rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 shadow-sm transition-all hover:border-gray-300">
                                </div>
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Bulan</label>
                                <div class="relative">
                                    <select id="filter-bulan" name="bulan" class="block w-full rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 pl-4 pr-8 shadow-sm transition-all hover:border-gray-300 cursor-pointer appearance-none">
                                        <option value="">Semua Bulan</option>
                                        @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Tahun</label>
                                <div class="relative">
                                    <select id="filter-tahun" name="tahun" class="block w-full rounded-xl border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 pl-4 pr-8 shadow-sm transition-all hover:border-gray-300 cursor-pointer appearance-none">
                                        <option value="">Semua Tahun</option>
                                        @php $cy = date('Y'); @endphp
                                        @for($y = $cy; $y >= $cy-2; $y--)
                                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2 flex gap-2">
                                <button type="button" id="btn-filter" class="flex-1 bg-gray-900 text-white px-4 py-3 rounded-xl text-sm font-bold hover:bg-black transition-all shadow-md active:scale-95 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('absensi.index') }}" class="bg-white border border-gray-200 text-gray-500 px-3.5 py-3 rounded-xl text-sm font-bold hover:bg-gray-50 hover:text-red-600 hover:border-red-200 transition-all shadow-sm" title="Reset Filter">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div id="loading-indicator" class="hidden">
                        <div class="flex flex-col items-center justify-center py-12">
                            <svg class="animate-spin h-10 w-10 text-indigo-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-500">Memuat data...</span>
                        </div>
                    </div>

                    <div id="table-container" class="overflow-hidden border border-gray-100 rounded-2xl ring-1 ring-gray-100 sm:mx-0">
                        <div class="overflow-x-auto relative min-h-[300px]">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kelas</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                                        <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">H</th>
                                        <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">S</th>
                                        <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">I</th>
                                        <th scope="col" class="px-2 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">A</th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="absensi-table-body" class="bg-white divide-y divide-gray-200">
                                    @include('absensi.partials.table_rows', ['riwayat' => $riwayat])
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="pagination-wrapper" class="mt-6">
                        {{ $riwayat->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div id="rekapModal" class="relative z-[9999] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-100"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                    <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">Lihat Rekap Bulanan</h3>
                                <p class="text-xs text-gray-500">Pilih filter untuk melihat laporan.</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 bg-white">
                        <form action="{{ route('absensi.laporan.view') }}" method="GET">
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Mata Pelajaran</label>
                                    <select name="id_mapel" required class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3 transition-all cursor-pointer">
                                        <option value="">-- Pilih Mapel --</option>
                                        @foreach($mapels_list as $m)
                                        <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kelas</label>
                                    <select name="id_kelas" required class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3 transition-all cursor-pointer">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($kelas_list as $k)
                                        <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Bulan</label>
                                        <select name="bulan" required class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3 transition-all cursor-pointer">
                                            @foreach(range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tahun</label>
                                        <select name="tahun" required class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3 transition-all cursor-pointer">
                                            @php $cy = date('Y'); @endphp
                                            <option value="{{ $cy }}">{{ $cy }}</option>
                                            <option value="{{ $cy-1 }}">{{ $cy-1 }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-gray-50">
                                <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition-colors text-sm">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all text-sm flex items-center gap-2 transform active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Tampilkan Laporan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. PINDAHKAN MODAL KE BODY (FIX Z-INDEX & POSISI)
            const modalElement = document.getElementById('rekapModal');
            if (modalElement) {
                document.body.appendChild(modalElement);
            }

            // 2. LIVE SEARCH & FILTER AJAX
            const searchInput = document.getElementById('search-input');
            const filterBulan = document.getElementById('filter-bulan');
            const filterTahun = document.getElementById('filter-tahun');
            const btnFilter = document.getElementById('btn-filter');
            const tableBody = document.getElementById('absensi-table-body');
            const tableContainer = document.getElementById('table-container');
            const loadingIndicator = document.getElementById('loading-indicator');
            const paginationWrapper = document.getElementById('pagination-wrapper');

            let debounceTimer;

            function fetchData() {
                // UI Loading State
                tableContainer.classList.add('opacity-50');
                loadingIndicator.classList.remove('hidden');

                const params = new URLSearchParams({
                    search: searchInput.value,
                    bulan: filterBulan.value,
                    tahun: filterTahun.value
                });

                const newUrl = `${window.location.pathname}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);

                fetch(newUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        const newTableBody = doc.getElementById('absensi-table-body');
                        const newPagination = doc.getElementById('pagination-wrapper');

                        if (newTableBody) tableBody.innerHTML = newTableBody.innerHTML;
                        if (newPagination) paginationWrapper.innerHTML = newPagination.innerHTML;
                    })
                    .catch(error => console.error('Error:', error))
                    .finally(() => {
                        // Restore UI
                        tableContainer.classList.remove('opacity-50');
                        loadingIndicator.classList.add('hidden');
                    });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchData, 500);
            });

            btnFilter.addEventListener('click', function(e) {
                e.preventDefault();
                fetchData();
            });
        });

        // 3. MODAL FUNCTIONS
        function openModal() {
            const modal = document.getElementById('rekapModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Kunci Scroll Body
            }
        }

        function closeModal() {
            const modal = document.getElementById('rekapModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; // Lepas Scroll Body
            }
        }
    </script>
</x-app-layout>