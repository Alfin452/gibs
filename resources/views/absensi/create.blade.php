<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Kehadiran Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
            <div class="p-8 bg-white border-b border-gray-100">

                <div class="mb-6 text-center">
                    <h3 class="text-lg font-bold text-gray-900">Form Absensi Pintar</h3>
                    <p class="text-sm text-gray-500">Pilih Mata Pelajaran & Kelas untuk melihat jadwal tersedia.</p>
                </div>

                <form action="{{ route('absensi.cek') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                            <select id="bulan" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm filter-trigger">
                                @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                            <select id="tahun" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm filter-trigger">
                                @php $cy = date('Y'); @endphp
                                <option value="{{ $cy }}">{{ $cy }}</option>
                                <option value="{{ $cy-1 }}">{{ $cy-1 }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                        <select name="id_mapel" id="id_mapel" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm filter-trigger">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($mapels as $m)
                            <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                        <select name="id_kelas" id="id_kelas" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm filter-trigger">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pertemuan (Sesuai Jadwal)</label>
                        <select name="tanggal" id="tanggal" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-gray-50">
                            <option value="">-- Silakan Pilih Mapel & Kelas Dahulu --</option>
                        </select>
                        <p id="loading-text" class="text-xs text-indigo-500 mt-2 hidden">Sedang mencari jadwal...</p>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('absensi.index') }}" class="text-gray-500 hover:text-gray-700 font-medium text-sm">Batal</a>
                        <button type="submit" id="btn-submit" disabled class="bg-gray-400 text-white font-bold py-2.5 px-6 rounded-lg shadow-lg transition-all cursor-not-allowed">
                            Buka Lembar Absensi →
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mapelSelect = document.getElementById('id_mapel');
            const kelasSelect = document.getElementById('id_kelas');
            const bulanSelect = document.getElementById('bulan');
            const tahunSelect = document.getElementById('tahun');
            const tanggalSelect = document.getElementById('tanggal');
            const loadingText = document.getElementById('loading-text');
            const btnSubmit = document.getElementById('btn-submit');

            // Fungsi ambil data
            async function fetchTanggal() {
                const mapel = mapelSelect.value;
                const kelas = kelasSelect.value;
                const bulan = bulanSelect.value;
                const tahun = tahunSelect.value;

                // Reset Dropdown
                tanggalSelect.innerHTML = '<option value="">-- Memuat Jadwal... --</option>';
                btnSubmit.classList.add('bg-gray-400', 'cursor-not-allowed');
                btnSubmit.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'transform', 'hover:scale-105');
                btnSubmit.disabled = true;

                if (!mapel || !kelas) {
                    tanggalSelect.innerHTML = '<option value="">-- Pilih Mapel & Kelas Dahulu --</option>';
                    return;
                }

                loadingText.classList.remove('hidden');

                try {
                    // Panggil API Laravel
                    const response = await fetch(`{{ route('absensi.get-tanggal') }}?id_mapel=${mapel}&id_kelas=${kelas}&bulan=${bulan}&tahun=${tahun}`);
                    const data = await response.json();

                    tanggalSelect.innerHTML = ''; // Kosongkan lagi

                    if (data.length === 0) {
                        tanggalSelect.innerHTML = '<option value="">Tidak ada jadwal di bulan ini</option>';
                    } else {
                        let adaYgBelum = false;
                        tanggalSelect.innerHTML = '<option value="">-- Pilih Tanggal Tersedia --</option>';

                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.tanggal;

                            if (item.status === 'sudah') {
                                option.text = `✅ ${item.tampilan} (Sudah Diabsen)`;
                                option.disabled = true; // Disable yg sudah diabsen biar ga dobel
                                option.classList.add('text-gray-400', 'bg-gray-100');
                            } else {
                                option.text = `📅 ${item.tampilan}`;
                                option.classList.add('font-bold', 'text-gray-800');
                                adaYgBelum = true;
                            }
                            tanggalSelect.appendChild(option);
                        });

                        // Aktifkan tombol submit hanya jika ada tanggal tersedia
                        if (adaYgBelum) {
                            btnSubmit.disabled = false;
                            btnSubmit.classList.remove('bg-gray-400', 'cursor-not-allowed');
                            btnSubmit.classList.add('bg-indigo-600', 'hover:bg-indigo-700', 'text-white', 'cursor-pointer');
                        }
                    }

                } catch (error) {
                    console.error('Error:', error);
                    tanggalSelect.innerHTML = '<option value="">Gagal memuat jadwal</option>';
                } finally {
                    loadingText.classList.add('hidden');
                }
            }

            // Pasang Listener di semua input filter
            const filters = document.querySelectorAll('.filter-trigger');
            filters.forEach(el => {
                el.addEventListener('change', fetchTanggal);
            });
        });
    </script>
</x-app-layout>