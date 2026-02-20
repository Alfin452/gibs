<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Portal GIBS') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
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

        /* Custom Font SweetAlert */
        .swal2-popup {
            font-family: 'Inter', sans-serif !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#F9FAFB] text-slate-900 selection:bg-indigo-500 selection:text-white">

    <div class="min-h-screen flex flex-row relative">

        <div id="mobile-overlay" class="fixed inset-0 bg-gray-900/50 z-20 hidden md:hidden transition-opacity backdrop-blur-sm" onclick="toggleSidebar()"></div>

        @include('layouts.sidebar')

        <main class="flex-1 md:ml-72 min-h-screen flex flex-col transition-all duration-300 w-full">

            <div class="md:hidden h-16 bg-white/90 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-4 sticky top-0 z-30">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-bold shadow-md">G</div>
                    <span class="font-bold text-lg text-gray-900 tracking-tight">PORTAL GIBS</span>
                </div>
                <button onclick="toggleSidebar()" class="p-2 text-gray-500 hover:bg-gray-100 hover:text-indigo-600 rounded-lg transition-colors focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>

            @if (isset($header))
            <header class="bg-white border-b border-gray-100 sticky top-0 z-20 hidden md:block">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        {{ $header }}
                    </div>
                </div>
            </header>
            @endif

            <div class="flex-1 p-4 sm:p-6 lg:p-8 fade-in">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </div>

            <footer class="py-6 mt-auto border-t border-gray-100 bg-white md:bg-transparent">
                <div class="max-w-7xl mx-auto px-4 text-center">
                    <p class="text-xs font-medium text-gray-400">
                        &copy; {{ date('Y') }} <span class="text-indigo-600 font-bold">Portal GIBS</span>. All rights reserved.
                    </p>
                </div>
            </footer>

        </main>
    </div>

    <script>
        // 1. Mobile Sidebar Logic
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

        // 2. SweetAlert2 Notifications (Flash Messages)
        document.addEventListener('DOMContentLoaded', function() {
            // Success Message
            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                toast: true,
                position: 'top-end',
                background: '#ffffff',
                iconColor: '#10B981'
            });
            @endif

            // Error Message
            @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#EF4444',
                confirmButtonText: 'Tutup'
            });
            @endif

            // Warning Message
            @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: "{{ session('warning') }}",
                confirmButtonColor: '#F59E0B',
                confirmButtonText: 'Mengerti'
            });
            @endif
        });

        // 3. Global Logout Confirmation
        document.addEventListener('submit', function(e) {
            if (e.target && e.target.action && e.target.action.includes('logout')) {
                e.preventDefault();

                Swal.fire({
                    title: 'Konfirmasi Keluar',
                    text: "Apakah Anda yakin ingin mengakhiri sesi ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4F46E5',
                    cancelButtonColor: '#9CA3AF',
                    confirmButtonText: 'Ya, Keluar',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        e.target.submit();
                    }
                });
            }
        });
    </script>

</body>

</html>