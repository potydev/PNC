{{-- @extends('layouts.app')

@if ($errors->any())
    <div class="bg-red-100 border border-red-500 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Ada kesalahan!</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Hasil Sidang</h1>
        <x-breadcrumb parent="Hasil Sidang" />
    </div>
    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
        <div class="flex items-center mb-4 flex-wrap">
            <form action="{{ route('hasil_sidang.dropdown-search') }}" method="GET" id="searchForm" class="max-w-full w-full">
                <div class="flex flex-col md:flex-row gap-4 w-full mb-4">
                    <div class="w-1/4 min-w-[310px]">
                        <label for="status_kelulusan" class="block text-sm font-medium text-gray-900 mb-1">Status Kelulusan</label>
                        <select name="status_kelulusan" id="status_kelulusan"
                            class="w-full p-2.5 border border-gray-300 rounded-lg"
                            onchange="document.getElementById('searchForm').submit();">
                            <option value="">Semua Status</option>
                            @foreach ($statusList as $status)
                                <option value="{{ $status }}" {{ request('status_kelulusan') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tahun Lulus -->
                    <div class="w-1/4 min-w-[310px]">
                        <label for="tahun_lulus" class="block text-sm font-medium text-gray-900 mb-1">Tahun Lulus</label>
                        <select name="tahun_lulus" id="tahun_lulus"
                            class="w-full p-2.5 border border-gray-300 rounded-lg"
                            onchange="document.getElementById('searchForm').submit();">
                            <option value="">Semua Tahun</option>
                            @foreach ($tahunList as $tahun)
                                <option value="{{ $tahun }}" {{ request('tahun_lulus') == $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>

        @if($hasilSidang->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Belum ada data hasil sidang.</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr class="text-center">
                                <th class="w-1 border border-gray-300 px-4 py-2 whitespace-nowrap">No.</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nama Mahasiswa</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Status Kelulusan</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Tahun Lulus</th>
                                <th class=" w-3 border border-gray-300 px-4 py-2 whitespace-nowrap">Aksi</th>
                            </tr>
                    </thead>
                    <tbody>
                        @foreach($hasilSidang as $hasil)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration + ($hasilSidang->currentPage() - 1) * $hasilSidang->perPage() }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $hasil->mahasiswa->nama_mahasiswa }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $hasil->status_kelulusan }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $hasil->tahun_lulus }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    <div class="flex justify-center space-x-2 whitespace-nowrap">
                                        <a href="{{ route('hasil_sidang.show', $hasil->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                            Lihat Riwayat
                                        </a>
                                    </div>
                                </td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    <div class="flex justify-center space-x-2 whitespace-nowrap">
                                        <a href="{{ route('hasil_sidang.show_file_revisi', $hasil->id) }}" target="_blank"
                                        class="mt-2 inline-flex text-sm items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                        <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                        </svg>Lihat File</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <nav aria-label="Page navigation example">
                {{ $hasilSidang->links() }}
            </nav>
        @endif
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

 --}}
