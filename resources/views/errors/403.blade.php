<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .fade-in-up {
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }

        .delay-300 {
            animation-delay: 300ms;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .blob {
            animation: float 8s infinite alternate ease-in-out;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 0.2;
            }

            100% {
                transform: translate(20px, -20px) scale(1.1);
                opacity: 0.4;
            }
        }
    </style>
</head>

<body class="relative min-h-screen bg-slate-50 flex items-center justify-center overflow-hidden font-sans selection:bg-red-200 selection:text-red-900">

    <div class="absolute top-10 left-1/4 w-72 h-72 lg:w-96 lg:h-96 bg-red-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 blob"></div>
    <div class="absolute bottom-10 right-1/4 w-72 h-72 lg:w-96 lg:h-96 bg-orange-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 blob delay-200"></div>

    <div class="relative z-10 max-w-lg w-full px-6">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-white p-10 text-center fade-in-up">

            <div class="flex justify-center mb-8 fade-in-up delay-100">
                <div class="relative">
                    <div class="absolute inset-0 bg-red-100 rounded-full animate-ping opacity-75"></div>
                    <div class="relative bg-red-50 p-5 rounded-full ring-4 ring-white shadow-sm">
                        <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <h1 class="text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-orange-500 mb-2 fade-in-up delay-200 tracking-tighter">
                403
            </h1>
            <h2 class="text-2xl font-bold text-slate-800 mb-4 fade-in-up delay-200">
                Akses Ditolak
            </h2>
            <p class="text-slate-500 mb-8 leading-relaxed fade-in-up delay-300">
                {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Manipulasi URL atau upaya ilegal telah diblokir oleh sistem.' }}
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center fade-in-up delay-300">
                <button id="btnBack" class="group relative px-6 py-3 font-semibold text-slate-700 bg-slate-100 rounded-xl hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 transition-all duration-200 ease-in-out">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </span>
                </button>
                <a href="{{ route('dashboard') }}" class="group relative px-6 py-3 font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl hover:from-blue-700 hover:to-blue-800 shadow-lg shadow-blue-500/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 ease-in-out">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Ke Dashboard
                    </span>
                </a>
            </div>

            <div class="mt-8 text-sm text-slate-400 fade-in-up delay-300">
                Akan otomatis dialihkan dalam <span id="countdown" class="font-bold text-red-500">10</span> detik.
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // 1. Logika Tombol "Kembali"
            const btnBack = document.getElementById('btnBack');
            if (btnBack) {
                btnBack.addEventListener('click', (e) => {
                    e.preventDefault();
                    // Cek apakah ada history halaman sebelumnya
                    if (window.history.length > 1 && document.referrer !== "") {
                        window.history.back();
                    } else {
                        // Jika dibuka di tab baru, paksa ke dashboard
                        window.location.href = "{{ route('dashboard') }}";
                    }
                });
            }

            let timeLeft = 10; 
            const countdownElement = document.getElementById('countdown');
            const redirectUrl = "{{ route('dashboard') }}";

            const timer = setInterval(() => {
                timeLeft--;
                countdownElement.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    window.location.href = redirectUrl; // Redirect eksekusi
                }
            }, 1000);
        });
    </script>
</body>

</html>