@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200">
        <h1 class="text-2xl font-bold text-left mb-4 md:mb-0 md:w-auto md:flex-1">Pengajuan Proposal</h1>
        <x-breadcrumb parent="Pengajuan Proposal" />
    </div>

    <div class="px-10 py-6 mt-3 rounded-md bg-white border border-gray-200">
        @include('components.alert-global')

        @if($proposal->isEmpty())
            @if ($user->role === 'Mahasiswa')
                <!-- Modal toggle -->
                <div class="flex justify-between mb-4 flex-wrap">
                    <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="focus:outline-none text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-4">
                        <svg class="w-7 h-7 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Unggah Proposal
                    </button>
                </div>
                <!-- Main modal -->
                <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                    <div class="relative p-4 w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow-sm">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Unggah Proposal
                                </h3>
                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <form action="{{ route('proposal.store') }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-5">
                                @csrf
                                <div class="grid gap-4 mb-4 grid-cols-2">
                                    <div class="col-span-2">
                                        <label for='mahasiswa_id' class="block mb-2 text-sm font-medium text-gray-900">Mahasiswa</label>
                                        <input type="text" name="mahasiswa_id" id="mahasiswa_id" value="{{ auth()->user()->mahasiswa->nama_mahasiswa }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" readonly />
                                    </div>
                                    <div class="col-span-2">
                                        <label for="judul_proposal" class="block mb-2 text-sm font-medium text-gray-900">Judul Proposal Tugas Akhir</label>
                                        <input type="text" name="judul_proposal" id="judul_proposal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                                    </div>
                                    <div class="mb-4">
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Unggah File</label>
                                        <input class="text-sm" type="file" name="file_proposal" accept="application/pdf" required>
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
                                            <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>                                </svg>
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Anda belum mengunggah proposal!</span>
                </div>
            @endif
        @endif

        @if ($user->role === 'Mahasiswa')
            @if($proposal)
                @foreach ($proposal as $item)
                    @if ($punyaCatatanRevisi && (empty($item->revisi_judul_proposal) || empty($item->revisi_file_proposal)))
                            <div class="mb-4 w-full flex justify-end">
                                <div class="w-42">
                                    <button data-modal-target="editModal-{{ $item->id }}" data-modal-toggle="editModal-{{ $item->id }}" class="text-sm flex items-center justify-center w-full px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-200 whitespace-nowrap">
                                        <svg class="mr-0.5 w-4 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                        </svg>
                                        Unggah Revisi Anda
                                    </button>
                                </div>
                                <!-- Modal Edit -->
                                <div id="editModal-{{ $item->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                    <div class="relative p-4 w-full max-w-md max-h-full">
                                    <!-- Modal content -->
                                        <div class="relative bg-white rounded-lg shadow-sm">
                                            <!-- Modal header -->
                                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    Unggah Revisi Anda
                                                </h3>
                                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="editModal-{{ $item->id }}">
                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                    </svg>
                                                    <span class="sr-only">Close modal</span>
                                                </button>
                                            </div>
                                            <!-- Modal body -->
                                            <form action="{{ route('proposal.updateRevisi', $item->id) }}" method="POST" enctype="multipart/form-data" class="p-4 md:p-5">
                                                @csrf
                                                <div class="grid gap-4 mb-4 grid-cols-2">
                                                    <div class="col-span-2">
                                                        <label for="revisi_judul_proposal" class="block mb-2 text-sm font-medium text-gray-900">Revisi Judul Proposal Tugas Akhir</label>
                                                        <input type="text" name="revisi_judul_proposal" id="revisi_judul_proposal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block mb-2 text-sm font-medium text-gray-900">Unggah File Revisi</label>
                                                        <input class="text-sm" type="file" name="revisi_file_proposal" accept="application/pdf" required>
                                                    </div>
                                                </div>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="editModal-{{ $item->id }}">
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
                    @endif
                        <div class="mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">Tanggal Pengajuan</label>
                            <input type="text" value="{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 cursor-not-allowed" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Nama Lengkap</label>
                            <input type="text" class="mt-1 block w-full p-2 border border-gray-300 rounded bg-gray-50" value="{{ $item->mahasiswa->nama_mahasiswa }}" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Judul Proposal</label>
                            <input type="text" class="mt-1 block w-full p-2 border border-gray-300 rounded bg-gray-50" value="{{ $item->judul_proposal }}" readonly>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">File Proposal</label>
                            <div class="flex items-center gap-2 flex-wrap mt-2">
                                @if($item->file_proposal)
                                    <a href="{{ route('proposal.showFileProposal', $item->id) }}" target="_blank">
                                        <button type="button" class="text-sm bg-blue-500 font-medium px-4 py-2 flex items-center justify-center gap-1 rounded-lg text-white whitespace-nowrap">
                                            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                            </svg>
                                            Lihat Proposal
                                        </button>
                                    </a>
                                @else
                                    <span class="text-red-500 ml-2">Tidak ada file</span>
                                @endif

                                <form id="upload-form-{{ $item->id }}" action="{{ route('proposal.updateFile', $item->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="file" name="file_proposal" id="file-proposal-{{ $item->id }}" accept="application/pdf" style="display: none;" onchange="document.getElementById('upload-form-{{ $item->id }}').submit();">
                                    <button type="button" onclick="document.getElementById('file-proposal-{{ $item->id }}').click();"
                                        class="text-sm bg-yellow-500 hover:bg-yellow-600 font-medium px-4 py-2 flex items-center justify-center gap-1 rounded-lg text-white whitespace-nowrap">
                                        <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16 10 3-3m0 0-3-3m3 3H5v3m3 4-3 3m0 0 3 3m-3-3h14v-3"/>
                                        </svg>
                                        Unggah Ulang
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if ($item->revisi_judul_proposal || $item->revisi_file_proposal)
                            <div class="mb-4">
                                <label class="block text-gray-700">Revisi Judul Proposal</label>
                                <input type="text" class="mt-1 block w-full p-2 border border-gray-300 rounded bg-gray-50" value="{{ $item->revisi_judul_proposal }}" readonly>
                            </div>
                            <div class="mb-4">
                                <label class="block text-gray-700">Revisi File Proposal</label>
                                @if($item->revisi_file_proposal)
                                    <a href="{{ route('proposal.showFileProposalRevisi', $item->id) }}" target="_blank">
                                        <button class="mt-2 text-sm bg-blue-500 font-medium px-4 py-2 flex items-center justify-center gap-1 rounded-lg text-white whitespace-nowrap">
                                            <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                            </svg>
                                            Lihat Revisi Proposal
                                        </button>
                                    </a>
                                @else
                                    <span class="text-red-500 ml-2">Tidak ada file</span>
                                @endif
                            </div>
                        @endif
                @endforeach
            @else
            @endif
        @elseif($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi' || $user->role === 'Dosen' && $user->dosen->jabatan === 'Super Admin')
            @if($proposal->isEmpty())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Tidak ada data proposal mahasiswa!</span>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr class="text-center">
                                <th class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">No.</th>
                                <th class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">Nama Mahasiswa</th>
                                <th class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">Judul Proposal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">File Proposal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">Revisi Judul Proposal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">Revisi File Proposal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposal as $p)
                                <tr class="hover:bg-gray-50">
                                    <td class="w-1 border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration + ($proposal->currentPage() - 1) * $proposal->perPage() }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $p->mahasiswa->nama_mahasiswa }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $p->judul_proposal }}</td>
                                    <td class="w-2 border border-gray-300 px-4 py-2 whitespace-nowrap">
                                        @if($p->file_proposal)
                                            <a href="{{ route('proposal.showFileProposal', $p->id) }}" target="_blank">
                                                <button class="text-sm bg-blue-500 hover:bg-blue-600 font-medium px-4 py-2 transition duration-200 flex items-center justify-center gap-1 rounded-lg text-white mx-auto">
                                                    <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                                    </svg>
                                                    Lihat Proposal
                                                </button>
                                            </a>
                                        @else
                                            <span class="italic text-red-500 ml-2 whitespace-nowrap">Tidak ada file</span>
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">
                                        @if ($p->revisi_judul_proposal)
                                            {{ $p->revisi_judul_proposal }}
                                        @else
                                            <span class="italic text-red-500 ml-2 whitespace-nowrap">Belum ada revisi</span>
                                        @endif
                                    </td>
                                    <td class="w-2 border border-gray-300 px-4 py-2 whitespace-nowrap text-center">
                                        @if($p->revisi_file_proposal)
                                            <a href="{{ route('proposal.showFileProposalRevisi', $p->id) }}" target="_blank">
                                                <button class="text-sm bg-blue-500 hover:bg-blue-600 font-medium px-4 py-2 transition duration-200 flex items-center justify-center gap-1 rounded-lg text-white mx-auto">
                                                    <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                                    </svg>
                                                    Lihat Revisi
                                                </button>
                                            </a>
                                        @else
                                            <span class="italic text-red-500 whitespace-nowrap">Belum ada revisi</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Page navigation example">
                    {{ $proposal->links() }} <!-- Ini akan menghasilkan pagination -->
                </nav>
            @endif
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



