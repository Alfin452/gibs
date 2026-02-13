<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Detail Kelas') }}
            </h2>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <a href="{{ route('absensi.daftar-kelas') }}" class="text-gray-500 hover:text-indigo-600 transition-colors text-sm font-medium">
                            Daftar Kelas
                        </a>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ml-1 text-sm font-medium text-gray-800 md:ml-2">{{ $kelas->nama_kelas }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 relative">
                
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/30 rounded-t-3xl">
                    
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200 shrink-0">
                            <span class="text-xl font-bold">{{ substr($kelas->nama_kelas, 0, 2) }}</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 leading-tight">{{ $kelas->nama_kelas }}</h2>
                            <p class="text-sm text-indigo-600 font-semibold">{{ $mapel->nama_mapel }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                        
                        <form id="search-form" action="{{ route('absensi.show', ['id_kelas' => $kelas->id_kelas, 'id_mapel' => $mapel->id_mapel]) }}" method="GET" class="relative w-full sm:w-64">
                            <input type="text" 
                                id="search-input"
                                name="search" 
                                value="{{ request('search') }}" 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder-gray-400 shadow-sm"
                                placeholder="Cari siswa..."
                                autocomplete="off"
                                @if(request('search')) autofocus onfocus="var val=this.value; this.value=''; this.value= val;" @endif
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg id="search-loading" class="hidden animate-spin w-5 h-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <svg id="search-icon" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </form>

                        <a href="{{ route('absensi.detail', ['id_kelas' => $kelas->id_kelas, 'id_mapel' => $mapel->id_mapel]) }}" 
                           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-indigo-600 transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5 w-full sm:w-auto whitespace-nowrap">
                           <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                           <span>Matriks Bulanan</span>
                        </a>

                    </div>
                </div>

                <div class="overflow-x-auto rounded-b-3xl">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 font-bold w-16 text-center">No</th>
                                <th class="px-6 py-4 font-bold">Nama Siswa</th>
                                <th class="px-6 py-4 font-bold text-center">Hadir</th>
                                <th class="px-6 py-4 font-bold text-center">Sakit</th>
                                <th class="px-6 py-4 font-bold text-center">Izin</th>
                                <th class="px-6 py-4 font-bold text-center">Alpha</th>
                                <th class="px-6 py-4 font-bold text-center">Ket.</th> 
                                <th class="px-6 py-4 font-bold w-48 text-center">Kehadiran (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse($siswa as $index => $s)
                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                <td class="px-6 py-4 text-center text-gray-400 font-medium group-hover:text-indigo-500">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-white border border-indigo-50 flex items-center justify-center text-indigo-700 font-bold text-sm shadow-sm group-hover:scale-110 transition-transform">
                                            {{ substr($s->nama_siswa, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 group-hover:text-indigo-700 transition-colors">{{ $s->nama_siswa }}</div>
                                            <div class="text-xs text-gray-400 font-mono">{{ $s->nisn ?? 'NISN -' }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-100 min-w-[32px]">{{ $s->total_hadir }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="{{ $s->total_sakit > 0 ? 'bg-blue-50 text-blue-700 border-blue-100' : 'text-gray-300 bg-transparent border-transparent' }} inline-flex items-center justify-center px-2.5 py-1 rounded-lg font-bold text-xs border min-w-[32px]">
                                        {{ $s->total_sakit > 0 ? $s->total_sakit : '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="{{ $s->total_izin > 0 ? 'bg-amber-50 text-amber-700 border-amber-100' : 'text-gray-300 bg-transparent border-transparent' }} inline-flex items-center justify-center px-2.5 py-1 rounded-lg font-bold text-xs border min-w-[32px]">
                                        {{ $s->total_izin > 0 ? $s->total_izin : '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="{{ $s->total_alpha > 0 ? 'bg-rose-50 text-rose-700 border-rose-100' : 'text-gray-300 bg-transparent border-transparent' }} inline-flex items-center justify-center px-2.5 py-1 rounded-lg font-bold text-xs border min-w-[32px]">
                                        {{ $s->total_alpha > 0 ? $s->total_alpha : '-' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if(count($s->list_keterangan) > 0)
                                        <button onclick="openNoteModal('{{ $s->nama_siswa }}', {{ json_encode($s->list_keterangan) }})"
                                                class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 bg-indigo-50 border border-indigo-100 rounded-lg hover:bg-indigo-600 hover:text-white hover:shadow-md transition-all duration-200" title="Lihat Catatan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                    @else
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex justify-between items-end">
                                            <span class="text-xs font-bold {{ $s->persentase < 75 ? 'text-rose-600' : 'text-gray-700' }}">
                                                {{ $s->persentase }}%
                                            </span>
                                        </div>
                                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full {{ $s->persentase >= 90 ? 'bg-emerald-500' : ($s->persentase >= 75 ? 'bg-indigo-500' : 'bg-rose-500') }}" 
                                                 style="width: {{ $s->persentase }}%"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-400">
                                    Tidak ada data siswa ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="noteModal" class="relative z-[9999] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <div id="noteModalBackdrop" class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm transition-opacity opacity-0 z-[9998]"></div>

        <div class="fixed inset-0 z-[9999] w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                
                <div id="noteModalPanel" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 duration-300 ease-out border border-gray-100">
                    
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex justify-between items-center shadow-md">
                        <div class="text-white">
                            <h3 class="text-lg font-bold tracking-tight">Catatan Siswa</h3>
                            <p class="text-indigo-100 text-xs mt-0.5 font-medium opacity-90" id="modal-student-name">Nama Siswa</p>
                        </div>
                        <button type="button" onclick="closeNoteModal()" class="text-indigo-100 hover:text-white hover:bg-white/10 rounded-lg p-1.5 transition-colors focus:outline-none focus:ring-2 focus:ring-white/20">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="px-6 py-6 max-h-[60vh] overflow-y-auto custom-scrollbar bg-gray-50/50">
                        <div id="modal-content" class="space-y-3">
                            </div>
                    </div>

                    <div class="bg-white px-6 py-3 border-t border-gray-100 flex justify-end">
                        <button type="button" onclick="closeNoteModal()" class="inline-flex justify-center rounded-xl border border-gray-300 px-5 py-2.5 bg-white text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search Debounce
            const searchInput = document.getElementById('search-input');
            const searchForm = document.getElementById('search-form');
            const searchIcon = document.getElementById('search-icon');
            const searchLoading = document.getElementById('search-loading');
            let debounceTimer;

            if(searchInput) {
                searchInput.addEventListener('input', function() {
                    searchIcon.classList.add('hidden');
                    searchLoading.classList.remove('hidden');
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        searchForm.submit();
                    }, 800);
                });
            }
        });

        // Modal Functions with Scroll Lock
        function openNoteModal(namaSiswa, dataNotes) {
            const modal = document.getElementById('noteModal');
            const backdrop = document.getElementById('noteModalBackdrop');
            const panel = document.getElementById('noteModalPanel');
            const contentDiv = document.getElementById('modal-content');
            
            // 1. Lock Scroll pada Body
            document.body.classList.add('overflow-hidden');

            // 2. Set Data
            document.getElementById('modal-student-name').innerText = namaSiswa;
            
            // 3. Generate HTML
            let html = '';
            if(dataNotes.length > 0) {
                dataNotes.forEach(note => {
                    const dateObj = new Date(note.tanggal);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    const dateStr = dateObj.toLocaleDateString('id-ID', options);

                    let statusBadge = '';
                    if(note.status === 'S') statusBadge = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">SAKIT</span>';
                    else if(note.status === 'I') statusBadge = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-700 border border-amber-200">IZIN</span>';
                    else if(note.status === 'A') statusBadge = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">ALPHA</span>';
                    else if(note.status === 'H') statusBadge = '<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">HADIR</span>';

                    html += `
                        <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow group">
                            <div class="flex justify-between items-start mb-2.5">
                                <span class="text-xs font-bold text-gray-500 flex items-center gap-1.5">
                                    <div class="p-1 bg-gray-100 rounded text-gray-400 group-hover:bg-indigo-50 group-hover:text-indigo-500 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    ${dateStr}
                                </span>
                                ${statusBadge}
                            </div>
                            <div class="text-sm text-gray-700 leading-relaxed bg-gray-50/50 p-3 rounded-lg border border-gray-100/50">
                                "${note.keterangan}"
                            </div>
                        </div>
                    `;
                });
            } else {
                html = `
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-gray-500 text-sm">Tidak ada catatan untuk siswa ini.</p>
                    </div>`;
            }

            contentDiv.innerHTML = html;

            // 4. Show & Animate
            modal.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
                panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
            }, 10);
        }

        function closeNoteModal() {
            const modal = document.getElementById('noteModal');
            const backdrop = document.getElementById('noteModalBackdrop');
            const panel = document.getElementById('noteModalPanel');

            // 1. Unlock Scroll
            document.body.classList.remove('overflow-hidden');

            // 2. Reverse Animation
            backdrop.classList.add('opacity-0');
            panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
            panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');

            // 3. Hide after animation
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</x-app-layout>