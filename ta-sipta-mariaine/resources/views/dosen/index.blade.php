@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Data Dosen</h1>
        <x-breadcrumb parent="Data Master" item="Dosen" />
    </div>
    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
        @include('components.alert-global')

        <!-- Modal toggle -->
        <div class="flex justify-between items-start mb-4 flex-wrap">
            <!-- Tombol Tambah Dosen -->
            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                class="focus:outline-none text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-4">
                <svg class="w-7 h-7 inline-block" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                        clip-rule="evenodd"></path>
                </svg>
                Tambah Dosen
            </button>

            <!-- Import & Download Template Dosen -->
            <div class="flex flex-col w-52">
                <!-- Form Import -->
                <form action="{{ route('dosen.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <input type="file" name="file" id="fileInput" accept=".csv, .xlsx, .xls" style="display: none;"
                        onchange="document.getElementById('importForm').submit();">
                    <button type="button" onclick="document.getElementById('fileInput').click();"
                        class="w-full flex items-center justify-center gap-2 focus:outline-none text-white bg-blue-500 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="white" viewBox="0 0 48 48" id="import">
                            <path d="m18 6-8 7.98h6V28h4V13.98h6L18 6zm14 28.02V20h-4v14.02h-6L30 42l8-7.98h-6z"></path>
                            <path fill="none" d="M0 0h48v48H0z"></path>
                        </svg>
                        Import Dosen
                    </button>
                </form>

                <!-- Tombol Download Template -->
                <a href="{{ route('template.download.dosen') }}"
                    class="w-full flex items-center justify-center focus:outline-none text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-6 py-3">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Download Template
                </a>
            </div>
        </div>


        <div class="flex justify-between items-center mb-4 flex-wrap">
            <form action="{{ route('dosen.search') }}" method="GET" class="w-full sm:max-w-xs mt-3" id="search-form">
                <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="search-input" name="search"
                        class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari data dosen disini"
                        required
                        value="{{ request('search') }}" style="min-width: 300px"
                        oninput="document.querySelector('#search-form').submit();" />
                    {{-- <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Search</button> --}}
                </div>
            </form>
        </div>

        <!-- Main modal -->
        <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
            <div class="relative p-4 w-full max-w-3xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Form Tambah Dosen
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form action="{{ route('dosen.store') }}" method="POST" class="p-4 md:p-5">
                        @csrf
                        <div class="grid gap-4 mb-4 grid-cols-2">
                            <div class="grid-cols-2">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Dosen</label>
                                <input type="text" name="name" id="name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Maria Ine" required />
                            </div>
                            <div class="grid-cols-2">
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                                <input type="text" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="mariaine@gmail.com" required />
                            </div>
                            <div class="grid-cols-2">
                                <label for="nip" class="block mb-2 text-sm font-medium text-gray-900">NIP</label>
                                <input type="number" name="nip" id="nip" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="22010" required />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <label for="jenis_kelamin" class="block text-sm font-medium text-gray-900">Jenis Kelamin</label>
                                <div class="flex col-span-2">
                                    <div class="flex items-center mb-6">
                                        <input id="Laki-laki" type="radio" name="jenis_kelamin" value="Laki-laki" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 dark:focus:bg-blue-600 dark:bg-gray-700 dark:border-gray-600" required>
                                        <label for="Laki-laki" class="block ml-2 mr-6 text-sm font-medium text-gray-900 dark:text-gray-300">Laki-laki</label>
                                    </div>
                                    <div class="flex items-center mb-6">
                                        <input id="Perempuan" type="radio" name="jenis_kelamin" value="Perempuan" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300 dark:focus:ring-blue-600 dark:focus:bg-blue-600 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="Perempuan" class="block ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Perempuan</label>
                                    </div>
                                </div>
                            </div>

                            <div class="grid-cols-2">
                                <label for="tempat_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" id="tempat_lahir" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Kebumen" required />
                            </div>
                            <div class="grid-cols-2">
                                <label for="tanggal_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                            </div>
                            <div class="grid-cols-2">
                                <label for="jabatan" class="block mb-2 text-sm font-medium text-gray-900">Jabatan</label>
                                <select name="jabatan" id="jabatan"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                    <option value="" selected disabled>Pilih Jabatan</option>
                                    <option value="Dosen Biasa">Dosen Biasa</option>
                                    <option value="Koordinator Program Studi">Koordinator Program Studi</option>
                                    <option value="Super Admin">Super Admin</option>
                                </select>
                            </div>
                            <div class="grid-cols-2">
                                <label for="program_studi_id" class="block mb-2 text-sm font-medium text-gray-900">Pilih Program Studi</label>
                                <select id="program_studi_id" name="program_studi_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option selected disabled>Pilih Program Studi</option>
                                    @foreach ($programStudi as $prodi)
                                        <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="crud-modal">
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

        @if($dosen->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Tidak ada data dosen yang bisa ditampilkan. Silakan tambahkan data dosen.</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr class="text-center">
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">No.</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nama Dosen</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">NIP</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Tempat Lahir</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Tanggal Lahir</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Jenis Kelamin</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Jabatan</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Program Studi</th>
                                <th class=" w-3 border border-gray-300 px-4 py-2 whitespace-nowrap">Aksi</th>
                            </tr>
                    </thead>
                    <tbody>
                        @foreach($dosen as $dsn)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration + ($dosen->currentPage() - 1) * $dosen->perPage() }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $dsn->nama_dosen }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $dsn->nip }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $dsn->tempat_lahir }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($dsn->tanggal_lahir)->translatedFormat('d F Y') }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $dsn->jenis_kelamin }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $dsn->jabatan ?? 'Dosen Biasa' }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $dsn->programStudi->nama_prodi ?? 'Tidak ada program studi' }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <div class="flex justify-center space-x-2">
                                        <button data-modal-target="editModal-{{ $dsn->id }}" data-modal-toggle="editModal-{{ $dsn->id }}" class="text-sm flex items-center justify-center w-full px-3 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-600 transition duration-200">
                                            <svg class="mr-0.5 w-4 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                            </svg>
                                            Edit
                                        </button>

                                        <button data-modal-target="popup-modal-{{ $dsn->id }}" data-modal-toggle="popup-modal-{{ $dsn->id }}" class="text-sm flex items-center justify-center w-full px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">
                                            <svg class="mr-0.5 w-4 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>

                                    <!-- Modal Edit -->
                                    <div id="editModal-{{ $dsn->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                        <div class="relative p-4 w-full max-w-3xl max-h-full">
                                            <!-- Modal content -->
                                            <div class="relative bg-white rounded-lg shadow-sm">
                                                <!-- Modal header -->
                                                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                                    <h3 class="text-lg font-semibold text-gray-900">
                                                        Form Edit Dosen
                                                    </h3>
                                                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="editModal-{{ $dsn->id }}">
                                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                        </svg>
                                                        <span class="sr-only">Close modal</span>
                                                    </button>
                                                </div>
                                                <!-- Modal body -->
                                                <form action="{{ route('dosen.update',  $dsn->id) }}" method="POST" class="p-4 md:p-5">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="grid gap-4 mb-4 grid-cols-2">
                                                        <div class="grid-cols-2">
                                                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Dosen</label>
                                                            <input type="text" name="name" id="name" value="{{ $dsn->user->name }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                        </div>
                                                        <div class="grid-cols-2">
                                                            <label for="nip" class="block mb-2 text-sm font-medium text-gray-900">NIP</label>
                                                            <input type="text" name="nip" id="nip" value="{{ $dsn->nip }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                        </div>
                                                        <div class="grid-cols-2">
                                                            <label for="tempat_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tempat Lahir</label>
                                                            <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ $dsn->tempat_lahir }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                        </div>
                                                        <div class="grid-cols-2">
                                                            <label for="tanggal_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                                                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ $dsn->tanggal_lahir }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-900">Jenis Kelamin</label>
                                                            <div class="flex col-span-2">
                                                                <div class="flex items-center mb-6">
                                                                    <input id="Laki-laki" type="radio" name="jenis_kelamin" value="Laki-laki" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300" {{ $dsn->jenis_kelamin == 'Laki-laki' ? 'checked' : '' }}>
                                                                    <label for="Laki-laki" class="block ml-2 mr-6 text-sm font-medium text-gray-900">Laki-laki</label>
                                                                </div>
                                                                <div class="flex items-center mb-6">
                                                                    <input id="Perempuan" type="radio" name="jenis_kelamin" value="Perempuan" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300" {{ $dsn->jenis_kelamin == 'Perempuan' ? 'checked' : '' }}>
                                                                    <label for="Perempuan" class="block ml-2 text-sm font-medium text-gray-900">Perempuan</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="grid-cols-2">
                                                            <label for="jabatan-{{ $dsn->id }}" class="block mb-2 text-sm font-medium text-gray-900">Jabatan</label>
                                                            <select
                                                                name="jabatan"
                                                                id="jabatan-{{ $dsn->id }}"
                                                                class="jabatan-select bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                onchange="toggleProdi(this, '{{ $dsn->id }}')"
                                                            >
                                                                <option disabled {{ $dsn->jabatan == null ? 'selected' : '' }}>Pilih Jabatan</option>
                                                                <option value="Dosen Biasa" {{ $dsn->jabatan == 'Dosen Biasa' ? 'selected' : '' }}>Dosen Biasa</option>
                                                                <option value="Koordinator Program Studi" {{ $dsn->jabatan == 'Koordinator Program Studi' ? 'selected' : '' }}>Koordinator Program Studi</option>
                                                                <option value="Super Admin" {{ $dsn->jabatan == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                                            </select>
                                                        </div>
                                                        <div class="grid-cols-2" id="prodi-container-{{ $dsn->id }}">
                                                            <label for="program_studi_id-{{ $dsn->id }}" class="block mb-2 text-sm font-medium text-gray-900">Program Studi</label>
                                                            <select
                                                                name="program_studi_id"
                                                                id="program_studi_id-{{ $dsn->id }}"
                                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                            >
                                                                <option disabled {{ $dsn->program_studi_id == null ? 'selected' : '' }}>Pilih Program Studi</option>
                                                                @foreach ($programStudi as $prodi)
                                                                    <option value="{{ $prodi->id }}" {{ $dsn->program_studi_id == $prodi->id ? 'selected' : '' }}>
                                                                        {{ $prodi->nama_prodi }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="editModal-{{ $dsn->id }}">
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

                                    <!-- Modal Hapus -->
                                    <div id="popup-modal-{{ $dsn->id }}" tabindex="-1"
                                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50
                                        justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                            <div class="relative p-4 w-full max-w-md max-h-full">
                                            <div class="relative bg-white rounded-lg shadow-sm">
                                                <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent
                                                    hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto
                                                    inline-flex justify-center items-center" data-modal-hide="popup-modal-{{ $dsn->id }}">
                                                    <svg class="w-3 h-3" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                    </svg>
                                                    <span class="sr-only">Close modal</span>
                                                </button>
                                                <div class="p-4 md:p-5 text-center">
                                                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                    </svg>
                                                    <h3 class="mb-5 text-lg font-normal text-gray-500">
                                                        Apakah anda yakin ingin menghapus data dosen ini?
                                                    </h3>
                                                    <form id="delete-form-{{ $dsn->id }}" action="{{ route('dosen.destroy', $dsn->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                        <button type="submit" class="w-full sm:w-20 md:w-20 text-white bg-red-600
                                                            hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300
                                                            font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 justify-center">
                                                            Ya
                                                        </button>
                                                    </form>
                                                    <button data-modal-hide="popup-modal-{{ $dsn->id }}" type="button"
                                                        class="w-full sm:w-20 md:w-20 py-2.5 px-5 ms-3 text-sm font-medium text-gray-900
                                                        focus:outline-none bg-grey-200 rounded-lg border border-gray-200
                                                        hover:bg-gray-500 hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-100 justify-center">
                                                        Tidak
                                                    </button>
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
                {{ $dosen->links() }}
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.jabatan-select').forEach(select => {
            const id = select.dataset.id;
            const prodiContainer = document.getElementById(`prodi-container-${id}`);
            const prodiSelect = document.getElementById(`program_studi_id-${id}`);

            const toggle = () => {
                const isKaprodi = select.value === 'Koordinator Program Studi';
                prodiContainer.style.display = isKaprodi ? 'block' : 'none';
                if (!isKaprodi) prodiSelect.value = '';
            };

            toggle(); // initial
            select.addEventListener('change', toggle); // on change
        });
    });
</script>


@endsection


