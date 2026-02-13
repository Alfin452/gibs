<x-app-layout>
    {{-- Set Locale ID untuk View ini --}}
    @php \Carbon\Carbon::setLocale('id'); @endphp

    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 leading-tight">
            {{ __('Dashboard Guru') }}
        </h2>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-2 space-y-8">

            <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold">Selamat Datang, {{ Auth::user()->guru->nama_guru ?? Auth::user()->name }}</h3>
                    <p class="text-indigo-100 mt-1 text-sm opacity-90">
                        Ini adalah ringkasan aktivitas mengajar Anda bulan <strong>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</strong>.
                    </p>
                </div>
                <div class="hidden md:block">
                    <span class="px-4 py-2 bg-white/20 rounded-lg text-sm font-semibold backdrop-blur-sm border border-white/10">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Siswa</p>
                            <h4 class="text-2xl font-bold text-gray-900">{{ $total_siswa ?? 0 }}</h4>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Kelas Diampu</p>
                            <h4 class="text-2xl font-bold text-gray-900">{{ $total_kelas ?? 0 }}</h4>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Mata Pelajaran</p>
                            <h4 class="text-2xl font-bold text-gray-900">{{ $total_mapel ?? 0 }}</h4>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden">
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Progress Absensi</p>
                            <h4 class="text-2xl font-bold text-indigo-600">{{ $progress ?? 0 }}%</h4>
                            <p class="text-[10px] text-gray-400 mt-1">Bulan {{ \Carbon\Carbon::now()->translatedFormat('F') }}</p>
                        </div>
                        <div class="relative w-12 h-12">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-gray-100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="4" />
                                <path class="{{ $progress >= 100 ? 'text-emerald-500' : ($progress >= 50 ? 'text-indigo-500' : 'text-red-500') }}"
                                    stroke-dasharray="{{ $progress }}, 100"
                                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none" stroke="currentColor" stroke-width="4" />
                            </svg>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 w-full h-1 bg-gray-100">
                        <div class="h-full {{ $progress >= 100 ? 'bg-emerald-500' : 'bg-indigo-500' }}" style="width: {{ $progress }}%"></div>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm h-full flex flex-col">
                        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    @if(count($tunggakan_absen) > 0)
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                    @else
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                    @endif
                                </span>
                                Pengingat Absensi
                            </h3>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-md {{ count($tunggakan_absen) > 0 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }}">
                                {{ count($tunggakan_absen) }} Belum Input
                            </span>
                        </div>

                        <div class="p-0 flex-1 overflow-y-auto max-h-[400px] custom-scrollbar">
                            @if(count($tunggakan_absen) > 0)
                            <table class="w-full text-sm text-left">
                                <thead class="text-xs text-gray-500 uppercase bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-6 py-3">Tanggal</th>
                                        <th class="px-6 py-3">Kelas</th>
                                        <th class="px-6 py-3">Mapel</th>
                                        <th class="px-6 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($tunggakan_absen as $t)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{-- Sudah diformat Indo di Controller --}}
                                            {{ $t['tanggal'] }}
                                            <span class="block text-xs text-gray-400 font-normal">{{ $t['hari'] }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded text-xs font-bold">{{ $t['kelas'] }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">{{ $t['mapel'] }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ $t['link'] }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-xs border border-indigo-200 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition-colors">
                                                Input Sekarang &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <div class="flex flex-col items-center justify-center h-full py-10 text-center">
                                <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <h4 class="text-gray-900 font-bold">Luar Biasa!</h4>
                                <p class="text-gray-500 text-sm mt-1">Semua jadwal bulan ini sudah Anda input.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 h-full">
                        <h3 class="font-bold text-gray-900 text-lg mb-4">Status Bulan Ini</h3>

                        <div class="space-y-6">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-500">Terisi</span>
                                    <span class="font-bold text-gray-900">{{ $sudah_absen ?? 0 }} / {{ $total_wajib_absen ?? 0 }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5">
                                    <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-1000" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-100">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Aksi Cepat</h4>
                                <div class="space-y-2">
                                    <a href="{{ route('absensi.create') }}" class="block w-full text-center py-2.5 rounded-xl bg-indigo-50 text-indigo-700 text-sm font-bold hover:bg-indigo-100 transition-colors">
                                        + Input Absensi
                                    </a>
                                    <a href="{{ route('absensi.index') }}" class="block w-full text-center py-2.5 rounded-xl border border-gray-200 text-gray-600 text-sm font-bold hover:bg-gray-50 transition-colors">
                                        Lihat Riwayat
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>