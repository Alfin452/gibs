<x-absen-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 leading-tight">
            {{ __('Daftar Kelas & Mapel') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-2">

            <div class="flex flex-col md:flex-row justify-between items-end md:items-center mb-8 gap-4">

                <div class="flex items-center gap-4 bg-white px-5 py-3 rounded-2xl shadow-sm border border-gray-100">
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Item</p>
                        <p class="text-xl font-bold text-gray-900 leading-none">{{ $daftar_kelas->total() }}</p>
                    </div>
                </div>

                <form id="search-form" action="{{ route('absensi.daftar-kelas') }}" method="GET" class="w-full md:w-80 relative group" onsubmit="return false;">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg id="search-loading" class="hidden animate-spin w-5 h-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg id="search-icon" class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text"
                        id="search-input"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari kelas atau mapel..."
                        class="pl-10 block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 transition-all hover:border-indigo-300"
                        autocomplete="off">
                </form>
            </div>

            <div id="results-container">
                @if($daftar_kelas->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($daftar_kelas as $item)
                    <div class="group bg-white rounded-2xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 relative overflow-hidden flex flex-col h-full">

                        <div class="absolute -top-6 -right-6 w-24 h-24 bg-gradient-to-br from-indigo-50 to-white rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>

                        <div class="relative z-10 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                                    <span class="text-lg font-bold">{{ substr($item->kelas->nama_kelas, 0, 2) }}</span>
                                </div>
                                <span class="bg-gray-50 text-gray-400 text-[10px] font-mono px-2 py-1 rounded-md border border-gray-100">
                                    #{{ $item->kelas->id_kelas }}
                                </span>
                            </div>

                            <h3 class="text-xl font-bold text-gray-900 mb-1 group-hover:text-indigo-600 transition-colors">
                                {{ $item->kelas->nama_kelas }}
                            </h3>

                            <p class="text-sm text-indigo-500 font-bold mb-4 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                {{ $item->mapel->nama_mapel }}
                            </p>

                            <div class="border-t border-gray-50 my-4"></div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold">{{ $item->kelas->siswa_count ?? 0 }}</span>
                                    <span class="text-xs text-gray-400">Siswa</span>
                                </div>

                                <a href="{{ route('absensi.show', ['id_kelas' => $item->id_kelas, 'id_mapel' => $item->id_mapel]) }}"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-bold flex items-center gap-1 group/link px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-all">
                                    Detail
                                    <svg class="w-4 h-4 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $daftar_kelas->links() }}
                </div>

                @else
                <div class="flex flex-col items-center justify-center py-16 bg-white rounded-3xl border border-dashed border-gray-300">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Tidak ada jadwal ditemukan</h3>
                    <p class="text-gray-500 text-sm mt-1">Coba kata kunci lain atau hubungi administrator.</p>
                </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const searchIcon = document.getElementById('search-icon');
            const searchLoading = document.getElementById('search-loading');
            const resultsContainer = document.getElementById('results-container');
            let debounceTimer;

            // Template Kartu Skeleton (Loading)
            // Kita buat 8 kartu dummy agar grid terlihat penuh
            const skeletonCard = `
                <div class="bg-white rounded-2xl p-6 border border-gray-100 animate-pulse relative h-full">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-gray-200 rounded-xl"></div>
                        <div class="w-10 h-5 bg-gray-100 rounded-md"></div>
                    </div>
                    <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-4 bg-gray-100 rounded w-1/2 mb-6"></div>
                    
                    <div class="border-t border-gray-50 my-4"></div>
                    
                    <div class="flex items-center justify-between mt-auto">
                        <div class="w-16 h-4 bg-gray-100 rounded"></div>
                        <div class="w-20 h-8 bg-gray-200 rounded-lg"></div>
                    </div>
                </div>
            `;

            // Grid Skeleton (Mengulang kartu skeleton 8 kali)
            const skeletonGrid = `
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    ${skeletonCard.repeat(8)}
                </div>
            `;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value;
                    const url = `{{ route('absensi.daftar-kelas') }}?search=${query}`;

                    // 1. UI Loading State
                    searchIcon.classList.add('hidden');
                    searchLoading.classList.remove('hidden');
                    resultsContainer.innerHTML = skeletonGrid; // Ganti konten dengan skeleton

                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {

                        // 2. Fetch Data
                        fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                // 3. Parse HTML dari Response
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newContent = doc.getElementById('results-container');

                                // 4. Update Konten
                                if (newContent) {
                                    resultsContainer.innerHTML = newContent.innerHTML;
                                } else {
                                    resultsContainer.innerHTML = '<div class="text-center py-10 text-gray-500">Gagal memuat data.</div>';
                                }

                                // 5. Update URL Browser
                                window.history.pushState({}, '', url);
                            })
                            .catch(err => {
                                console.error('Error:', err);
                                resultsContainer.innerHTML = '<div class="text-center py-10 text-red-500">Terjadi kesalahan koneksi.</div>';
                            })
                            .finally(() => {
                                // 6. Reset UI
                                searchIcon.classList.remove('hidden');
                                searchLoading.classList.add('hidden');
                            });

                    }, 600); // Debounce 600ms
                });
            }
        });
    </script>
</x-absen-layout>