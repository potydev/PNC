
@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1 whitespace-nowrap">Data Pendaftaran Sidang</h1>
        <x-breadcrumb parent="Pendaftaran Sidang" />
    </div>
    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
        <form method="GET" action="{{ route('pendaftaran_sidang.dropdown_search') }}" id="filterForm" class="w-full max-w-sm mb-4">
            <label for="tahun" class="block mb-2 text-sm font-medium text-gray-900">Pilih Tahun Pendaftaran</label>
            <select name="tahun" id="tahun"
                class="w-full p-2.5 border border-gray-300 rounded-lg"
                onchange="document.getElementById('filterForm').submit()">
                <option value="">Semua Tahun</option>
                @foreach ($tahunList as $thn)
                    <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>
                        {{ $thn }}
                    </option>
                @endforeach
            </select>
        </form>

        <!-- Main modal -->
        @if($pendaftaran->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Belum ada mahasiswa yang mendaftar sidang tugas akhir.</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr class="text-center">
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">No.</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nama Mahasiswa</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Tanggal Pendaftaran</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">File Tugas Akhir</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">File Bebas Administrasi</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Slip Pembayaran Semester Akhir</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Transkip Sementara</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Bukti Pembayaran Sidang TA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaran as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $item->mahasiswa->nama_mahasiswa }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pendaftaran)->translatedFormat('d F Y') }}
                                </td>

                                {{-- File Tugas Akhir --}}
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    @if ($item->file_tugas_akhir)
                                        <a href="{{ route('pendaftaran_sidang.showFile', ['id' => $item->id, 'fileField' => 'file_tugas_akhir']) }}" target="_blank">
                                            <button class="text-sm bg-blue-500 font-medium px-4 py-2 flex items-center justify-center gap-1 mx-auto rounded-lg text-white whitespace-nowrap">
                                                <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                                </svg>
                                                Lihat File TA
                                            </button>
                                        </a>
                                    @else
                                        <span class="text-gray-500">Belum ada file</span>
                                    @endif
                                </td>

                                {{-- Surat Bebas Pinjaman Administrasi --}}
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    @if ($item->file_bebas_pinjaman_administrasi)
                                        <a href="{{ route('pendaftaran_sidang.showFile', ['id' => $item->id, 'fileField' => 'file_bebas_pinjaman_administrasi']) }}" target="_blank">
                                            <button class="text-sm bg-blue-500 font-medium px-4 py-2 flex items-center justify-center gap-1  mx-auto rounded-lg text-white whitespace-nowrap">
                                                <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                                </svg>
                                                Lihat File Bebas Administrasi
                                            </button>
                                        </a>
                                    @else
                                        <span class="text-gray-500">Belum ada file</span>
                                    @endif
                                </td>

                                {{-- Slip Pembayaran Semester Akhir --}}
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap align-middle">
                                    @if ($item->file_slip_pembayaran_semester_akhir)
                                        <a href="{{ route('pendaftaran_sidang.showFile', ['id' => $item->id, 'fileField' => 'file_slip_pembayaran_semester_akhir']) }}" target="_blank">
                                            <button class="text-sm bg-blue-500 font-medium px-4 py-2 flex items-center justify-center gap-1 mx-auto rounded-lg text-white whitespace-nowrap">
                                                <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                                </svg>
                                                Lihat Slip Pembayaran
                                            </button>
                                        </a>
                                    @else
                                        <span class="text-gray-500">Belum ada file</span>
                                    @endif
                                </td>

                                {{-- Transkip Sementara --}}
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap align-middle">
                                    @if ($item->file_transkip_sementara)
                                        <a href="{{ route('pendaftaran_sidang.showFile', ['id' => $item->id, 'fileField' => 'file_transkip_sementara']) }}" target="_blank">
                                            <button class="text-sm bg-blue-500 font-medium px-4 py-2 flex items-center justify-center gap-1  mx-auto rounded-lg text-white whitespace-nowrap">
                                                <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                                </svg>
                                                Lihat Transkip
                                            </button>
                                        </a>
                                    @else
                                        <span class="text-gray-500">Belum ada file</span>
                                    @endif
                                </td>

                                {{-- Bukti Pembayaran Sidang TA --}}
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap align-middle">
                                    @if ($item->file_bukti_pembayaran_sidang_ta)
                                        <a href="{{ route('pendaftaran_sidang.showFile', ['id' => $item->id, 'fileField' => 'file_bukti_pembayaran_sidang_ta']) }}" target="_blank">
                                            <button class="text-sm bg-blue-500 font-medium px-4 py-2 flex items-center justify-center gap-1  mx-auto rounded-lg text-white whitespace-nowrap">
                                                <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                                </svg>
                                                Lihat Bukti Pembayaran
                                            </button>
                                        </a>
                                    @else
                                        <span class="text-gray-500">Belum ada file</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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


