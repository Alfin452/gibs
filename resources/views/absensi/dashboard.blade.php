<x-absen-layout>
    {{-- Set Locale ID untuk View ini --}}
    @php \Carbon\Carbon::setLocale('id'); @endphp

    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight">
                    {{ __('Overview Presensi') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Pantau dan lengkapi jadwal kehadiran kelas Anda bulan ini.</p>
            </div>

        </div>
    </x-slot>

    <div class="space-y-6 fade-in pb-8">

        <div class="grid grid-cols-1 md:grid-cols-2 {{ isset($kelas_hrt) ? 'xl:grid-cols-5' : 'xl:grid-cols-4' }} gap-6">

            <div class="bg-gradient-to-br from-primary-800 to-primary-950 rounded-3xl p-6 shadow-xl shadow-primary-900/10 text-white relative overflow-hidden group transition-transform duration-300 hover:-translate-y-1">
                <div class="absolute -right-6 -top-6 bg-secondary-500/20 rounded-full w-32 h-32 blur-2xl group-hover:bg-secondary-500/30 transition-all"></div>
                <div class="relative z-10 flex justify-between items-start">
                    <div>
                        <p class="text-primary-200 text-sm font-medium mb-1">Progress Bulan Ini</p>
                        <h3 class="text-5xl font-extrabold tracking-tighter text-white">{{ $progress ?? 0 }}<span class="text-2xl text-secondary-400">%</span></h3>
                    </div>
                    <div class="p-3 bg-primary-800/50 rounded-2xl backdrop-blur-sm border border-primary-700/50 text-secondary-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="relative z-10 mt-6 pt-4 border-t border-primary-700/50 flex items-center justify-between">
                    <span class="text-xs font-medium text-primary-200 flex items-center gap-1.5">
                        <div class="w-1.5 h-1.5 rounded-full bg-secondary-400"></div>
                        {{ $sudah_absen ?? 0 }} dari {{ $total_wajib_absen ?? 0 }} Terisi
                    </span>
                </div>
            </div>

            @if(isset($kelas_hrt))
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/60 hover:shadow-xl hover:shadow-secondary-500/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-secondary-50 rounded-2xl text-secondary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-secondary-700 bg-secondary-50 border border-secondary-100 px-2.5 py-1 rounded-full">Wali Kelas</span>
                </div>
                <h3 class="text-3xl font-bold text-slate-800 tracking-tight">{{ $jumlah_siswa_hrt }}</h3>
                <p class="text-sm font-medium text-slate-500 mt-1">Siswa Kls <span class="text-secondary-600 font-bold">{{ $kelas_hrt->nama_kelas }}</span></p>
            </div>
            @endif

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/60 hover:shadow-xl hover:shadow-primary-500/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-primary-50 rounded-2xl text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-slate-800 tracking-tight">{{ $total_siswa ?? 0 }}</h3>
                <p class="text-sm font-medium text-slate-500 mt-1">Total Siswa Diajar</p>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/60 hover:shadow-xl hover:shadow-emerald-500/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-emerald-50 rounded-2xl text-emerald-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-slate-800 tracking-tight">{{ $total_kelas ?? 0 }}</h3>
                <p class="text-sm font-medium text-slate-500 mt-1">Kelas Diampu</p>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/60 hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 hover:-translate-y-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-indigo-50 rounded-2xl text-indigo-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-slate-800 tracking-tight">{{ $total_mapel ?? 0 }}</h3>
                <p class="text-sm font-medium text-slate-500 mt-1">Mata Pelajaran</p>
            </div>

        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 pb-4">

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden xl:col-span-2 flex flex-col">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl {{ count($tunggakan_absen) > 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-extrabold text-slate-800 text-lg">Pengingat Absensi</h3>
                            <p class="text-xs text-slate-500">Jadwal yang belum Anda isi bulan ini</p>
                        </div>
                    </div>
                    @if(count($tunggakan_absen) > 0)
                    <span class="bg-rose-50 text-rose-600 text-sm px-4 py-1.5 rounded-full font-bold border border-rose-100 shadow-sm animate-pulse">{{ count($tunggakan_absen) }} Tunggakan</span>
                    @else
                    <span class="bg-emerald-50 text-emerald-600 text-sm px-4 py-1.5 rounded-full font-bold border border-emerald-100 shadow-sm">Tuntas</span>
                    @endif
                </div>

                <div class="overflow-x-auto overflow-y-auto max-h-[450px] flex-1 p-2 custom-scrollbar">
                    <table class="w-full text-sm text-left border-collapse">
                        <tbody>
                            @forelse($tunggakan_absen as $t)
                            <tr class="group hover:bg-slate-50/80 transition-colors duration-200 border-b border-dashed border-slate-200 last:border-0">
                                <td class="p-4 rounded-l-2xl align-middle">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-2xl bg-slate-100 flex flex-col items-center justify-center text-slate-700 border border-slate-200 group-hover:border-primary-300 group-hover:bg-white transition-colors">
                                            <span class="text-[10px] font-bold text-slate-500 uppercase">{{ explode(' ', $t['hari'])[0] }}</span>
                                            <span class="text-lg font-extrabold leading-none">{{ explode(' ', $t['tanggal'])[0] }}</span>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-base">{{ $t['mapel'] }}</div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-xs font-semibold bg-primary-50 text-primary-700 px-2.5 py-0.5 rounded-md border border-primary-100">{{ $t['kelas'] }}</span>
                                                <span class="text-xs font-medium text-slate-500">{{ $t['tanggal'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 rounded-r-2xl align-middle text-right">
                                    <a href="{{ $t['link'] }}" class="inline-flex items-center justify-center gap-1.5 bg-secondary-400 hover:bg-secondary-500 text-slate-900 text-xs font-bold px-5 py-2.5 rounded-xl transition-all shadow-sm focus:ring-2 focus:ring-secondary-400 focus:ring-offset-1 hover:-translate-y-0.5 hover:shadow-md">
                                        Input Sekarang
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="p-16 text-center">
                                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-50 text-emerald-500 mb-4 shadow-inner">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <p class="text-slate-800 font-extrabold text-xl">Luar Biasa!</p>
                                    <p class="text-slate-500 text-sm mt-2">Semua jadwal bulan ini telah diselesaikan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 flex flex-col h-full">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-extrabold text-slate-800 text-lg">Akses Cepat</h3>
                    <div class="p-2 bg-slate-50 rounded-xl text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>

                <div class="p-6 flex-1 flex flex-col justify-between">

                    <div class="space-y-4 mb-8">
                        <a href="{{ route('absensi.create') }}" class="block p-4 rounded-2xl border border-primary-100 bg-primary-50/50 hover:bg-primary-50 hover:border-primary-200 transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-primary-600 text-white flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-primary-900">Input Kehadiran</h4>
                                    <p class="text-xs text-primary-600 mt-0.5">Catat absen harian kelas</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('absensi.index') }}" class="block p-4 rounded-2xl border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800">Rekap Riwayat</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Lihat data bulan-bulan lalu</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-center">
                        <p class="text-xs text-slate-500 font-medium">Bulan berjalan</p>
                        <h4 class="text-lg font-extrabold text-slate-800 mt-1">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</h4>
                    </div>

                </div>
            </div>

        </div>

    </div>
</x-absen-layout>