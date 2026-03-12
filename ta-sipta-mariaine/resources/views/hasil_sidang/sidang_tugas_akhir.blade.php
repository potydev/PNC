@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold text-left mb-4 md:mb-0 md:w-auto md:flex-1">Hasil Sidang</h1>
        <x-breadcrumb parent="Hasil Sidang" />
    </div>

    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
        @include('components.alert-global')

        <div class="flex flex-wrap items-end justify-between mb-4 gap-4">
            {{-- Form Filter --}}
            <form action="{{ route('hasil_sidang.tugas_akhir.dropdown-search') }}" method="GET" id="searchForm"
                class="flex flex-col md:flex-row gap-4 flex-wrap">

                <div class="min-w-[200px]">
                    <label for="status_kelulusan" class="block text-sm font-medium text-gray-900 mb-1">
                        Status Kelulusan
                    </label>
                    <select name="status_kelulusan" id="status_kelulusan"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            onchange="document.getElementById('searchForm').submit();">
                        <option value="">Semua Status</option>
                        @foreach ($statusList as $status)
                            <option value="{{ $status }}" {{ request('status_kelulusan') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[200px]">
                    <label for="tahun_lulus" class="block text-sm font-medium text-gray-900 mb-1">
                        Tahun Lulus
                    </label>
                    <select name="tahun_lulus" id="tahun_lulus"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            onchange="document.getElementById('searchForm').submit();">
                        <option value="">Semua Tahun</option>
                        @foreach ($tahunList as $tahun)
                            <option value="{{ $tahun }}" {{ request('tahun_lulus') == $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[200px]">
                    <label for="tahun_ajaran" class="block text-sm font-medium text-gray-900 mb-1">
                        Tahun Ajaran
                    </label>
                    <select name="tahun_ajaran" id="tahun_ajaran"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            onchange="document.getElementById('searchForm').submit();">
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
                    <form action="{{ route('penilaian_ta.cetak_rekap_yudisium') }}" method="GET" target="_blank">
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
                            Cetak Mahasiswa Yudisium
                        </button>
                    </form>
                </div>
            @endif

            <p class="text-sm text-gray-700 mt-4">
                Jumlah Mahasiswa yang Sudah Sidang: <strong>{{ $jumlahMahasiswaSidang }}</strong>
            </p>
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
                            <th class="w-3 border border-gray-300 px-4 py-2 whitespace-nowrap">Riwayat</th>
                            <th class="w-3 border border-gray-300 px-4 py-2 whitespace-nowrap">File Revisi</th>
                            @if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi')
                                <th class="w-3 border border-gray-300 px-4 py-2 whitespace-nowrap" colspan="2">Kelengkapan Yudisium</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hasilSidang as $hasil)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                    {{ $loop->iteration + ($hasilSidang->currentPage() - 1) * $hasilSidang->perPage() }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    {{ $hasil->mahasiswa->nama_mahasiswa }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                    {{ $hasil->status_kelulusan ?? 'Belum Lulus' }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                    {{ $hasil->tahun_lulus ?? 'Belum Lulus' }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('hasil_sidang.tugas_akhir.show', $hasil->id) }}"
                                            class="inline-flex text-sm items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                            <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M7 2a2 2 0 0 0-2 2v1a1 1 0 0 0 0 2v1a1 1 0 0 0 0 2v1a1 1 0 1 0 0 2v1a1 1 0 1 0 0 2v1a1 1 0 1 0 0 2v1a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H7Zm3 8a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm-1 7a3 3 0 0 1 3-3h2a3 3 0 0 1 3 3 1 1 0 0 1-1 1h-6a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                            </svg>
                                            Riwayat Sidang
                                        </a>
                                    </div>
                                </td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    @if ($hasil && $hasil->file_revisi)
                                        <div class="flex justify-center space-x-2 whitespace-nowrap">
                                            <a href="{{ route('hasil_sidang.show_file_revisi', $hasil->id) }}" target="_blank"
                                            class="inline-flex text-sm items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                            <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                            </svg>Lihat File</a>
                                        </div>
                                    @else
                                        <span class="text-red-500 italic">Belum upload revisi</span>
                                    @endif
                                </td>
                                @if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi')
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                        {{ $hasil->kelengkapan_yudisium }}
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                        <!-- Tombol Cek Kelengkapan -->
                                        <button data-modal-target="kelengkapanModal-{{ $hasil->id }}"
                                            data-modal-toggle="kelengkapanModal-{{ $hasil->id }}"
                                            class="inline-flex text-sm items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                            <svg class="mr-1 w-4 h-4 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z" clip-rule="evenodd"/>
                                            </svg>
                                            Cek Kelengkapan
                                        </button>

                                        <!-- Modal Kelengkapan -->
                                        <div id="kelengkapanModal-{{ $hasil->id }}" tabindex="-1" aria-hidden="true"
                                            class="fixed hidden z-50 w-full inset-0 flex justify-center items-center bg-black bg-opacity-50">
                                            <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
                                                <button type="button"
                                                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
                                                    data-modal-hide="kelengkapanModal-{{ $hasil->id }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>

                                                <h2 class="text-lg font-semibold mb-4 text-center">Cek Kelengkapan Yudisium</h2>
                                                <p class="mb-4 text-center">Pilih status kelengkapan yudisium untuk mahasiswa ini:</p>

                                                <div class="flex justify-center gap-4 mb-4">
                                                    <!-- Tombol Belum Lengkap -->
                                                    <form action="{{ route('hasil_sidang.cek_kelengkapan', $hasil->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="kelengkapan_yudisium" value="Belum Lengkap">
                                                        <button type="submit"class="flex justify-center items-center gap-1 text-sm px-3 py-3 w-36 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">
                                                            <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                                                            </svg>
                                                            Belum Lengkap
                                                        </button>
                                                    </form>

                                                    <!-- Tombol Lengkap -->
                                                    <form action="{{ route('hasil_sidang.cek_kelengkapan', $hasil->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="kelengkapan_yudisium" value="Lengkap">
                                                        <button type="submit" class="flex justify-center items-center gap-1 text-sm px-3 py-3 w-36 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                                                            <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
                                                            </svg>
                                                            Lengkap
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                @endif
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
@endsection
