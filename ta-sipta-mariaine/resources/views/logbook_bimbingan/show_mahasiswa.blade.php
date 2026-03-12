@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Logbook Bimbingan</h1>
        <x-breadcrumb parent="Bimbingan" item="Logbook Bimbingan {{ $dosen->nama_dosen }}" />
    </div>

    <div class="px-10 py-6 mt-3 p-6 rounded-lg bg-white shadow-md border border-gray-200">
    @include('components.alert-global')

        <table class="w-full md:w-1/2">
            <tbody>
                <tr>
                    <td class="py-1 whitespace-nowrap"><strong>Nama Dosen</strong></td>
                    <td class="py-1 whitespace-nowrap"> : {{ $dosen->nama_dosen ?? 'Dosen tidak ditemukan' }}</td>
                </tr>
                <tr>
                    <td class="py-1 whitespace-nowrap"><strong>Nama Mahasiswa</strong></td>
                    <td class="py-1 whitespace-nowrap"> : {{ $pengajuan->mahasiswa->nama_mahasiswa }}</td>
                </tr>
                <tr>
                    <td class="py-1 whitespace-nowrap"><strong>NIM</strong></td>
                    <td class="py-1 whitespace-nowrap"> : {{ $pengajuan->mahasiswa->nim }}</td>
                </tr>
                <tr>
                    <td class="py-1"><strong>Judul Tugas Akhir</strong></td>
                    <td class="py-1"> : {{ $proposal->revisi_judul_proposal ?? $proposal->judul_proposal ?? 'Belum ada judul tugas akhir' }}</td>
                </tr>
            </tbody>
        </table>

        <div class=" w-full md-w-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-2 mt-4">
            <div>
                @if ($user->role === 'Mahasiswa')
                <a href="{{ route('logbook_bimbingan.index_mahasiswa') }}">
                    <button class="flex items-center mb-2 text-white bg-gray-600 hover:bg-gray-800 font-medium rounded-lg text-sm px-5 py-2.5">
                        <svg class="mr-1 w-4 h-4 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m15 19-7-7 7-7"/>
                        </svg>
                        Kembali
                    </button>
                </a>
                @elseif ($user->role === 'Dosen')
                    <a href="{{ route('pengajuan_pembimbing.index') }}">
                        <button class="flex items-center mb-2 text-white bg-gray-600 hover:bg-gray-800 font-medium rounded-lg text-sm px-5 py-2.5">
                        <svg class="mr-1 w-4 h-4 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m15 19-7-7 7-7"/>
                        </svg>
                        Kembali
                    </button>
                </a>
                @endif
            </div>


            @if ($user->role === 'Dosen')
                @if ($cekLogbook && $logbooks->isNotEmpty())
                    <div class="w-full md:w-auto flex justify-end">
                        <form action="{{ route('logbook_bimbingan.rekomendasi', ['id' => $logbooks->last()->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class=" flex items-center justify-center gap-1 rounded-lg text-white mx-auto text-sm bg-green-600 hover:bg-green-700 font-medium px-5 py-2.5">
                                <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 0 0-1 1H6a2 2 0 0 0-2 2v15a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2h-2a1 1 0 0 0-1-1H9Zm1 2h4v2h1a1 1 0 1 1 0 2H9a1 1 0 0 1 0-2h1V4Zm5.707 8.707a1 1 0 0 0-1.414-1.414L11 14.586l-1.293-1.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                                </svg>
                                Rekomendasikan Sidang TA
                            </button>
                        </form>
                    </div>
                @endif
            @elseif ($user->role === 'Mahasiswa')
                @if ($availablePendaftaranBimbingan->isNotEmpty() && $user->role === 'Mahasiswa')
                    <button data-modal-target="modal" data-modal-toggle="modal" class="text-white bg-blue-600 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
                        Silakan isi logbook bimbingan
                    </button>

                    <!-- Modal -->
                    <div id="modal" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                        <div class="bg-white rounded-lg p-6 w-96 z-50">
                            <h2 class="text-lg font-bold mb-4">Isi Logbook Bimbingan</h2>
                            <form action="{{ route('logbook_bimbingan.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="pendaftaran_bimbingan_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jadwal Bimbingan</label>
                                    <select id="pendaftaran_bimbingan_id" name="pendaftaran_bimbingan_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                        @foreach ($availablePendaftaranBimbingan as $pendaftaran)
                                            <option value="{{ $pendaftaran->id }}">
                                                {{ $pendaftaran->jadwalBimbingan->dosen->nama_dosen }} - [{{ \Carbon\Carbon::parse($pendaftaran->jadwalBimbingan->tanggal)->translatedFormat('d F Y') }} - {{ $pendaftaran->jadwalBimbingan->waktu }}]
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label>Pilih file PDF:</label>
                                    <input type="file" name="file_bimbingan" accept="application/pdf" required>
                                </div>
                                <div class="flex justify-end space-x-2">
                                    <!-- Tombol Batal -->
                                    <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-target="modal" data-modal-toggle="modal">
                                        <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        Batal
                                    </button>

                                    <!-- Tombol Simpan -->
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
                @endif
            @endif
        </div>

        @if ($availablePendaftaranBimbingan->isEmpty() && $user->role === 'Mahasiswa')
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Belum ada jadwal yang Anda daftarkan! Anda tidak bisa mengisikan logbook bimbingan.</span>
            </div>
        @endif

        @if($logbooks->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center mt-4" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                @if($user->role === 'Mahasiswa')
                    <span class="block sm:inline">Anda tidak mempunyai data logbook bimbingan.</span>
                @elseif($user->role === 'Dosen')
                    <span class="block sm:inline">Tidak ada logbook yang diisi oleh mahasiswa bimbingan Anda.</span>
                @elseif($user->role === 'Dosen' && $user->role->dosen->jabatan === 'Koordinator Program Studi')
                    <span class="block sm:inline">Belum ada data logbook bimbingan yang diisi.</span>
                @endif
            </div>
        @else
            <div class="mt-6">
                <div class="overflow-x-auto">
                    <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="w-1 border border-gray-300 px-4 py-2 text-center whitespace-nowrap">No.</th>
                                <th class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">Tanggal</th>
                                <th class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">Permasalahan</th>
                                <th class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logbooks as $logbook)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ \Carbon\Carbon::parse($logbook->pendaftaranBimbingan->jadwalBimbingan->tanggal)->translatedFormat('d F Y') }}
                                    @if($user->role === 'Mahasiswa')
                                        <td class="border border-gray-300 px-4 py-2 text-center">
                                            @if($logbook->permasalahan)
                                                {{ $logbook->permasalahan }}
                                            @else
                                                <span class="text-red-500 italic">Belum disi</span>
                                            @endif
                                        </td>
                                    @elseif ($user->role === 'Dosen')
                                        <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center align-middle">
                                            @if($logbook->permasalahan)
                                                {{ $logbook->permasalahan }}
                                            @else
                                                <button
                                                    class="bg-green-600 hover:bg-green-700 px-2 py-2 flex items-center justify-center gap-1 rounded-lg text-white mx-auto text-sm"
                                                    onclick="document.getElementById('editModal{{ $logbook->id }}').classList.remove('hidden');">
                                                    <svg class="flex items-center mr-1 w-5 h-5 text-white hover:text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                        <path fill-rule="evenodd" d="M8 7V2.221a2 2 0 0 0-.5.365L3.586 6.5a2 2 0 0 0-.365.5H8Zm2 0V2h7a2 2 0 0 1 2 2v.126a5.087 5.087 0 0 0-4.74 1.368v.001l-6.642 6.642a3 3 0 0 0-.82 1.532l-.74 3.692a3 3 0 0 0 3.53 3.53l3.694-.738a3 3 0 0 0 1.532-.82L19 15.149V20a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Z" clip-rule="evenodd"/>
                                                        <path fill-rule="evenodd" d="M17.447 8.08a1.087 1.087 0 0 1 1.187.238l.002.001a1.088 1.088 0 0 1 0 1.539l-.377.377-1.54-1.542.373-.374.002-.001c.1-.102.22-.182.353-.237Zm-2.143 2.027-4.644 4.644-.385 1.924 1.925-.385 4.644-4.642-1.54-1.54Zm2.56-4.11a3.087 3.087 0 0 0-2.187.909l-6.645 6.645a1 1 0 0 0-.274.51l-.739 3.693a1 1 0 0 0 1.177 1.176l3.693-.738a1 1 0 0 0 .51-.274l6.65-6.646a3.088 3.088 0 0 0-2.185-5.275Z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Isi Permasalahan
                                                </button>
                                                <!-- Modal -->
                                                <div id="editModal{{ $logbook->id }}" class="hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full h-full max-h-full bg-black bg-opacity-45">
                                                    <div class="bg-white rounded-lg p-6 w-96 z-50">
                                                        <h2 class="text-lg font-bold mb-4">Isi Permasalahan</h2>
                                                        <form action="{{ route('logbook_bimbingan.update_permasalahan', $logbook->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="mb-4">
                                                                <label for="permasalahan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white text-left">Permasalahan</label>
                                                                <textarea id="permasalahan" name="permasalahan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" rows="3"></textarea>
                                                            </div>
                                                            <div class="flex justify-end gap-2">
                                                                <button type="button" onclick="document.getElementById('editModal{{ $logbook->id }}').classList.add('hidden');" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
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
                                            @endif
                                        </td>
                                    @endif
                                    <td class="w-2 border border-gray-300 px-4 py-2 text-center align-middle whitespace-nowrap">
                                        @if($logbook->file_bimbingan)
                                        <a href="{{ route('logbook_bimbingan.showFile', $logbook->id) }}" target="_blank">
                                            <button class="text-sm bg-blue-500 font-medium px-4 py-2 flex items-center justify-center gap-1  mx-auto rounded-lg text-white whitespace-nowrap">
                                                <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                                </svg>
                                                Lihat File
                                            </button>
                                        @else
                                            Tidak ada file
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
    // Script untuk membuka dan menutup modal
    document.getElementById('openModal').onclick = function() {
        document.getElementById('modal').classList.remove('hidden');
    };
    document.getElementById('closeModal').onclick = function() {
        document.getElementById('modal').classList.add('hidden');
    };
</script>

<script>
    function openModal(fileUrl, fileType) {
        const modal = document.getElementById('fileModal');
        const iframe = document.getElementById('fileFrame');

        // Cek jenis file untuk menampilkan dengan cara yang sesuai
        if (fileType === 'pdf') {
            iframe.src = fileUrl; // Tampilkan PDF langsung
        } else if (fileType === 'doc' || fileType === 'docx') {
            iframe.src = `https://docs.google.com/gview?url=${encodeURIComponent(fileUrl)}&embedded=true`;
        } else if (fileType === 'png' || fileType === 'jpg' || fileType === 'jpeg') {
            iframe.src = fileUrl; // Tampilkan gambar langsung
        } else {
            alert('Format file tidak didukung untuk preview.');
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('fileModal');
        const iframe = document.getElementById('fileFrame');
        iframe.src = ''; // Reset isi
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>

@endsection





