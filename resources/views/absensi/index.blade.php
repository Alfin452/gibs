<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekap Kehadiran') }}
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-0 space-y-6">

            @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm flex items-center gap-3">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <strong class="font-bold text-green-800">Berhasil!</strong>
                    <span class="block text-green-700 text-sm">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if (session('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg shadow-sm flex items-center gap-3">
                <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <strong class="font-bold text-yellow-800">Perhatian!</strong>
                    <span class="block text-yellow-700 text-sm">{{ session('warning') }}</span>
                </div>
            </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8">

                    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Riwayat Input Absensi</h3>
                            <p class="text-gray-500 mt-1">Kelola dan pantau data kehadiran harian siswa.</p>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button onclick="openModal()" class="group relative inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                <svg class="w-5 h-5 mr-2 -ml-1 text-emerald-600 group-hover:text-emerald-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Lihat Rekap Bulanan
                            </button>

                            <a href="{{ route('absensi.create') }}" class="group relative inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-200 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-5 h-5 mr-2 -ml-1 text-indigo-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Input Absensi Baru
                            </a>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-xl p-5 mb-8 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                            <div class="md:col-span-4">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Cari</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Mapel atau Kelas..." class="pl-10 block w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all duration-200">
                                </div>
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Bulan</label>
                                <select id="filter-bulan" name="bulan" class="block w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all">
                                    <option value="">Semua Bulan</option>
                                    @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tahun</label>
                                <select id="filter-tahun" name="tahun" class="block w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-all">
                                    <option value="">Semua Tahun</option>
                                    @php $cy = date('Y'); @endphp
                                    @for($y = $cy; $y >= $cy-2; $y--)
                                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="md:col-span-2 flex gap-2">
                                <button type="button" id="btn-filter" class="flex-1 bg-gray-900 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-black transition-all shadow-md active:scale-95">
                                    Filter
                                </button>
                                <a href="{{ route('absensi.index') }}" class="flex-none bg-white border border-gray-300 text-gray-700 px-3 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 hover:text-red-600 transition-all" title="Reset Filter">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </a>
                                <div id="loading-indicator" class="hidden flex items-center justify-center p-2">
                                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden border border-gray-200 rounded-xl">
                        <div class="overflow-x-auto relative min-h-[300px]">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
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

    <div id="rekapModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-gray-100">

                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                        Lihat Rekap Bulanan
                    </h3>
                    <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1 rounded-md hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-6 py-6">
                    <form action="{{ route('absensi.laporan.view') }}" method="GET">
                        <div class="space-y-5">

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mata Pelajaran</label>
                                <select name="id_mapel" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach($mapels_list as $m)
                                    <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kelas</label>
                                <select name="id_kelas" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas_list as $k)
                                    <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bulan</label>
                                    <select name="bulan" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                                        @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tahun</label>
                                    <select name="tahun" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2.5">
                                        @php $cy = date('Y'); @endphp
                                        <option value="{{ $cy }}">{{ $cy }}</option>
                                        <option value="{{ $cy-1 }}">{{ $cy-1 }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <button type="button" onclick="closeModal()" class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                Tampilkan Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const filterBulan = document.getElementById('filter-bulan');
            const filterTahun = document.getElementById('filter-tahun');
            const btnFilter = document.getElementById('btn-filter');
            const tableBody = document.getElementById('absensi-table-body');
            const paginationWrapper = document.getElementById('pagination-wrapper');
            const loadingIndicator = document.getElementById('loading-indicator');

            let debounceTimer;

            const skeletonRow = `
                <tr class="animate-pulse border-b border-gray-100">
                    <td class="px-6 py-4"><div class="h-4 bg-gray-100 rounded w-3/4"></div></td>
                    <td class="px-6 py-4"><div class="h-4 bg-gray-100 rounded w-1/2"></div></td>
                    <td class="px-6 py-4"><div class="h-4 bg-gray-100 rounded w-full"></div></td>
                    <td class="px-2 py-4"><div class="h-4 bg-gray-100 rounded w-6 mx-auto"></div></td>
                    <td class="px-2 py-4"><div class="h-4 bg-gray-100 rounded w-6 mx-auto"></div></td>
                    <td class="px-2 py-4"><div class="h-4 bg-gray-100 rounded w-6 mx-auto"></div></td>
                    <td class="px-2 py-4"><div class="h-4 bg-gray-100 rounded w-6 mx-auto"></div></td>
                    <td class="px-6 py-4"><div class="h-8 bg-gray-100 rounded w-20 mx-auto"></div></td>
                </tr>
            `.repeat(5);

            function fetchData() {
                tableBody.innerHTML = skeletonRow;
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
                    .catch(error => {
                        console.error('Error:', error);
                        tableBody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-red-500 font-medium">Gagal memuat data. Silakan coba lagi.</td></tr>';
                    })
                    .finally(() => {
                        loadingIndicator.classList.add('hidden');
                    });
            }

            // Live Search
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchData, 500);
            });

            // Manual Filter Button
            btnFilter.addEventListener('click', function(e) {
                e.preventDefault();
                fetchData();
            });
        });

        function openModal() {
            const modal = document.getElementById('rekapModal');
            modal.classList.remove('hidden');
            // Sedikit animasi fade-in bisa ditambahkan lewat CSS transition di class
        }

        function closeModal() {
            document.getElementById('rekapModal').classList.add('hidden');
        }
    </script>
</x-app-layout>