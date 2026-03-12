@extends('layouts.app')

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
        <h1 class="text-2xl font-bold text-left mb-4 md:mb-0 md:w-auto md:flex-1">Hasil Seminar Proposal</h1>
        <x-breadcrumb parent="Hasil Akhir Sempro" />
    </div>

    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
        @if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen->jabatan === 'Super Admin')
            <div class="flex items-center mb-4 flex-wrap">
                <form action="{{ route('hasil_akhir_sempro.dropdown-search') }}" method="GET" id="searchForm" class="max-w-full w-full">
                    <div class="flex flex-col md:flex-row gap-4 w-full mb-4">
                        <div class="w-1/6 min-w-[200px]">
                            <label for="status_sidang" class="block text-sm font-medium text-gray-900 mb-1">Status Kelulusan</label>
                            <select name="status_sidang" id="status_sidang"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                onchange="document.getElementById('searchForm').submit();">
                                <option value="">Semua Status</option>
                                @foreach ($statusList as $status)
                                    <option value="{{ $status }}" {{ request('status_sidang') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-1/6 min-w-[200px]">
                            <label for="tahun_ajaran" class="block text-sm font-medium text-gray-900 mb-1">Tahun Ajaran</label>
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
                    </div>
                </form>
            </div>
        @endif

        @if($hasilAkhirSempro->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Belum ada data hasil akhir sempro.</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr class="text-center">
                            <th class="w-1 border border-gray-300 px-4 py-2 whitespace-nowrap">No.</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nama Mahasiswa</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Jadwal Seminar</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nilai Penguji Utama</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nilai Penguji Pendamping</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nilai Akhir</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Status Sidang</th>
                            <th class="w-3 border border-gray-300 px-4 py-2 whitespace-nowrap" colspan="2">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($hasilAkhirSempro as $hasil)
                            <tr class="hover:bg-gray-50 text-center">
                                <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration + ($hasilAkhirSempro->currentPage() - 1) * $hasilAkhirSempro->perPage() }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $hasil->mahasiswa->nama_mahasiswa }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $hasil->mahasiswa->jadwalSeminarProposal->tanggal }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    @if ($hasil->nilai_penguji_utama !== null)
                                        {{ $hasil->nilai_penguji_utama }}
                                    @else
                                        <span class="italic text-red-500">Belum Ada Nilai</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    @if ($hasil->nilai_penguji_pendamping !== null)
                                        {{ $hasil->nilai_penguji_pendamping }}
                                    @else
                                        <span class="italic text-red-500">Belum Ada Nilai</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    @if ($hasil->total_akhir !== null)
                                        {{ $hasil->total_akhir }}
                                    @else
                                        <span class="italic text-red-500">Belum Ada Nilai</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    @if ($hasil->status_sidang)
                                        {{ $hasil->status_sidang }}
                                    @else
                                        <span class="italic text-center text-red-500">Belum Ada Status</span>
                                    @endif
                                </td>

                                @if ($hasil->penilaianSempro && $hasil->penilaianSempro->catatan_revisi)
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                        <a href="{{ route('penilaian_sempro.catatan.gabung', $hasil->jadwal_seminar_proposal_id) }}"
                                            target="_blank"
                                            class="inline-flex text-sm items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                            <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                                            </svg>
                                            Catatan Sempro
                                        </a>
                                    </td>
                                @else
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                        <button type="button"
                                            class="inline-flex text-sm items-center gap-2 bg-gray-300 hover:bg-gray-500 text-gray-600 font-semibold px-4 py-2 rounded cursor-not-allowed"
                                            disabled>
                                            <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                                            </svg>
                                            Catatan Sempro
                                        </button>
                                    </td>
                                @endif

                                @if ($hasil->status_sidang)
                                    <td class="w-2 border border-gray-300 px-4 py-2 whitespace-nowrap">
                                        <a href="{{ route('berita_acara.seminar-proposal.lihat', [
                                        'jadwal_id' => $hasil->jadwal_seminar_proposal_id, 'mahasiswa_id' => $hasil->mahasiswa->id]) }}" target="_blank"
                                            class="inline-flex text-sm items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                            <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                                            </svg>
                                            Berita Acara
                                        </a>
                                    </td>
                                @else
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                        <button type="button"
                                            class="inline-flex text-sm items-center gap-2 bg-gray-300  hover:bg-gray-500 text-gray-600 font-semibold px-4 py-2 rounded cursor-not-allowed"
                                            disabled>
                                            <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                                            </svg>
                                            Berita Acara
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example">
                {{ $hasilAkhirSempro->links() }}
            </nav>
        @endif
    </div>
</div>
@endsection
