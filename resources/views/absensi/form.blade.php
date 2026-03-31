<x-absen-layout>
    @php
    \Carbon\Carbon::setLocale('id');
    @endphp
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                {{ __('Lembar Presensi Siswa') }}
            </h2>
            <div class="flex flex-wrap items-center gap-3 text-sm text-right">

                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 text-left">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Keterangan</p>
                    <div class="flex items-center gap-2 mt-0.5 text-xs font-bold">
                        <span class="text-green-600">H=Hadir</span>
                        <span class="text-blue-600">S=Sakit</span>
                        <span class="text-yellow-600">I=Izin</span>
                        <span class="text-red-600">A=Alpha</span>
                        <span class="text-gray-500">L=Libur</span>
                    </div>
                </div>

                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 text-left">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Kelas</p>
                    <p class="font-bold text-secondary-600">{{ $infoKelas->nama_kelas }}</p>
                </div>
                <div class="bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100 text-left">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Tanggal</p>
                    <p class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($tanggal)->isoFormat('dddd, D MMMM Y') }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-2 w-full px-4 sm:px-6 lg:px-2">

        <div class="bg-primary-600 rounded-t-xl p-6 flex flex-col md:flex-row justify-between items-center gap-4 shadow-lg">
            <div class="text-white">
                <p class="text-xs font-bold text-primary-200 uppercase tracking-wider mb-1">Mata Pelajaran</p>
                <h3 class="text-2xl font-bold">{{ $infoMapel->nama_mapel }}</h3>
            </div>

            <div class="flex gap-2 bg-primary-700/50 p-1.5 rounded-lg backdrop-blur-sm">
                <button type="button" onclick="setSemua('H')" class="bg-white text-green-700 px-4 py-2 rounded-md text-xs font-bold hover:bg-green-50 shadow-sm transition-all flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Set Semua Hadir
                </button>
                <button type="button" onclick="setSemua('L')" class="text-white px-4 py-2 rounded-md text-xs font-bold hover:bg-primary-600 transition-all border border-transparent hover:border-primary-400">
                    Set Hari Libur
                </button>
            </div>
        </div>

        <form action="{{ route('absensi.store') }}" method="POST" id="form-absensi">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <input type="hidden" name="id_kelas" value="{{ $infoKelas->id_kelas ?? '' }}">
            <input type="hidden" name="id_major" value="{{ $infoKelas->id_major ?? '' }}">
            <input type="hidden" name="id_mapel" value="{{ $infoMapel->id_mapel }}">

            <div class="bg-white shadow-xl rounded-b-xl border border-t-0 border-gray-200">

                <div class="w-full overflow-hidden rounded-b-xl">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-16 bg-gray-50">
                                    No
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50">
                                    Nama Siswa
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider w-80 bg-gray-50">
                                    Status Kehadiran
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider bg-gray-50">
                                    Keterangan (Opsional)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($siswa as $index => $s)

                            @php
                            // Cek apakah siswa ini terdaftar di data klinik hari ini dan statusnya MASIH SAKIT
                            $is_sakit_klinik = isset($siswa_masih_sakit) && $siswa_masih_sakit->has($s->id_siswa);

                            if (isset($dataKehadiran) && isset($dataKehadiran[$s->id_siswa])) {
                            // MODE EDIT
                            $statusDB = $dataKehadiran[$s->id_siswa]->status;
                            $ketDB = $dataKehadiran[$s->id_siswa]->keterangan;
                            } else {
                            // FORM BARU
                            if ($is_sakit_klinik) {
                            $statusDB = 'S'; // Otomatis diset Sakit
                            $ketDB = $siswa_masih_sakit[$s->id_siswa]->keterangan; // Keluhan otomatis
                            } else {
                            $statusDB = 'H'; // Default Hadir (Termasuk untuk yang sudah sembuh)
                            $ketDB = '';
                            }
                            }
                            @endphp

                            <tr class="hover:bg-primary-50/30 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500 font-medium align-middle">
                                    {{ $index + 1 }}
                                </td>

                                <td class="px-6 py-4 align-middle">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 group-hover:text-primary-700 transition-colors">
                                            {{ $s->nama_siswa }}
                                        </span>
                                        <span class="text-xs text-gray-400 font-mono mt-0.5">
                                            NIS: {{ $s->nis }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 align-middle">
                                    <div class="flex justify-center items-center gap-1 bg-gray-100 p-1.5 rounded-lg border border-gray-200 shadow-inner">

                                        @php
                                        $options = [
                                        'H' => ['bg' => 'peer-checked:bg-green-500', 'hover' => 'hover:text-green-600', 'label' => 'Hadir'],
                                        'S' => ['bg' => 'peer-checked:bg-blue-500', 'hover' => 'hover:text-blue-600', 'label' => 'Sakit'],
                                        'I' => ['bg' => 'peer-checked:bg-yellow-500', 'hover' => 'hover:text-yellow-600', 'label' => 'Izin'],
                                        'A' => ['bg' => 'peer-checked:bg-red-500', 'hover' => 'hover:text-red-600', 'label' => 'Alpha'],
                                        'L' => ['bg' => 'peer-checked:bg-gray-500', 'hover' => 'hover:text-gray-600', 'label' => 'Libur'],
                                        ];
                                        @endphp

                                        @foreach($options as $code => $style)
                                        <label class="cursor-pointer relative group/radio">
                                            <input type="radio" name="status[{{ $s->id_siswa }}]" value="{{ $code }}"
                                                class="peer sr-only" {{ (isset($statusDB) && $statusDB == $code) ? 'checked' : '' }}>

                                            <div class="w-10 h-8 flex items-center justify-center rounded text-xs font-bold text-gray-400 border border-transparent hover:bg-gray-50 {{ $style['hover'] }} {{ $style['bg'] }} peer-checked:text-white peer-checked:shadow-md transition-all duration-200">
                                                {{ $code }}
                                            </div>

                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/radio:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-20 shadow-sm">
                                                {{ $style['label'] }}
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </label>
                                        @endforeach

                                    </div>
                                </td>

                                <td class="px-6 py-4 align-middle">
                                    <input type="text" name="keterangan[{{ $s->id_siswa }}]" value="{{ $ketDB }}" placeholder="Tulis catatan..."
                                        class="w-full text-sm border-gray-200 bg-gray-50 focus:bg-white rounded-md focus:border-primary-500 focus:ring-primary-500 transition-all placeholder-gray-400">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-between items-center rounded-b-xl">
                    <p class="text-sm text-gray-500 italic hidden sm:block">
                        Pastikan semua data siswa telah dicek sebelum menyimpan.
                    </p>
                    <button type="submit" class="w-full sm:w-auto bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg shadow-primary-600/20 transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan Presensi
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function setSemua(status) {
            const radios = document.querySelectorAll(`input[value="${status}"]`);
            radios.forEach(radio => {
                radio.checked = true;
            });
        }

        // --- SCRIPT VALIDASI KONFIRMASI PENYIMPANAN ---
        document.addEventListener('DOMContentLoaded', function() {
            const formAbsensi = document.getElementById('form-absensi');

            if (formAbsensi) {
                formAbsensi.addEventListener('submit', function(e) {
                    e.preventDefault(); // Hentikan proses submit otomatis

                    Swal.fire({
                        title: 'Konfirmasi Simpan',
                        text: "Apakah Anda yakin data presensi siswa sudah benar?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3ab09e', // Warna Primary (Keppel)
                        cancelButtonColor: '#9CA3AF', // Warna Gray
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Periksa Kembali',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Tampilkan state loading saat form dikirim
                            Swal.fire({
                                title: 'Menyimpan Data...',
                                text: 'Mohon tunggu sebentar',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Submit form secara manual
                            formAbsensi.submit();
                        }
                    });
                });
            }
        });
    </script>

    @if(isset($siswa_masih_sakit) && $siswa_masih_sakit->count() > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let pesanHtml = "";
            let hasSakitInClass = false; // Flag untuk mengecek apakah ada siswa kelas ini yang sakit

            // Mulai membangun list
            let listSakit = "<ul class='list-disc pl-5 space-y-2 text-sm text-gray-700'>";

            @foreach($siswa_masih_sakit as $sakit)
            @php
            // Cari apakah siswa yang sakit ini ada dalam daftar $siswa di kelas ini
            $dataSiswa = $siswa -> firstWhere('id_siswa', $sakit -> id_siswa);
            @endphp
            @if($dataSiswa)
            hasSakitInClass = true;
            pesanHtml += "<li><b>{{ $dataSiswa->nama_siswa }}</b>: Sakit <br><span class='text-xs text-gray-500'>({{ $sakit->keterangan }})</span></li>";
            @endif
            @endforeach

            // Jika ditemukan siswa kelas ini yang sakit, tampilkan SweetAlert
            if (hasSakitInClass) {
                let fullHtml = "<div class='text-left mb-4'>" +
                    "<p class='font-bold text-red-600 mb-2'>Sedang Sakit:</p>" +
                    "<ul class='list-disc pl-5 space-y-2 text-sm text-gray-700'>" +
                    pesanHtml +
                    "</ul></div>"


                Swal.fire({
                    title: 'Informasi Medis Siswa',
                    html: fullHtml,
                    icon: 'info',
                    confirmButtonColor: '#3ab09e',
                    confirmButtonText: 'Baik, Mengerti'
                });
            }
        });
    </script>
    @endif
</x-absen-layout>