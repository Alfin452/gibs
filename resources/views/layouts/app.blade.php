<aside class="w-72 bg-gradient-to-b from-blue-900 to-blue-950 border-r border-blue-800 min-h-screen fixed left-0 top-0 hidden md:flex flex-col z-20 shadow-xl transition-all duration-300 text-white">

    {{-- HEADER / LOGO --}}
    <div class="relative w-12 h-12 flex items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-tr from-cyan-400 to-blue-600 rounded-xl blur opacity-40 group-hover:opacity-80 group-hover:blur-md transition-all duration-500"></div>
        <div class="relative w-full h-full bg-[#0B1120] border border-white/10 rounded-xl flex items-center justify-center overflow-hidden group-hover:scale-[1.02] transition-transform duration-300">
            <div class="absolute top-0 left-0 w-full h-1/2 bg-gradient-to-b from-white/10 to-transparent z-20 pointer-events-none"></div>

            <img src="{{ asset('images/logo-gibs.png') }}"
                alt="Logo GIBS"
                class="relative z-10 w-full h-full object-contain p-1.5 filter drop-shadow-md">

        </div>

        <div class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-[#0B1120] shadow-sm animate-pulse z-30"></div>
    </div>

    {{-- NAVIGATION MENU --}}
    <nav class="flex-1 px-4 py-8 space-y-1 overflow-y-auto custom-scrollbar">

        {{-- Label Menu --}}
        <div class="px-4 mb-4">
            <span class="text-[10px] font-bold text-blue-300/80 uppercase tracking-widest">Main Menu</span>
        </div>

        {{-- DASHBOARD --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white shadow-inner border border-white/10 font-semibold' : 'text-blue-100/70 hover:bg-white/5 hover:text-white' }}">
            @if(request()->routeIs('dashboard'))
            {{-- Indikator Aktif: Putih/Cyan agar menyala --}}
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-cyan-400 rounded-r-full shadow-[0_0_10px_rgba(34,211,238,0.5)]"></div>
            @endif
            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('dashboard') ? 'text-cyan-400' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="text-sm">Dashboard</span>
        </a>

        {{-- Label Menu --}}
        <div class="px-4 mt-8 mb-4">
            <span class="text-[10px] font-bold text-blue-300/80 uppercase tracking-widest">Akademik</span>
        </div>

        {{-- DAFTAR KELAS --}}
        <a href="{{ route('absensi.daftar-kelas') }}"
            class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden {{ request()->routeIs('absensi.daftar-kelas') ? 'bg-white/10 text-white shadow-inner border border-white/10 font-semibold' : 'text-blue-100/70 hover:bg-white/5 hover:text-white' }}">
            @if(request()->routeIs('absensi.daftar-kelas'))
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-cyan-400 rounded-r-full shadow-[0_0_10px_rgba(34,211,238,0.5)]"></div>
            @endif
            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('absensi.daftar-kelas') ? 'text-cyan-400' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span class="text-sm">Daftar Kelas</span>
        </a>

        {{-- INPUT KEHADIRAN --}}
        <a href="{{ route('absensi.create') }}"
            class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden {{ request()->routeIs('absensi.create') ? 'bg-white/10 text-white shadow-inner border border-white/10 font-semibold' : 'text-blue-100/70 hover:bg-white/5 hover:text-white' }}">
            @if(request()->routeIs('absensi.create'))
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-cyan-400 rounded-r-full shadow-[0_0_10px_rgba(34,211,238,0.5)]"></div>
            @endif
            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('absensi.create') ? 'text-cyan-400' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span class="text-sm">Input Kehadiran</span>
        </a>

        {{-- REKAP KEHADIRAN --}}
        <a href="{{ route('absensi.index') }}"
            class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden {{ request()->routeIs('absensi.index') || request()->routeIs('absensi.laporan*') ? 'bg-white/10 text-white shadow-inner border border-white/10 font-semibold' : 'text-blue-100/70 hover:bg-white/5 hover:text-white' }}">
            @if(request()->routeIs('absensi.index') || request()->routeIs('absensi.laporan*'))
            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-cyan-400 rounded-r-full shadow-[0_0_10px_rgba(34,211,238,0.5)]"></div>
            @endif
            <svg class="w-5 h-5 transition-colors {{ request()->routeIs('absensi.index') || request()->routeIs('absensi.laporan*') ? 'text-cyan-400' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span class="text-sm">Rekap Kehadiran</span>
        </a>

    </nav>

    {{-- FOOTER / USER PROFILE --}}
    <div class="p-4 border-t border-blue-800/50 bg-blue-950/20">
        <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-blue-900/50 border border-blue-800 group hover:border-blue-700 hover:shadow-lg hover:bg-blue-900 transition-all duration-300">

            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-full bg-blue-700 border-2 border-blue-600 flex items-center justify-center text-white shrink-0 shadow-sm">
                    <span class="font-bold text-sm">{{ substr(Auth::user()->nama ?? Auth::user()->username ?? 'U', 0, 1) }}</span>
                </div>
                <div class="flex flex-col min-w-0">
                    <p class="text-xs font-bold text-white truncate">
                        {{ Auth::user()->nama ?? Auth::user()->username ?? 'Pengguna' }}
                    </p>
                    <p class="text-[10px] text-blue-200 font-medium truncate capitalize">
                        {{ Auth::user()->role ?? 'Guru' }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="p-2 rounded-lg text-blue-300 hover:text-white hover:bg-red-500/80 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-400"
                    title="Keluar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </form>

        </div>
    </div>
</aside>