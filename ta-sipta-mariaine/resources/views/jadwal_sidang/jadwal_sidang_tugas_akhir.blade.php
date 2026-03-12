@extends('layouts.app')

@section('content')
    <div class="p-4 sm:ml-64">
        <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
            <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1 whitespace-nowrap">Jadwal Sidang Tugas Akhir</h1>
            <x-breadcrumb parent="Jadwal Sidang" item="Sidang Tugas Akhir" />
        </div>
        <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
            @include('components.alert-global')
            <div class="flex justify-between items-center mb-4 flex-wrap">
                <form action="{{ route('jadwal_sidang_tugas_akhir.dropdown-search') }}" method="GET" class="w-full" id="searchForm">
                    <div class="flex flex-col md:flex-row gap-4 w-full mb-4 flex-wrap">
                        {{-- Program Studi --}}
                        <div class="flex-1 min-w-[250px]">
                            <label for="program_studi" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Program Studi</label>
                            <select name="program_studi" id="program_studi"
                                class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                onchange="document.getElementById('searchForm').submit();">
                                <option value="">Semua Program Studi</option>
                                @foreach($programStudi as $prodi)
                                    <option value="{{ $prodi->id }}" {{ request()->get('program_studi') == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama_prodi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tahun Ajaran --}}
                        <div class="flex-1 min-w-[250px]">
                            <label for="tahun_ajaran" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Pilih Angkatan</label>
                            <select name="tahun_ajaran" id="tahun_ajaran"
                                class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                onchange="document.getElementById('searchForm').submit();">
                                <option value="">Semua Angkatan</option>
                                @foreach ($tahunAjaranList as $ta)
                                    <option value="{{ $ta->id }}" {{ request()->get('tahun_ajaran') == $ta->id ? 'selected' : '' }}>
                                        {{ $ta->tahun_ajaran }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Dosen --}}
                        <div class="flex-1 min-w-[250px]">
                            <label for="dosen_id" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Pilih Dosen</label>
                            <select name="dosen_id" id="dosen_id"
                                class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                onchange="document.getElementById('searchForm').submit();">
                                <option value="">Semua Dosen</option>
                                @foreach ($pembimbingUtama as $dosen)
                                    <option value="{{ $dosen->id }}" {{ request()->get('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                        {{ $dosen->nama_dosen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            @if (auth()->user() ->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi')
               <div class="flex flex-wrap justify-between items-start gap-4 mb-4">
                    {{-- Kiri: Import & Download Template --}}
                    <div class="flex gap-2 flex-wrap">
                        {{-- Import Jadwal Sidang --}}
                        <form action="{{ route('jadwal_sidang_tugas_akhir.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf
                            <input type="file" name="file" id="fileInput" accept=".csv, .xlsx, .xls" style="display: none;"
                                onchange="document.getElementById('importForm').submit();">
                            <button type="button"
                                onclick="document.getElementById('fileInput').click();"
                                class="flex items-center gap-2 text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="white" viewBox="0 0 48 48" id="import">
                                    <path d="m18 6-8 7.98h6V28h4V13.98h6L18 6zm14 28.02V20h-4v14.02h-6L30 42l8-7.98h-6z"></path>
                                    <path fill="none" d="M0 0h48v48H0z"></path>
                                </svg>
                                Import Jadwal Sidang Tugas Akhir
                            </button>
                        </form>

                        {{-- Download Template --}}
                        <a href="{{ route('template.download.jadwalsidangta') }}"
                            class="flex items-center gap-2 text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-6 py-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                            </svg>
                            Download Template
                        </a>
                    </div>

                    {{-- Kanan Bawah: Cetak Rekap --}}
                    <div class="w-full flex justify-end">
                        <a href="{{ route('rekap.dosen_penguji', ['tahun_ajaran' => request('tahun_ajaran')]) }}"
                            target="_blank"
                            class="flex items-center gap-2 text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-6 py-3">
                            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M13 11.15V4a1 1 0 1 0-2 0v7.15L8.78 8.374a1 1 0 1 0-1.56 1.25l4 5a1 1 0 0 0 1.56 0l4-5a1 1 0 1 0-1.56-1.25L13 11.15Z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M9.657 15.874 7.358 13H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-2.358l-2.3 2.874a3 3 0 0 1-4.685 0ZM17 16a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z" clip-rule="evenodd" />
                            </svg>
                            Cetak Rekap Dosen Penguji
                        </a>
                    </div>
                </div>


            @endif

            <div class="overflow-x-auto">
                @foreach($jadwalsGrouped as $namaProdi => $jadwalsPerProdi)
                    <h4 class="text-xl font-semibold mt-4 mb-4 whitespace-nowrap">
                        Program Studi {{ $jadwalsPerProdi->first()->mahasiswa->programStudi->nama_prodi }}
                    </h4>

                    @if($jadwalsPerProdi->isEmpty())
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                            <strong class="font-bold">Perhatian!</strong>
                            <span class="block sm:inline">Belum ada Jadwal Seminar Proposal untuk program studi ini.</span>
                        </div>
                    @else
                        <div class="mb-8">
                            <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                                <thead class="bg-gray-100">
                                    <tr class="text-center">
                                        <th class="w-1/12 border border-gray-300 px-4 py-2 whitespace-nowrap">No.</th>
                                        <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Mahasiswa</th>
                                        <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">NIM</th>
                                        <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Jenis Sidang</th>
                                        <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Pembimbing Utama</th>
                                        <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Pembimbing Pendamping</th>
                                        <th class="w-3/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Penguji Utama</th>
                                        <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Penguji Pendamping</th>
                                        <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Tanggal</th>
                                        <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Waktu</th>
                                        <th class="w-3/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Ruangan</th>
                                        @if (auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi')
                                            <th class="w-2 border border-gray-300 px-4 py-2 whitespace-nowrap">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach($jadwals as $jadwal) --}}
                                    {{-- @foreach($jadwalsPerProdi as $jadwal) --}}
                                    @foreach($jadwalsPerProdi as $jadwal)
                                        <tr class="hover:bg-gray-50 text-center">
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">{{ $loop->iteration }}</td>
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->mahasiswa->nama_mahasiswa }}</td>
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->mahasiswa->nim }}</td>
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->jenis_sidang }}</td>
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->pembimbingUtama->nama_dosen }}</td>
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->pembimbingPendamping->nama_dosen }}</td>
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->pengujiUtama->nama_dosen }}</td>
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->pengujiPendamping->nama_dosen }}</td>
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }}</td>
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</td>
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->ruanganSidang->nama_ruangan }}</td>
                                            @if (auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi')
                                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                                    <div class="flex justify-center space-x-2">
                                                        <button data-modal-target="editModal-{{ $jadwal->id }}" data-modal-toggle="editModal-{{ $jadwal->id }}" class="text-sm flex items-center justify-center w-full px-3 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-600 transition duration-200">
                                                            <svg class="mr-0.5 w-4 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                                            </svg>
                                                            Edit
                                                        </button>

                                                        <!-- Modal Edit -->
                                                        <div id="editModal-{{ $jadwal->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                                            <div class="relative p-4 w-full max-w-3xl max-h-full">
                                                                <!-- Modal content -->
                                                                <div class="relative bg-white rounded-lg shadow-sm">
                                                                    <!-- Modal header -->
                                                                    <div class="flex items-center justify-start p-4 md:p-5 border-b rounded-t border-gray-200">
                                                                        <h3 class="text-lg font-semibold text-gray-900">
                                                                            Form Edit Jadwal Sidang Tugas Akhir
                                                                        </h3>
                                                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="editModal-{{ $jadwal->id }}">
                                                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                                            </svg>
                                                                            <span class="sr-only">Close modal</span>
                                                                        </button>
                                                                    </div>
                                                                    <!-- Modal body -->
                                                                    <form action="{{ route('jadwal_sidang_tugas_akhir.update', $jadwal->id) }}" method="POST" class="p-4 md:p-5">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="grid gap-4 mb-4 grid-cols-2">
                                                                            <div class="grid-cols-2">
                                                                                <label for="mahasiswa_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Nama Mahasiswa</label>
                                                                                <input type="text" name="name" id="mahasiswa_id" value="{{ $jadwal->mahasiswa->nama_mahasiswa }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" disabled>
                                                                            </div>
                                                                            <div class="grid-cols-2">
                                                                                <label for="pembimbing_utama_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Dosen Pembimbing Utama</label>
                                                                                <select name="pembimbing_utama_id" id="pembimbing_utama_id"
                                                                                    class="tom-select dropdown-dosen">
                                                                                    @foreach ($pembimbingUtama as $utama)
                                                                                        <option value="{{ $utama->id }}" {{ $utama->id == $jadwal->pembimbing_utama_id ? 'selected' : '' }}>
                                                                                            {{ $utama->nama_dosen }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="grid-cols-2">
                                                                                <label for="pembimbing_pendamping_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Dosen Pembimbing Pendamping</label>
                                                                                <select name="pembimbing_pendamping_id" id="pembimbing_pendamping_id"
                                                                                    class="tom-select dropdown-dosen">
                                                                                    @foreach ($pembimbingPendamping as $pendamping)
                                                                                        <option value="{{ $pendamping->id }}" {{ $pendamping->id == $jadwal->pembimbing_pendamping_id ? 'selected' : '' }}>
                                                                                            {{ $pendamping->nama_dosen }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="grid-cols-2">
                                                                                <label for="penguji_utama_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Dosen Penguji Utama</label>
                                                                                <select name="penguji_utama_id" id="penguji_utama_id"
                                                                                    class="tom-select dropdown-dosen">
                                                                                    @foreach ($pengujiUtama as $utama)
                                                                                        <option value="{{ $utama->id }}" {{ $utama->id == $jadwal->penguji_utama_id ? 'selected' : '' }}>
                                                                                            {{ $utama->nama_dosen }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="grid-cols-2">
                                                                                <label for="penguji_pendamping_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Dosen Penguji Pendamping</label>
                                                                                <select name="penguji_pendamping_id" id="penguji_pendamping_id"
                                                                                    class="tom-select dropdown-dosen">
                                                                                    <option selected disabled>Pilih Dosen</option>
                                                                                    @foreach ($pengujiPendamping as $pendamping)
                                                                                        <option value="{{ $pendamping->id }}" {{ $pendamping->id == $jadwal->penguji_pendamping_id ? 'selected' : '' }}>
                                                                                            {{ $pendamping->nama_dosen }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="grid-cols-2">
                                                                                <label for="tanggal" class="text-left block mb-2 text-sm font-medium text-gray-900">Tanggal</label>
                                                                                <input type="date" name="tanggal" id="tanggal" value="{{ $jadwal->tanggal }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                                            </div>
                                                                            <div class="grid grid-cols-2 gap-4">
                                                                                <div>
                                                                                    <label for="waktu_mulai" class="text-left block mb-2 text-sm font-medium text-gray-900">Waktu Mulai</label>
                                                                                    <input type="text" name="waktu_mulai" id="waktu_mulai" value="{{ $jadwal->waktu_mulai }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                                                </div>
                                                                                <div>
                                                                                    <label for="waktu_selesai" class="text-left block mb-2 text-sm font-medium text-gray-900">Waktu Selesai</label>
                                                                                    <input type="text" name="waktu_selesai" id="waktu_selesai" value="{{ $jadwal->waktu_selesai }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="grid-cols-2">
                                                                                <label for="ruangan_sidang_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Ruangan Sidang</label>
                                                                                <select name="ruangan_sidang_id" id="ruangan_sidang_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                                                    <option disabled value="">Pilih Ruangan Sidang</option>
                                                                                    @foreach ($ruanganSidang as $ruangan)
                                                                                        <option value="{{ $ruangan->id }}" {{ $jadwal->ruangan_sidang_id == $ruangan->id ? 'selected' : '' }}>
                                                                                            {{ $ruangan->nama_ruangan }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex justify-end space-x-2">
                                                                            <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="editModal-{{ $jadwal->id }}">
                                                                                <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                                                </svg>
                                                                                Batal
                                                                            </button>
                                                                            <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                                                                <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                                    <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                                                                    <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
                                                                                </svg>
                                                                                Simpan
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
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
                    @endif
                @endforeach
            </div>

        @if($jadwals->isEmpty())
           <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                @if (auth()->user() ->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi')
                    <span class="block sm:inline">Belum ada Jadwal Sidang Tugas Akhir. Silakan import jadwal sidang tugas akhir untuk program studi Anda!</span>
                @elseif (auth()->user() ->role === 'Mahasiswa')
                    <span class="block sm:inline">Belum ada Jadwal Sidang Tugas Akhir. Silakan tunggu informasi ini secara berkala!</span>
                @else
                    <span class="block sm:inline">Belum ada Jadwal Sidang Tugas Akhir. Silakan tunggu informasi ini secara berkala!</span>
                @endif
            </div>
        @endif

            {{-- @if($jadwals->isEmpty())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>

                    @if (auth()->user() ->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi')
                        <span class="block sm:inline">Belum ada Jadwal Sidang Tugas Akhir. Silakan import jadwal sidang tugas akhir untuk program studi Anda!</span>
                    @elseif (auth()->user() ->role === 'Mahasiswa')
                        <span class="block sm:inline">Belum ada Jadwal Sidang Tugas Akhir. Silakan tunggu informasi ini secara berkala!</span>
                    @else
                        <span class="block sm:inline">Belum ada Jadwal Sidang Tugas Akhir. Silakan tunggu informasi ini secara berkala!</span>
                    @endif
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr class="text-center">
                                <th class="w-1/12 border border-gray-300 px-4 py-2 whitespace-nowrap">No.</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Mahasiswa</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Jenis Sidang</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Pembimbing Utama</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Pembimbing Pendamping</th>
                                <th class="w-3/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Penguji Utama</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Penguji Pendamping</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Tanggal</th>
                                <th class="w-2/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Waktu</th>
                                <th class="w-3/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Ruangan</th>
                                <th class="w-2 border border-gray-300 px-4 py-2 whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwals as $jadwal)
                                <tr class="hover:bg-gray-50 text-center">
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">{{ $loop->iteration + ($jadwals->currentPage() - 1) * $jadwals->perPage() }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->mahasiswa->nama_mahasiswa }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->jenis_sidang }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->pembimbingUtama->nama_dosen }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->pembimbingPendamping->nama_dosen }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->pengujiUtama->nama_dosen }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->pengujiPendamping->nama_dosen }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->ruanganSidang->nama_ruangan }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                        <div class="flex justify-center space-x-2">
                                            <button data-modal-target="editModal-{{ $jadwal->id }}" data-modal-toggle="editModal-{{ $jadwal->id }}" class="text-sm flex items-center justify-center w-full px-3 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-600 transition duration-200">
                                                <svg class="mr-0.5 w-4 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                                </svg>
                                                Edit
                                            </button>

                                            <!-- Modal Edit -->
                                            <div id="editModal-{{ $jadwal->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                                <div class="relative p-4 w-full max-w-3xl max-h-full">
                                                    <!-- Modal content -->
                                                    <div class="relative bg-white rounded-lg shadow-sm">
                                                        <!-- Modal header -->
                                                        <div class="flex items-center justify-start p-4 md:p-5 border-b rounded-t border-gray-200">
                                                            <h3 class="text-lg font-semibold text-gray-900">
                                                                Form Edit Jadwal Sidang Tugas Akhir
                                                            </h3>
                                                            <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="editModal-{{ $jadwal->id }}">
                                                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                                </svg>
                                                                <span class="sr-only">Close modal</span>
                                                            </button>
                                                        </div>
                                                        <!-- Modal body -->
                                                        <form action="{{ route('jadwal_sidang_tugas_akhir.update', $jadwal->id) }}" method="POST" class="p-4 md:p-5">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="grid gap-4 mb-4 grid-cols-2">
                                                                <div class="grid-cols-2">
                                                                    <label for="mahasiswa_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Nama Mahasiswa</label>
                                                                    <input type="text" name="name" id="mahasiswa_id" value="{{ $jadwal->mahasiswa->nama_mahasiswa }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" disabled>
                                                                </div>
                                                                <div class="grid-cols-2">
                                                                    <label for="pembimbing_utama_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Dosen Pembimbing Utama</label>
                                                                    <select name="pembimbing_utama_id" id="pembimbing_utama_id"
                                                                        class="tom-select dropdown-dosen">
                                                                        @foreach ($pembimbingUtama as $utama)
                                                                            <option value="{{ $utama->id }}" {{ $utama->id == $jadwal->pembimbing_utama_id ? 'selected' : '' }}>
                                                                                {{ $utama->nama_dosen }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="grid-cols-2">
                                                                    <label for="pembimbing_pendamping_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Dosen Pembimbing Pendamping</label>
                                                                    <select name="pembimbing_pendamping_id" id="pembimbing_pendamping_id"
                                                                        class="tom-select dropdown-dosen">
                                                                        @foreach ($pembimbingPendamping as $pendamping)
                                                                            <option value="{{ $pendamping->id }}" {{ $pendamping->id == $jadwal->pembimbing_pendamping_id ? 'selected' : '' }}>
                                                                                {{ $pendamping->nama_dosen }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="grid-cols-2">
                                                                    <label for="penguji_utama_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Dosen Penguji Utama</label>
                                                                    <select name="penguji_utama_id" id="penguji_utama_id"
                                                                        class="tom-select dropdown-dosen">
                                                                        @foreach ($pengujiUtama as $utama)
                                                                            <option value="{{ $utama->id }}" {{ $utama->id == $jadwal->penguji_utama_id ? 'selected' : '' }}>
                                                                                {{ $utama->nama_dosen }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="grid-cols-2">
                                                                    <label for="penguji_pendamping_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Dosen Penguji Pendamping</label>
                                                                    <select name="penguji_pendamping_id" id="penguji_pendamping_id"
                                                                        class="tom-select dropdown-dosen">
                                                                        <option selected disabled>Pilih Dosen</option>
                                                                        @foreach ($pengujiPendamping as $pendamping)
                                                                            <option value="{{ $pendamping->id }}" {{ $pendamping->id == $jadwal->penguji_pendamping_id ? 'selected' : '' }}>
                                                                                {{ $pendamping->nama_dosen }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="grid-cols-2">
                                                                    <label for="tanggal" class="text-left block mb-2 text-sm font-medium text-gray-900">Tanggal</label>
                                                                    <input type="date" name="tanggal" id="tanggal" value="{{ $jadwal->tanggal }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                                </div>
                                                                <div class="grid grid-cols-2 gap-4">
                                                                    <div>
                                                                        <label for="waktu_mulai" class="text-left block mb-2 text-sm font-medium text-gray-900">Waktu Mulai</label>
                                                                        <input type="text" name="waktu_mulai" id="waktu_mulai" value="{{ $jadwal->waktu_mulai }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                                    </div>
                                                                    <div>
                                                                        <label for="waktu_selesai" class="text-left block mb-2 text-sm font-medium text-gray-900">Waktu Selesai</label>
                                                                        <input type="text" name="waktu_selesai" id="waktu_selesai" value="{{ $jadwal->waktu_selesai }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                                    </div>
                                                                </div>
                                                                <div class="grid-cols-2">
                                                                    <label for="ruangan_sidang_id" class="text-left block mb-2 text-sm font-medium text-gray-900">Ruangan Sidang</label>
                                                                    <select name="ruangan_sidang_id" id="ruangan_sidang_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                                        <option disabled value="">Pilih Ruangan Sidang</option>
                                                                        @foreach ($ruanganSidang as $ruangan)
                                                                            <option value="{{ $ruangan->id }}" {{ $jadwal->ruangan_sidang_id == $ruangan->id ? 'selected' : '' }}>
                                                                                {{ $ruangan->nama_ruangan }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="flex justify-end space-x-2">
                                                                <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="editModal-{{ $jadwal->id }}">
                                                                    <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                                    </svg>
                                                                    Batal
                                                                </button>
                                                                <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                                                    <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                                                        <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    Simpan
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Page navigation example">
                    {{ $jadwals->links() }}
                </nav>
            @endif --}}
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function updateAllDropdowns(modal) {
                const dropdowns = Array.from(modal.querySelectorAll('.dropdown-dosen'));
                const selectedValues = dropdowns.map(dd => dd.value).filter(v => v);

                dropdowns.forEach(currentDropdown => {
                    const currentValue = currentDropdown.value;

                    for (const option of currentDropdown.options) {
                        if (option.value === "" || option.value === currentValue) {
                            option.hidden = false;
                        } else {
                            option.hidden = selectedValues.includes(option.value);
                        }
                    }
                });
            }

            function setupDropdownListeners(modal) {
                const dropdowns = modal.querySelectorAll('.dropdown-dosen');

                dropdowns.forEach(dropdown => {
                    dropdown.addEventListener('change', () => {
                        updateAllDropdowns(modal);
                    });
                });

                // Inisialisasi saat pertama kali dibuka
                updateAllDropdowns(modal);
            }

            // Untuk setiap modal edit
            document.querySelectorAll('[id^="editModal-"]').forEach(modal => {
                setupDropdownListeners(modal);
            });
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[id^="editModal-"]').forEach(modal => {
            // Ambil semua select dengan class .dropdown-dosen dalam modal tersebut
            const selectElements = modal.querySelectorAll('.dropdown-dosen');

            // Simpan semua TomSelect instance
            const tomSelects = Array.from(selectElements).map(select => {
                return new TomSelect(select, {
                    placeholder: 'Pilih Dosen',
                    allowEmptyOption: true,
                    onChange: hideSelected
                });
            });

            function hideSelected() {
                // Ambil semua nilai yang sedang dipilih
                const selectedValues = tomSelects.map(ts => ts.getValue()).filter(val => val);

                tomSelects.forEach(currentSelect => {
                    const currentValue = currentSelect.getValue();
                    currentSelect.clearOptions();

                    const selectElement = modal.querySelector(`#${currentSelect.input.id}`);
                    Array.from(selectElement.options).forEach(opt => {
                        const isUsed = selectedValues.includes(opt.value) && opt.value !== currentValue;
                        opt.hidden = isUsed;

                        if (!isUsed || opt.value === currentValue) {
                            currentSelect.addOption({ value: opt.value, text: opt.textContent });
                        }
                    });

                    currentSelect.refreshOptions(false);
                });
            }

            hideSelected(); // Inisialisasi saat modal dibuka
        });
    });
    </script>

@endsection

