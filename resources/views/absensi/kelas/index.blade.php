<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Kelas') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between items-end md:items-center mb-8 gap-4">

                <div class="flex items-center gap-4 bg-white px-5 py-3 rounded-2xl shadow-sm border border-gray-100">
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Kelas</p>
                        <p class="text-xl font-bold text-gray-900 leading-none">{{ $kelas->total() }}</p>
                    </div>
                </div>

                <form action="{{ route('absensi.daftar-kelas') }}" method="GET" class="w-full md:w-80 relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kelas..." class="pl-10 block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 transition-all hover:border-indigo-300">
                </form>
            </div>

            @if($kelas->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($kelas as $k)
                <div class="group bg-white rounded-2xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 relative overflow-hidden">

                    <div class="absolute -top-6 -right-6 w-24 h-24 bg-gradient-to-br from-indigo-50 to-white rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>

                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                                <span class="text-lg font-bold">{{ substr($k->nama_kelas, 0, 2) }}</span>
                            </div>
                            <span class="bg-gray-50 text-gray-400 text-[10px] font-mono px-2 py-1 rounded-md border border-gray-100">
                                #{{ $k->id_kelas }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 mb-1 group-hover:text-indigo-600 transition-colors">
                            {{ $k->nama_kelas }}
                        </h3>
                        <p class="text-xs text-gray-500 font-medium mb-6">Tahun Ajaran Aktif</p>

                        <div class="flex items-center justify-between border-t border-gray-50 pt-4 mt-2">
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <span class="text-sm font-semibold">{{ $k->siswa_count ?? 0 }}</span>
                                <span class="text-xs text-gray-400">Siswa</span>
                            </div>

                            <a href="#" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold flex items-center gap-1 group/link">
                                Detail
                                <svg class="w-3 h-3 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $kelas->links() }}
            </div>

            @else
            <div class="flex flex-col items-center justify-center py-16 bg-white rounded-3xl border border-dashed border-gray-300">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Tidak ada kelas ditemukan</h3>
                <p class="text-gray-500 text-sm mt-1">Coba kata kunci lain atau belum ada data kelas.</p>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>