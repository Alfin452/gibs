<x-absen-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-900 leading-tight">
            {{ __('Input Kehadiran Baru') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">

    <style>
        /* --- Modern Flatpickr Overrides (Responsive Fix) --- */
        .flatpickr-calendar {
            background: #ffffff;
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0;
            font-family: inherit;
        }

        /* FIX: Paksa container internal agar mengikuti lebar induk */
        .flatpickr-rContainer {
            width: 100%;
        }

        .flatpickr-days {
            width: 100% !important;
            border: none !important;
        }

        .dayContainer {
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            justify-content: space-around;
        }

        .flatpickr-months {
            margin-bottom: 0.5rem;
            padding-top: 0.5rem;
            /* Pastikan navigasi bulan juga rata tengah/penuh */
            width: 100%;
        }

        .flatpickr-current-month {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            padding-top: 0;
            /* Rapikan padding atas */
            width: 75%;
            /* Beri ruang untuk panah navigasi */
            left: 12.5%;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 700;
        }

        .flatpickr-prev-month,
        .flatpickr-next-month {
            fill: #6366f1 !important;
            padding: 10px;
            top: 5px;
        }

        .flatpickr-prev-month:hover,
        .flatpickr-next-month:hover {
            background: #eef2ff;
        }

        span.flatpickr-weekday {
            color: #9ca3af;
            font-weight: 600;
            font-size: 0.85rem;
            /* Pastikan nama hari juga terdistribusi rata */
            flex: 1;
        }

        /* Style Tanggal */
        .flatpickr-day {
            border-radius: 0.5rem !important;
            border: 1px solid transparent;
            font-weight: 500;
            color: #d1d5db;
            /* FIX: Hapus max-width default library dan gunakan Flex basis */
            max-width: initial !important;
            flex-basis: 14.2857% !important;
            /* 100% dibagi 7 hari */
            height: 40px;
            line-height: 40px;
            margin-top: 2px;
            transition: all 0.2s ease;
        }

        /* Tanggal Aktif (Enabled) */
        .flatpickr-day:not(.flatpickr-disabled) {
            color: #374151;
            background: #f3f4f6;
            font-weight: 700;
        }

        .flatpickr-day:not(.flatpickr-disabled):hover {
            background: #e0e7ff;
            color: #4338ca;
        }

        /* Tanggal Terpilih */
        .flatpickr-day.selected,
        .flatpickr-day.selected:hover {
            background: #4f46e5 !important;
            color: #ffffff !important;
            border-color: #4f46e5 !important;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }

        .flatpickr-day.today {
            border-color: #e5e7eb;
        }
    </style>

    @php
    \Carbon\Carbon::setLocale('id');
    @endphp

    <div class="py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-2">
            <form action="{{ route('absensi.cek') }}" method="POST" id="form-absensi">
                @csrf
                <input type="hidden" name="id_mapel" id="id_mapel" required>
                <input type="hidden" name="id_kelas" id="id_kelas" required>
                <input type="hidden" name="kombinasi_jadwal" id="kombinasi_jadwal">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <div class="lg:col-span-2 space-y-6">

                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <span class="bg-indigo-100 text-indigo-600 w-8 h-8 flex items-center justify-center rounded-full text-sm">1</span>
                                Pilih Periode
                            </h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase">Bulan</label>
                                    <select id="bulan" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all filter-trigger cursor-pointer">
                                        @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase">Tahun</label>
                                    <select id="tahun" class="mt-1 block w-full rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all filter-trigger cursor-pointer">
                                        @php $cy = date('Y'); @endphp
                                        <option value="{{ $cy }}">{{ $cy }}</option>
                                        <option value="{{ $cy-1 }}">{{ $cy-1 }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 px-1">
                                <span class="bg-indigo-100 text-indigo-600 w-8 h-8 flex items-center justify-center rounded-full text-sm">2</span>
                                Pilih Kelas & Mapel
                            </h3>

                            @if(isset($kelompok_jadwal) && count($kelompok_jadwal) > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($kelompok_jadwal as $group)
                                @php
                                $jadwal_utama = $group->first();
                                @endphp

                                <div
                                    class="jadwal-card group cursor-pointer bg-white border border-gray-200 hover:border-indigo-500 hover:shadow-md hover:ring-2 hover:ring-indigo-500/20 rounded-xl p-5 transition-all duration-200 relative overflow-hidden"
                                    onclick="pilihJadwal(this, '{{ $jadwal_utama->id_mapel }}', '{{ $jadwal_utama->id_kelas }}', '{{ $jadwal_utama->mapel->nama_mapel }}', '{{ $jadwal_utama->kelas->nama_kelas }}')">
                                    <div class="absolute -right-6 -top-6 opacity-5 group-hover:opacity-10 transition-opacity rotate-12 pointer-events-none">
                                        <svg class="w-32 h-32 text-indigo-900" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 2H5c-1.103 0-2 .897-2 2v16c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2zM5 20V4h14l.002 16H5z"></path>
                                            <path d="M7 6h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2zm-8 4h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2zm-8 4h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2z"></path>
                                        </svg>
                                    </div>

                                    <div class="relative z-10">
                                        <div class="mb-3">
                                            <p class="text-xs font-bold text-indigo-600 uppercase tracking-wide mb-1">
                                                {{ $jadwal_utama->kelas->nama_kelas }}
                                            </p>
                                            <h4 class="text-lg font-bold text-gray-900 group-hover:text-indigo-700 transition-colors leading-snug">
                                                {{ $jadwal_utama->mapel->nama_mapel }}
                                            </h4>
                                        </div>

                                        <div class="h-px bg-gray-100 w-full mb-3"></div>

                                        <div class="space-y-2">
                                            @foreach($group as $slot)
                                            <div class="flex items-start gap-2 text-xs text-gray-500 group-hover:text-gray-600">
                                                <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>

                                                <div>
                                                    <span class="font-bold text-gray-700">{{ $slot->hari }}</span>
                                                    <span class="text-gray-400 mx-1">•</span>
                                                    <span class="font-mono">
                                                        {{ \Carbon\Carbon::parse($slot->jam_mulai)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($slot->jam_selesai)->format('H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="checkmark hidden absolute top-4 right-4 bg-indigo-600 text-white rounded-full p-1 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-xl">
                                <p class="text-sm text-yellow-700">Anda belum memiliki jadwal mengajar.</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="sticky top-6 space-y-6">

                            <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden relative">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                                    <h3 class="text-white font-bold text-lg flex items-center gap-2">
                                        <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Pilih Tanggal
                                    </h3>
                                    <p class="text-indigo-100 text-xs mt-1">Hanya tanggal jadwal aktif yang bisa dipilih.</p>
                                </div>

                                <div class="p-4 relative min-h-[340px]">
                                    <div id="loading-indicator" class="hidden absolute inset-0 z-20 bg-white/90 backdrop-blur-[1px] flex flex-col items-center justify-center transition-all duration-300">
                                        <div class="w-10 h-10 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin mb-3"></div>
                                        <span class="text-xs font-bold text-gray-500 animate-pulse">Mengecek Jadwal...</span>
                                    </div>

                                    <input type="text" name="tanggal" id="tanggal" class="hidden">
                                </div>

                                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-gray-200 border border-gray-300"></span>
                                        <span>Kosong</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 shadow-sm"></span>
                                        <span class="font-bold text-gray-700">Jadwal Ada</span>
                                    </div>
                                </div>
                            </div>

                            <div id="calendar-status" class="text-center text-xs h-4"></div>

                            <div id="selection-preview" class="hidden transition-all duration-500 transform translate-y-4 opacity-0">
                                <div class="bg-white rounded-2xl p-5 border border-indigo-100 shadow-sm relative overflow-hidden">
                                    <div class="absolute top-0 right-0 w-16 h-16 bg-indigo-50 rounded-bl-full -mr-4 -mt-4 z-0"></div>
                                    <div class="relative z-10">
                                        <p class="text-xs text-indigo-500 font-bold uppercase tracking-wider mb-2">Konfirmasi</p>

                                        <div class="space-y-1 mb-4">
                                            <div class="flex justify-between items-end border-b border-gray-100 pb-2">
                                                <span class="text-gray-500 text-sm">Kelas</span>
                                                <span class="font-bold text-gray-900" id="preview-kelas">-</span>
                                            </div>
                                            <div class="flex justify-between items-end border-b border-gray-100 pb-2">
                                                <span class="text-gray-500 text-sm">Mapel</span>
                                                <span class="font-bold text-gray-900 text-right w-2/3 truncate" id="preview-mapel">-</span>
                                            </div>
                                            <div class="flex justify-between items-end pt-2">
                                                <span class="text-gray-500 text-sm">Tanggal</span>
                                                <span class="font-bold text-indigo-600" id="preview-tanggal">-</span>
                                            </div>
                                        </div>

                                        <button type="submit" id="btn-submit"
                                            class="w-full group relative flex items-center justify-center px-6 py-3.5 border border-transparent text-sm font-bold rounded-xl text-white bg-gray-900 hover:bg-indigo-600 transition-all shadow-lg hover:shadow-indigo-500/30">
                                            <span>Buka Absensi</span>
                                            <svg class="ml-2 w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="empty-state-action" class="text-center py-8">
                                <p class="text-sm text-gray-400">Silakan pilih kelas terlebih dahulu.</p>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="warning-modal" class="relative z-[9999] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">

                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full sm:my-8 sm:max-w-3xl border border-gray-100">

                    <div class="bg-white px-6 py-8 sm:p-10">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-6">

                            <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-amber-50 sm:mx-0 ring-8 ring-amber-50/50">
                                <svg class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>

                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-2xl font-bold leading-6 text-gray-900 mb-4" id="modal-title">
                                    Akses Belum Tersedia
                                </h3>
                                <div class="mt-2">
                                    <p class="text-base text-gray-600 leading-relaxed" id="warning-message-content">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse sm:px-10 border-t border-gray-100">
                        <button type="button" onclick="closeWarningModal()"
                            class="inline-flex w-full justify-center rounded-xl bg-gray-900 px-6 py-3 text-base font-bold text-white shadow-lg shadow-gray-200 hover:bg-black hover:-translate-y-0.5 transition-all sm:w-auto">
                            Saya Mengerti
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script>
        let fpInstance;
        const serverDateToday = "{{ \Carbon\Carbon::now()->format('Y-m-d') }}";

        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. PINDAHKAN MODAL KE BODY AGAR FULL SCREEN & DI ATAS SIDEBAR ---
            const modalEl = document.getElementById('warning-modal');
            if (modalEl) {
                document.body.appendChild(modalEl);
            }
            // ---------------------------------------------------------------------

            const tanggalInput = document.getElementById('tanggal');

            fpInstance = flatpickr(tanggalInput, {
                locale: 'id',
                dateFormat: "Y-m-d",
                inline: true,
                animate: true,
                onChange: function(selectedDates, dateStr) {
                    // Cek Tanggal Masa Depan
                    if (dateStr > serverDateToday) {
                        showWarningModal(dateStr);

                        fpInstance.clear();
                        document.getElementById('selection-preview').classList.add('hidden');
                        return;
                    }

                    if (dateStr) {
                        showConfirmationPanel(selectedDates[0]);
                    }
                }
            });

            // Event Listener Filter
            document.querySelectorAll('.filter-trigger').forEach(el => {
                el.addEventListener('change', function() {
                    const bulan = document.getElementById('bulan').value;
                    const tahun = document.getElementById('tahun').value;

                    fpInstance.jumpToDate(`${tahun}-${bulan}-01`);

                    if (document.getElementById('id_mapel').value) {
                        fetchTanggalAvailable();
                    }
                });
            });
        });

        // --- FUNGSI MODAL ---
        function showWarningModal(dateStr) {
            const modal = document.getElementById('warning-modal');
            const msgContainer = document.getElementById('warning-message-content');

            const dateObj = new Date(dateStr);
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            const formattedDate = dateObj.toLocaleDateString('id-ID', options);

            msgContainer.innerHTML = `
            Maaf, jadwal untuk tanggal <span class="font-bold text-amber-600">${formattedDate}</span> belum bisa diisi saat ini.<br><br>
            Sistem mencatat tanggal server hari ini adalah <span class="font-bold text-gray-800">${serverDateToday}</span>. Anda hanya dapat mengisi absensi untuk hari ini atau hari sebelumnya.
        `;

            modal.classList.remove('hidden');

            // Matikan scroll pada body saat modal muncul
            document.body.style.overflow = 'hidden';
        }

        function closeWarningModal() {
            const modal = document.getElementById('warning-modal');
            modal.classList.add('hidden');

            // Hidupkan kembali scroll body
            document.body.style.overflow = 'auto';
        }
        // -------------------------

        function pilihJadwal(el, idMapel, idKelas, namaMapel, namaKelas) {
            // ... (kode pilihJadwal sama seperti sebelumnya) ...
            document.querySelectorAll('.jadwal-card').forEach(card => {
                card.classList.remove('ring-2', 'ring-indigo-600', 'bg-indigo-50', 'border-indigo-600');
                card.querySelector('.checkmark').classList.add('hidden');
            });
            el.classList.add('ring-2', 'ring-indigo-600', 'bg-indigo-50', 'border-indigo-600');
            el.querySelector('.checkmark').classList.remove('hidden');

            document.getElementById('id_mapel').value = idMapel;
            document.getElementById('id_kelas').value = idKelas;
            document.getElementById('kombinasi_jadwal').value = `${idKelas}-${idMapel}`;

            document.getElementById('preview-mapel').innerText = namaMapel;
            document.getElementById('preview-kelas').innerText = namaKelas;

            document.getElementById('selection-preview').classList.add('hidden');
            document.getElementById('empty-state-action').classList.remove('hidden');

            fetchTanggalAvailable();
        }

        async function fetchTanggalAvailable() {
            // ... (kode fetch sama seperti sebelumnya) ...
            const mapel = document.getElementById('id_mapel').value;
            const kelas = document.getElementById('id_kelas').value;
            const bulan = document.getElementById('bulan').value;
            const tahun = document.getElementById('tahun').value;
            const loading = document.getElementById('loading-indicator');
            const statusText = document.getElementById('calendar-status');

            if (!mapel || !kelas) return;

            loading.classList.remove('hidden');
            statusText.innerText = "";
            document.getElementById('tanggal').disabled = true;

            try {
                const url = `{{ route('absensi.get-tanggal') }}?id_mapel=${mapel}&id_kelas=${kelas}&bulan=${bulan}&tahun=${tahun}`;
                const response = await fetch(url);
                if (!response.ok) throw new Error("Gagal mengambil data");

                const data = await response.json();
                const validDates = data.map(item => item.tanggal);

                fpInstance.clear();
                fpInstance.jumpToDate(`${tahun}-${bulan}-01`);

                if (validDates.length > 0) {
                    fpInstance.set('enable', validDates);
                    statusText.innerHTML = `<span class="text-emerald-600 font-bold flex items-center justify-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Tersedia ${validDates.length} jadwal pertemuan.</span>`;
                } else {
                    fpInstance.set('enable', []);
                    statusText.innerHTML = `<span class="text-amber-500 font-bold flex items-center justify-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Tidak ada jadwal ditemukan bulan ini.</span>`;
                }

            } catch (error) {
                console.error("Error:", error);
                statusText.innerHTML = `<span class="text-red-500 font-bold">Gagal memuat jadwal.</span>`;
                fpInstance.set('enable', []);
            } finally {
                loading.classList.add('hidden');
                document.getElementById('tanggal').disabled = false;
            }
        }

        function showConfirmationPanel(dateObj) {
            // ... (kode sama seperti sebelumnya) ...
            const panel = document.getElementById('selection-preview');
            const emptyState = document.getElementById('empty-state-action');

            emptyState.classList.add('hidden');
            panel.classList.remove('hidden');

            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('preview-tanggal').innerText = dateObj.toLocaleDateString('id-ID', options);

            void panel.offsetWidth;
            panel.classList.remove('translate-y-4', 'opacity-0');
        }
    </script>
</x-absen-layout>