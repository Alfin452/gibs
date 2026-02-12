<aside class="w-64 bg-white border-r border-gray-100 min-h-screen fixed left-0 top-0 hidden md:block z-10">
    <div class="h-16 flex items-center justify-center border-b border-gray-100">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-600/20">
                G
            </div>
            <span class="text-lg font-bold text-gray-800 tracking-tight">PORTAL GIBS</span>
        </a>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto" style="height: calc(100vh - 8rem);">

        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 pl-3">
            Main Menu
        </div>

        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="{{ route('absensi.daftar-kelas') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-slate-500 hover:bg-slate-50 hover:text-indigo-600">
            <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span class="font-medium">Daftar Kelas</span>
        </a>

        <a href="{{ route('absensi.create') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('absensi.create') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('absensi.create') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span class="font-medium">Input Kehadiran</span>
        </a>

        <a href="{{ route('absensi.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('absensi.index') || request()->routeIs('absensi.laporan*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-500 hover:bg-slate-50 hover:text-indigo-600' }}">
            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('absensi.index') || request()->routeIs('absensi.laporan*') ? 'text-white' : 'text-slate-400 group-hover:text-indigo-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span class="font-medium">Rekap Kehadiran</span>
        </a>

    </nav>

    <div class="absolute bottom-0 left-0 w-full p-4 border-t border-gray-100 bg-white">
        <div class="flex items-center justify-between gap-2">

            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-9 h-9 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 truncate">
                        {{ Auth::user()->nama ?? Auth::user()->username ?? 'Pengguna' }}
                    </p>
                    <p class="text-xs text-gray-500 truncate capitalize">
                        {{ Auth::user()->role ?? 'Guru' }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200"
                    title="Keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>

        </div>
    </div>
</aside>