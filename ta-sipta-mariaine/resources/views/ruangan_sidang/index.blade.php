@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Data Ruangan Sidang</h1>
        <x-breadcrumb parent="Data Master" item="Ruangan Sidang" />
    </div>
    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
        @include('components.alert-global')

        <!-- Modal toggle -->
        <div class="flex justify-between mb-4 flex-wrap">
            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="focus:outline-none text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-4">
                <svg class="w-7 h-7 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                </svg>
                Tambah Ruangan Sidang
            </button>
            <div class="flex justify-end mb-4 flex-grow">
                <form action="{{ route('ruangan_sidang.search') }}" method="GET" class="max-w-md  w-full" id="search-form">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="search" id="search-input" name="search"
                            class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Cari data ruangan sidang disini"
                            required value="{{ request('search') }}"
                            oninput="document.querySelector('#search-form').submit();" />
                    </div>
                </form>
            </div>
        </div>

        <!-- Main modal -->
        <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Form Tambah Ruangan Sidang
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form action="{{ route('ruangan_sidang.store') }}" method="POST" class="p-4 md:p-5">
                        @csrf
                        <div class="grid gap-4 mb-4 grid-cols-2">
                            <div class="col-span-2">
                                <label for="nama_ruangan" class="block mb-2 text-sm font-medium text-gray-900">Nama Ruangan</label>
                                <input type="text" name="nama_ruangan" id="nama_ruangan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Lab. Pemrograman Lanjut" required />
                            </div>
                            <div class="col-span-2">
                                <label for="tempat" class="block mb-2 text-sm font-medium text-gray-900">Tempat</label>
                                <input type="text" name="tempat" id="tempat" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Lantai 3 GKB" required />
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

        @if($ruanganSidang->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Tidak ada data ruangan sidang yang bisa ditampilkan. Silakan tambahkan data ruangan sidang.</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr class="text-center">
                            <th class="w-0.5/12 border border-gray-300 px-4 py-2">No.</th>
                            <th class="w-6/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Ruangan Sidang</th>
                            <th class="w-6/12 border border-gray-300 px-4 py-2 whitespace-nowrap">Tempat</th>
                            <th class="w-1/12 border border-gray-300 px-4 py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ruanganSidang as $ruangan)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2 text-center">{{ $loop->iteration + ($ruanganSidang->currentPage() - 1) * $ruanganSidang->perPage() }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $ruangan->nama_ruangan }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $ruangan->tempat }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <div class="flex justify-center space-x-2">
                                        <button data-modal-target="editModal-{{ $ruangan->id }}" data-modal-toggle="editModal-{{ $ruangan->id }}" class="text-sm flex items-center justify-center w-full px-3 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-600 transition duration-200">
                                            <svg class="mr-0.5 w-4 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                            </svg>
                                            Edit
                                        </button>

                                          <button data-modal-target="popup-modal-{{ $ruangan->id }}" data-modal-toggle="popup-modal-{{ $ruangan->id }}"
                                            class="text-sm flex items-center justify-center w-full px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">
                                            <svg class="mr-0.5 w-4 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>

                                    <!-- Modal Edit -->
                                    <div id="editModal-{{ $ruangan->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                        <div class="relative p-4 w-full max-w-md max-h-full">
                                            <!-- Modal content -->
                                            <div class="relative bg-white rounded-lg shadow-sm">
                                                <!-- Modal header -->
                                                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                                    <h3 class="text-lg font-semibold text-gray-900">
                                                        Form Edit Ruangan Sidang
                                                    </h3>
                                                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="editModal-{{ $ruangan->id }}">
                                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                        </svg>
                                                        <span class="sr-only">Close modal</span>
                                                    </button>
                                                </div>
                                                <!-- Modal body -->
                                                <form action="{{ route('ruangan_sidang.update', $ruangan->id) }}" method="POST" class="p-4 md:p-5">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="grid gap-4 mb-4 grid-cols-2">
                                                        <div class="col-span-2">
                                                            <label for="edit_nama_ruangan" class="block mb-2 text-sm font-medium text-gray-900">Nama Ruangan</label>
                                                            <input type="text" name="nama_ruangan" id="edit_nama_ruangan" value="{{ $ruangan->nama_ruangan }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="22001" required />
                                                        </div>
                                                        <div class="col-span-2">
                                                            <label for="edit_tempat" class="block mb-2 text-sm font-medium text-gray-900">Tempat</label>
                                                            <input type="text" name="tempat" id="edit_tempat" value="{{ $ruangan->tempat }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="22001" required />
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-end space-x-2">
                                                        <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="editModal-{{ $ruangan->id }}">
                                                            <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                            </svg>
                                                            Batal
                                                        </button>
                                                        <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                                            <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                                                <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>                                </svg>
                                                            Simpan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Hapus -->
                                    <div id="popup-modal-{{ $ruangan->id }}" tabindex="-1"
                                        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50
                                        justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                        <div class="relative p-4 w-full max-w-md max-h-full">
                                            <div class="relative bg-white rounded-lg shadow-sm">
                                                <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent
                                                    hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto
                                                    inline-flex justify-center items-center" data-modal-hide="popup-modal-{{ $ruangan->id }}">
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
                                                        Apakah anda yakin ingin menghapus data ruangan sidang ini?
                                                    </h3>
                                                    <form id="delete-form-{{ $ruangan->id }}" action="{{ route('ruangan_sidang.destroy', $ruangan->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                        <button type="submit" class="w-full sm:w-20 md:w-20 text-white bg-red-600
                                                            hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300
                                                            font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 justify-center">
                                                            Ya
                                                        </button>
                                                    </form>
                                                    <button data-modal-hide="popup-modal-{{ $ruangan->id }}" type="button"
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
                {{ $ruanganSidang->links() }} <!-- Ini akan menghasilkan pagination -->
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


