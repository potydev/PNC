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
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Riwayat Sidang</h1>
        <x-breadcrumb parent="Hasil Sidang TA" />
    </div>
    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
        @if ($riwayatSidang->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Anda belum memiliki riwayat sidang</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <h2 class="text-lg font-bold mb-4">Informasi Riwayat Sidang Mahasiswa</h2>
                <table class="table-auto w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100 text-center">
                            <th class="w-1 border border-gray-300 px-4 py-2 whitespace-nowrap">No</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Tanggal</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Jenis Sidang</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Waktu</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Ruangan</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Status Sidang</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayatSidang as $riwayat)
                            <tr class="border-t">
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($riwayat->tanggal)->translatedFormat('d F Y') }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $riwayat->jadwalSidangTugasAkhir->jenis_sidang }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $riwayat->jadwalSidangTugasAkhir->waktu_mulai }} - {{ $riwayat->jadwalSidangTugasAkhir->waktu_selesai }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $riwayat->jadwalSidangTugasAkhir->ruanganSidang->nama_ruangan ?? '-' }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $riwayat->status_sidang ?? '-' }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                    @if ($riwayat->punya_catatan_revisi_ta)
                                        <a href="{{ route('penilaian_ta.catatan.gabung', $riwayat->jadwal_sidang_tugas_akhir_id) }}" target="_blank"
                                            class="inline-flex text-sm items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                            <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                                            </svg>
                                            Catatan Dosen
                                        </a>
                                    @else
                                        <button type="button"
                                            class="inline-flex text-sm items-center gap-2 bg-gray-300 hover:bg-gray-500 text-gray-600 font-semibold px-4 py-2 rounded cursor-not-allowed"
                                            disabled>
                                            <svg class="w-5 h-5 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd" />
                                            </svg>
                                            Catatan Dosen
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <hr class="border border-gray-300 mb-6 mt-6">
                <h2 class="mb-2 text-lg font-bold">Revisi File Tugas Akhir</h2>
                @if (!$hasilSidang->file_revisi)
                    {{-- <form action="{{ route('hasil_sidang.upload_revisi', $hasilSidang->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="file_revisi">Upload File Revisi (PDF):</label>
                        <input type="file" name="file_revisi" accept="application/pdf" required>
                        <button type="submit" class="mt-2 bg-blue-600 text-white px-4 py-1.5 rounded">Unggah</button>
                    </form> --}}
                    <div class="w-full lg:w-1/2">
                        <form action="{{ route('hasil_sidang.upload_revisi', $hasilSidang->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <label class="block mb-2 text-sm font-medium text-gray-700">Unggah File Revisi (PDF)</label>

                            <div class="flex items-center gap-2 overflow-hidden">
                                <!-- Input File -->
                                <input type="file" name="file_revisi" accept="application/pdf"
                                    class="flex-1 min-w-0 max-w-full text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 px-2 py-1"
                                    required>

                                <!-- Tombol Unggah -->
                                <button type="submit"
                                    class="shrink-0 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Unggah
                                </button>
                            </div>
                        </form>
                    </div>

                @else
                    <table>
                        <tr>
                            <td class="whitespace-nowrap">Tanggal Upload Revisi :</td>
                            <td>{{ \Carbon\Carbon::parse($hasilSidang->tanggal_revisi)->translatedFormat('d F Y')  }}</td>
                        </tr>
                    </table>
                    <a href="{{ route('hasil_sidang.show_file_revisi', $hasilSidang->id) }}" target="_blank"
                    class="mt-2 inline-flex text-sm items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                    <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                    </svg>Lihat File</a>
                @endif
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

@endsection

