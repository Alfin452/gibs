<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Input Kehadiran Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-1 w-full px-2 sm:px-6 lg:px-1">
        
        <form action="{{ route('absensi.cek') }}" method="POST">
            @csrf

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Parameter Absensi</h3>
                        <p class="text-sm text-gray-500">Filter jadwal berdasarkan Mapel & Kelas.</p>
                    </div>
                    <div id="status-indicator" class="hidden px-3 py-1 rounded bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wide">
                        Sedang Memuat...
                    </div>
                </div>

                <div class="p-6 lg:p-8">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Bulan</label>
                            <select id="bulan" class="block w-full pl-4 pr-10 py-3 text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm bg-gray-50 transition-colors cursor-pointer filter-trigger">
                                @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Tahun</label>
                            <select id="tahun" class="block w-full pl-4 pr-10 py-3 text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm bg-gray-50 transition-colors cursor-pointer filter-trigger">
                                @php $cy = date('Y'); @endphp
                                <option value="{{ $cy }}">{{ $cy }}</option>
                                <option value="{{ $cy-1 }}">{{ $cy-1 }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Mata Pelajaran</label>
                            <select name="id_mapel" id="id_mapel" required class="block w-full pl-4 pr-10 py-3 text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm bg-gray-50 transition-colors cursor-pointer filter-trigger">
                                <option value="">-- Pilih Mapel --</option>
                                @foreach($mapels as $m)
                                <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="relative">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Kelas Target</label>
                            <select name="id_kelas" id="id_kelas" required class="block w-full pl-4 pr-10 py-3 text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm bg-gray-50 transition-colors cursor-pointer filter-trigger">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelas as $k)
                                <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <div class="bg-indigo-50 rounded-lg p-1 border border-indigo-100 flex flex-col md:flex-row">
                            
                            <div class="relative flex-grow">
                                <select name="tanggal" id="tanggal" required disabled class="block w-full pl-4 pr-10 py-4 text-base border-transparent bg-transparent focus:ring-0 text-gray-500 cursor-not-allowed font-medium">
                                    <option value="">-- Pilih Mapel & Kelas di atas dahulu --</option>
                                </select>
                            </div>

                            <div class="md:w-auto w-full p-1">
                                <button type="submit" id="btn-submit" disabled class="w-full h-full min-h-[50px] px-8 rounded-md bg-gray-300 text-white font-bold text-sm uppercase tracking-wider transition-all shadow-none cursor-not-allowed flex items-center justify-center gap-2">
                                    <span>Buka Absensi</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>

                        </div>
                        <p id="helper-text" class="text-xs text-gray-400 mt-2 ml-2">Jadwal akan muncul otomatis setelah filter dipilih.</p>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mapelSelect = document.getElementById('id_mapel');
            const kelasSelect = document.getElementById('id_kelas');
            const bulanSelect = document.getElementById('bulan');
            const tahunSelect = document.getElementById('tahun');
            const tanggalSelect = document.getElementById('tanggal');
            const btnSubmit = document.getElementById('btn-submit');
            
            const statusIndicator = document.getElementById('status-indicator');
            const helperText = document.getElementById('helper-text');

            async function fetchTanggal() {
                const mapel = mapelSelect.value;
                const kelas = kelasSelect.value;
                const bulan = bulanSelect.value;
                const tahun = tahunSelect.value;

                resetUI();

                if (!mapel || !kelas) return;

                setLoading(true);

                try {
                    const response = await fetch(`{{ route('absensi.get-tanggal') }}?id_mapel=${mapel}&id_kelas=${kelas}&bulan=${bulan}&tahun=${tahun}`);
                    const data = await response.json();

                    tanggalSelect.innerHTML = ''; 

                    if (data.length === 0) {
                        tanggalSelect.innerHTML = '<option value="">Tidak ada jadwal pertemuan ditemukan</option>';
                        updateStatus('Jadwal Kosong', 'red');
                    } else {
                        let count = 0;
                        tanggalSelect.innerHTML = '<option value="">-- Pilih Tanggal Tersedia --</option>';

                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.tanggal;
                            if (item.status === 'sudah') {
                                option.text = `${item.tampilan} *SELESAI`; 
                                option.disabled = true;
                                option.classList.add('bg-gray-100', 'text-gray-400');
                            } else {
                                option.text = `${item.tampilan}`; 
                                option.classList.add('font-bold', 'text-gray-900');
                                count++;
                            }
                            tanggalSelect.appendChild(option);
                        });

                        tanggalSelect.disabled = false;
                        tanggalSelect.classList.remove('text-gray-500', 'cursor-not-allowed');
                        tanggalSelect.classList.add('text-indigo-700', 'cursor-pointer', 'bg-white');
                        
                        updateStatus(`${count} Jadwal Ditemukan`, 'green');

                        tanggalSelect.addEventListener('change', function() {
                            if(this.value) {
                                enableSubmit();
                            } else {
                                disableSubmit();
                            }
                        });
                    }
                } catch (error) {
                    console.error(error);
                    tanggalSelect.innerHTML = '<option>Gagal memuat data</option>';
                    updateStatus('Error', 'red');
                } finally {
                    setLoading(false);
                }
            }

            function setLoading(isLoading) {
                if(isLoading) {
                    statusIndicator.classList.remove('hidden');
                    statusIndicator.textContent = 'Mencari...';
                    statusIndicator.className = 'px-3 py-1 rounded bg-yellow-100 text-yellow-700 text-xs font-bold uppercase tracking-wide animate-pulse';
                    tanggalSelect.innerHTML = '<option>Sedang mencari...</option>';
                }
            }

            function updateStatus(text, color) {
                statusIndicator.classList.remove('hidden', 'animate-pulse');
                statusIndicator.textContent = text;
                const colorClasses = color === 'red' 
                    ? 'bg-red-100 text-red-700' 
                    : 'bg-green-100 text-green-700';
                statusIndicator.className = `px-3 py-1 rounded text-xs font-bold uppercase tracking-wide ${colorClasses}`;
                
                if(color === 'green') {
                    helperText.textContent = 'Silakan pilih tanggal untuk melanjutkan.';
                    helperText.classList.add('text-indigo-500');
                } else {
                    helperText.textContent = 'Silakan ubah filter di atas.';
                    helperText.classList.remove('text-indigo-500');
                }
            }

            function resetUI() {
                tanggalSelect.innerHTML = '<option value="">-- Pilih Mapel & Kelas di atas dahulu --</option>';
                tanggalSelect.disabled = true;
                tanggalSelect.classList.add('text-gray-500', 'cursor-not-allowed');
                tanggalSelect.classList.remove('text-indigo-700', 'cursor-pointer', 'bg-white');
                disableSubmit();
                statusIndicator.classList.add('hidden');
                helperText.textContent = 'Jadwal akan muncul otomatis setelah filter dipilih.';
            }

            function enableSubmit() {
                btnSubmit.disabled = false;
                btnSubmit.classList.remove('bg-gray-300', 'cursor-not-allowed', 'shadow-none', 'text-gray-500');
                btnSubmit.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'shadow-md', 'text-white', 'hover:scale-[1.02]');
            }

            function disableSubmit() {
                btnSubmit.disabled = true;
                btnSubmit.classList.add('bg-gray-300', 'cursor-not-allowed', 'shadow-none', 'text-gray-500');
                btnSubmit.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'shadow-md', 'text-white', 'hover:scale-[1.02]');
            }

            const filters = document.querySelectorAll('.filter-trigger');
            filters.forEach(el => el.addEventListener('change', fetchTanggal));
        });
    </script>
</x-app-layout>