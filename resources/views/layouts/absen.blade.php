<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Portal GIBS') }} - Absensi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .swal2-popup {
            font-family: 'Inter', sans-serif !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #D1D5DB;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#F9FAFB] text-slate-900 selection:bg-blue-500 selection:text-white">

    <div class="min-h-screen flex flex-row relative">

        <div id="mobile-overlay" class="fixed inset-0 bg-gray-900/50 z-20 hidden md:hidden transition-opacity backdrop-blur-sm" onclick="toggleSidebar()"></div>

        <aside class="w-72 bg-gradient-to-b from-blue-900 to-blue-950 border-r border-blue-800 min-h-screen fixed left-0 top-0 hidden md:flex flex-col z-30 shadow-xl transition-all duration-300 text-white">

            {{-- HEADER / LOGO --}}
            <div class="h-20 flex items-center px-8 border-b border-white/10">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-blue-600 border border-white/20 text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform duration-200">
                        <span class="text-xl font-bold font-sans">G</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-extrabold text-white tracking-tight leading-none group-hover:text-cyan-300 transition-colors">PORTAL</span>
                        <span class="text-xs font-bold text-blue-300 tracking-[0.2em] leading-none mt-1">ABSENSI</span>
                    </div>
                </a>
            </div>

            {{-- NAVIGATION MENU --}}
            <nav class="flex-1 px-4 py-8 space-y-1 overflow-y-auto custom-scrollbar">

                <div class="px-4 mb-4">
                    <span class="text-[10px] font-bold text-blue-300/80 uppercase tracking-widest">Main Menu</span>
                </div>

                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 group relative overflow-hidden {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white shadow-inner border border-white/10 font-semibold' : 'text-blue-100/70 hover:bg-white/5 hover:text-white' }}">
                    @if(request()->routeIs('dashboard'))
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-cyan-400 rounded-r-full shadow-[0_0_10px_rgba(34,211,238,0.5)]"></div>
                    @endif
                    <svg class="w-5 h-5 transition-colors {{ request()->routeIs('dashboard') ? 'text-cyan-400' : 'text-blue-300 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span class="text-sm">Dashboard</span>
                </a>

                <div class="px-4 mt-8 mb-4">
                    <span class="text-[10px] font-bold text-blue-300/80 uppercase tracking-widest">Kelola Data</span>
                </div>

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

                    <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
                        @csrf
                        <button type="submit" class="p-2 rounded-lg text-blue-300 hover:text-white hover:bg-red-500/80 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-400" title="Keluar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-1 md:ml-72 min-h-screen flex flex-col transition-all duration-300 w-full">

            <div class="md:hidden h-16 bg-white/90 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-4 sticky top-0 z-30">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold shadow-md">G</div>
                    <span class="font-bold text-lg text-gray-900 tracking-tight">PORTAL ABSENSI</span>
                </div>
                <button onclick="toggleSidebar()" class="p-2 text-gray-500 hover:bg-gray-100 hover:text-blue-600 rounded-lg transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>

            @if (isset($header))
            <header class="bg-white border-b border-gray-100 sticky top-0 z-20 hidden md:block shadow-[0_4px_20px_-10px_rgba(0,0,0,0.05)]">
                <div class="max-w-[85rem] mx-auto py-5 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        {{ $header }}
                    </div>
                </div>
            </header>
            @endif

            <div class="flex-1 fade-in p-4 sm:p-6 lg:p-2">
                {{ $slot }}
            </div>

            <footer class="py-6 mt-auto border-t border-gray-100 bg-white md:bg-transparent">
                <div class="max-w-[85rem] mx-auto px-4 text-center">
                    <p class="text-xs font-medium text-gray-400">
                        &copy; {{ date('Y') }} <span class="text-blue-600 font-bold">Portal GIBS</span> - Modul Absensi.
                    </p>
                </div>
            </footer>

        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('aside');
            const overlay = document.getElementById('mobile-overlay');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                sidebar.classList.add('flex');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('hidden');
                sidebar.classList.remove('flex');
                overlay.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Notifications
            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                toast: true,
                position: 'top-end'
            });
            @endif
            @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#EF4444'
            });
            @endif
            @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: "{{ session('warning') }}",
                confirmButtonColor: '#F59E0B'
            });
            @endif
        });

        // Logout Confirmation
        document.addEventListener('submit', function(e) {
            if (e.target && e.target.action && e.target.action.includes('logout')) {
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Keluar',
                    text: "Apakah Anda yakin ingin keluar?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563EB', // Warna biru untuk tombol Confirm
                    cancelButtonColor: '#9CA3AF',
                    confirmButtonText: 'Ya, Keluar',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) e.target.submit();
                });
            }
        });
    </script>
</body>

</html>