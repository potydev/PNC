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
        <x-breadcrumb parent="Hasil Sidang" item="Riwayat Sidang" />
    </div>
    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
        <p class="mb-4">
            <strong>Nama Mahasiswa:</strong> {{ $hasilSidang->mahasiswa->nama_mahasiswa }} <br>
            <strong>Status Kelulusan:</strong> {{ $hasilSidang->status_kelulusan }} <br>
            <strong>Tahun Lulus:</strong> {{ $hasilSidang->tahun_lulus ?? 'Belum Lulus'}}
        </p>

        <div class="overflow-x-auto">
            <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr class="text-center">
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">No.</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Jadwal Sidang</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Jenis Sidang</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Status Sidang</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap" colspan="4">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($hasilSidang->riwayatSidang as $riwayat)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($riwayat->jadwalSidangTugasAkhir->tanggal)->translatedFormat('d F Y') }}</td>
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $riwayat->jadwalSidangTugasAkhir->jenis_sidang }}</td>
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $riwayat->status_sidang }}</td>
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

                            <td class="w-2 border border-gray-300 px-4 py-2 whitespace-nowrap">
                                <a href="{{ route('penilaian_ta.gabung', $riwayat->jadwal_sidang_tugas_akhir_id) }}" target="_blank"
                                    class="inline-flex text-sm items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                    <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                                    </svg>
                                    Penilaian Dosen
                                </a>
                            </td>
                            <td class="w-2 border border-gray-300 px-4 py-2 whitespace-nowrap">
                                <a href="{{ route('penilaian_ta.lihat_nilai', $riwayat->jadwal_sidang_tugas_akhir_id) }}" target="_blank"
                                    class="inline-flex text-sm items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
                                     <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                                    </svg>
                                    Lihat Nilai
                                </a>
                            </td>
                            <td class="w-2 border border-gray-300 px-4 py-2 whitespace-nowrap">
                                <a href="{{ route('berita_acara.sidang-tugas-akhir.lihat', [
                                'jenis_sidang' => $riwayat->jadwalSidangTugasAkhir->jenis_sidang,
                                'jadwal_id' => $riwayat->jadwal_sidang_tugas_akhir_id, 'mahasiswa_id' => $riwayat->jadwalSidangTugasAkhir->mahasiswa->id]) }}" target="_blank"
                                    class="inline-flex text-sm items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                                    <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                                    </svg>
                                    Berita Acara - {{ $riwayat->jadwalSidangTugasAkhir->jenis_sidang }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a href="{{ route('hasil_sidang.tugas_akhir.index') }}" class="inline-flex gap-1 items-center mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            <svg class="w-4 h-4 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m15 19-7-7 7-7"/>
            </svg>
            Kembali
        </a>
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

