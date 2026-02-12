<aside class="w-72 bg-white border-r border-gray-100 min-h-screen fixed left-0 top-0 hidden md:flex flex-col z-20 shadow-[4px_0_24px_rgba(0,0,0,0.02)] transition-all duration-300">

    <div class="h-20 flex items-center px-8 border-b border-gray-50">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200 group-hover:scale-105 transition-transform duration-200">
                <span class="text-xl font-bold font-sans">G</span>
                <div class="absolute inset-0 rounded-xl ring-1 ring-inset ring-black/10"></div>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-extrabold text-gray-900 tracking-tight leading-none group-hover:text-indigo-600 transition-colors">PORTAL</span>
                <span class="text-xs font-bold text-gray-400 tracking-[0.2em] leading-none mt-1">GIBS SYSTEM</span>
            </div>
        </a>
    </div>

    <nav class="flex-1 px-4 py-8 space-y-1 overflow-y-auto custom-scrollbar">

        <div class="px-4 mb-4">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Main Menu</span>
        </div>

        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
            @if(request()->routeIs('dashboard'))
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-600 rounded-r-full"></div>
            @endif
            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="text-sm">Dashboard</span>
        </a>

        <div class="px-4 mt-8 mb-4">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Akademik</span>
        </div>

        <a href="{{ route('absensi.daftar-kelas') }}"
            class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden {{ request()->routeIs('absensi.daftar-kelas') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
            @if(request()->routeIs('absensi.daftar-kelas'))
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-600 rounded-r-full"></div>
            @endif
            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('absensi.daftar-kelas') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span class="text-sm">Daftar Kelas</span>
        </a>

        <a href="{{ route('absensi.create') }}"
            class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden {{ request()->routeIs('absensi.create') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
            @if(request()->routeIs('absensi.create'))
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-600 rounded-r-full"></div>
            @endif
            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('absensi.create') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span class="text-sm">Input Kehadiran</span>
        </a>

        <a href="{{ route('absensi.index') }}"
            class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden {{ request()->routeIs('absensi.index') || request()->routeIs('absensi.laporan*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
            @if(request()->routeIs('absensi.index') || request()->routeIs('absensi.laporan*'))
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-indigo-600 rounded-r-full"></div>
            @endif
            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('absensi.index') || request()->routeIs('absensi.laporan*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span class="text-sm">Rekap Kehadiran</span>
        </a>

    </nav>

    <div class="p-4 border-t border-gray-100 bg-white">
        <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-gray-50 border border-gray-100 group hover:border-indigo-100 hover:shadow-sm transition-all duration-300">

            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-white border-2 border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0 shadow-sm">
                    <span class="font-bold text-sm">{{ substr(Auth::user()->nama ?? Auth::user()->username ?? 'U', 0, 1) }}</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <p class="text-xs font-bold text-gray-900 truncate">
                        {{ Auth::user()->nama ?? Auth::user()->username ?? 'Pengguna' }}
                    </p>
                    <p class="text-[10px] text-gray-500 font-medium truncate capitalize">
                        {{ Auth::user()->role ?? 'Guru' }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-100"
                    title="Keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>

        </div>
    </div>
</aside>