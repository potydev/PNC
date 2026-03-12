@extends('layouts.app')


@section('content')
<div class="p-4 sm:ml-64">
    <div class="mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Logbook Bimbingan {{ $mahasiswa->nama_mahasiswa }}</h1>
        <x-breadcrumb parent="Bimbingan" item="Logbook Bimbingan {{ $mahasiswa->nama_mahasiswa }}" />
    </div>
    <div class="mt-3 p-6 rounded-lg bg-white shadow-md border border-gray-200">
        <a href="{{ route('pengajuan_pembimbing.index') }}">
            <button class="flex items-center mb-2 text-white bg-gray-600 hover:bg-gray-800 font-medium rounded-lg text-sm px-5 py-2.5">
                <svg class="mr-1 w-4 h-4 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m15 19-7-7 7-7"/>
                </svg>
                Kembali
            </button>
        </a>
        @if($logbooks->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center mt-4" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Belum ada data logbook bimbingan yang diisi oleh {{ $mahasiswa->nama_mahasiswa }}.</span>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full mt-2 border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="w-1 border border-gray-300 px-4 py-2 text-center">No.</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Tanggal</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Pembimbing</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Permasalahan</th>
                        <th class="w-2 border border-gray-300 px-4 py-2 text-center">File</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logbooks as $logbook)
                        <tr>
                            <td class="w-1 border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($logbook->pendaftaranBimbingan->jadwalBimbingan->tanggal)->translatedFormat('d F Y') }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                {{ $logbook->pendaftaranBimbingan->jadwalBimbingan->dosen->nama_dosen }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                @if($logbook->permasalahan)
                                    {{ $logbook->permasalahan }}
                               @else
                                    <span class="text-red-500 italic">Belum disi</span>
                                @endif
                            </td>
                            <td class="w-2 border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                @if($logbook->file_bimbingan)
                                    <a href="{{ route('logbook_bimbingan.showFile', $logbook->id) }}" target="_blank">
                                        <button class="text-sm bg-blue-500 font-medium px-4 py-2 flex items-center justify-center gap-1  mx-auto rounded-lg text-white whitespace-nowrap">
                                            <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                            </svg>
                                            Lihat File
                                        </button>
                                    </a>
                                @else
                                    Tidak ada file
                                @endif
                            </td>
                        </tr>

                    @endforeach
                </tbody>
                <!-- Modal -->
                <div id="fileModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 items-center justify-center">
                    <div class="bg-white rounded-lg shadow-lg w-11/12 md:w-3/4 lg:w-1/2 h-[90%] flex flex-col">
                        <div class="flex justify-between items-center p-4 border-b">
                            <h3 class="text-lg font-semibold">Preview File</h3>
                            <button onclick="closeModal()" class="text-red-500 hover:text-red-700">&times;</button>
                        </div>
                        <div class="flex-grow overflow-hidden">
                            <iframe id="fileFrame" class="w-full h-full" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </table>
        </div>
        @endif
    </div>
</div>

<script>
    // Script untuk membuka dan menutup modal
    document.getElementById('openModal').onclick = function() {
        document.getElementById('modal').classList.remove('hidden');
    };
    document.getElementById('closeModal').onclick = function() {
        document.getElementById('modal').classList.add('hidden');
    };
</script>

@endsection





