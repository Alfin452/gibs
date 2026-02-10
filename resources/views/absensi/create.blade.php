<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Kehadiran Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
            <div class="p-8 bg-white border-b border-gray-100">

                <div class="mb-6 text-center">
                    <h3 class="text-lg font-bold text-gray-900">Mulai Absensi</h3>
                    <p class="text-sm text-gray-500">Pilih kelas dan mata pelajaran untuk membuka lembar absensi.</p>
                </div>

                <form action="{{ route('absensi.cek') }}" method="POST">
                    @csrf

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pertemuan</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors">
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                        <select name="id_mapel" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($mapels as $m)
                            <option value="{{ $m->id_mapel }}">{{ $m->nama_mapel }} ({{ $m->tipe }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                        <select name="id_kelas" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                            <option value="{{ $k->id_kelas }}">{{ $k->nama_kelas }} ({{ $k->fase }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('absensi.index') }}" class="text-gray-500 hover:text-gray-700 font-medium text-sm">Batal</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-lg shadow-indigo-600/20 transition-all transform hover:scale-105">
                            Buka Lembar Absensi →
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>