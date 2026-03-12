@extends('layouts.app')


@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Rekap Nilai Tugas Akhir</h1>
        <x-breadcrumb parent="Penilaian" item="Rekap Nilai Tugas Akhir" />
    </div>
    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
    @include('components.alert-global')

    <div class="flex flex-wrap items-end justify-between mb-4 gap-4">
        {{-- Form Filter --}}
        <form action="{{ route('penilaian_ta.rekap_nilai') }}" method="GET" id="filter-form"
            class="flex flex-col md:flex-row gap-4 flex-wrap">

            {{-- Status Kelulusan --}}
            <div class="min-w-[200px]">
                <label for="status_kelulusan" class="block text-sm font-medium text-gray-900 mb-1">
                    Status Kelulusan
                </label>
                <select name="status_kelulusan" id="status_kelulusan"
                    class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                    onchange="document.getElementById('filter-form').submit();">
                    <option value="">Semua Status</option>
                    @foreach ($statusList as $status)
                        <option value="{{ $status }}" {{ request('status_kelulusan') == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tahun Ajaran --}}
            <div class="min-w-[200px]">
                <label for="tahun_ajaran" class="block text-sm font-medium text-gray-900 mb-1">
                    Tahun Ajaran
                </label>
                <select name="tahun_ajaran" id="tahun_ajaran"
                    class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                    onchange="document.getElementById('filter-form').submit();">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach ($tahunAjaranList as $ta)
                        <option value="{{ $ta->id }}" {{ request('tahun_ajaran') == $ta->id ? 'selected' : '' }}>
                            {{ $ta->tahun_ajaran }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi')
            {{-- Tombol Cetak --}}
            <div class="ml-auto">
                <form action="{{ route('penilaian_ta.cetak_rekap') }}" method="GET" target="_blank">
                    <input type="hidden" name="tahun_ajaran" value="{{ request('tahun_ajaran') }}">
                    <input type="hidden" name="status_kelulusan" value="{{ request('status_kelulusan') }}">
                    <button type="submit"
                        class="inline-flex gap-1 items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition">
                        <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M13 11.15V4a1 1 0 1 0-2 0v7.15L8.78 8.374a1 1 0 1 0-1.56 1.25l4 5a1 1 0 0 0 1.56 0l4-5a1 1 0 1 0-1.56-1.25L13 11.15Z"
                                clip-rule="evenodd" />
                            <path fill-rule="evenodd"
                                d="M9.657 15.874 7.358 13H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-2.358l-2.3 2.874a3 3 0 0 1-4.685 0ZM17 16a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z"
                                clip-rule="evenodd" />
                        </svg>
                        Cetak Rekap Nilai
                    </button>
                </form>
            </div>
        @endif
    </div>

            @if($rekap->isEmpty())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Tidak ada data nilai yang bisa ditampilkan.</span>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr class="text-center">
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap" rowspan="2">No.</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap" rowspan="2">Nama</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap" rowspan="2">NIM</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap" rowspan="2">Program Studi</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap" rowspan="2">Tahun Ajaran</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap" colspan="2">Nilai</th>
                            </tr>
                            <tr>
                                <th>Angka</th>
                                <th>Huruf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rekap as $item)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration + ($hasilAkhirAll->currentPage() - 1) * $hasilAkhirAll->perPage() }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $item['nama'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $item['nim'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $item['prodi'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $item['tahun_ajaran'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $item['total_angka'] }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $item['total_huruf'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Page navigation example">
                    {{ $hasilAkhirAll->links() }} <!-- Ini akan menghasilkan pagination -->
                </nav>
            @endif
        {{-- @endif --}}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const modalToggleButtons = document.querySelectorAll('[data-modal-toggle]');

        modalToggleButtons.forEach(button => {
            button.addEventListener('click', () => {
                const modalId = button.getAttribute('data-modal-target');
                const modal = document.getElementById(modalId);
                modal.classList.toggle('hidden'); // Toggle modal visibility
                modal.classList.toggle('flex'); // Toggle modal visibility
            });
        });
    });
</script>

@endsection


