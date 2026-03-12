@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200">
        <h1 class="text-2xl font-bold text-left mb-4 md:mb-0 md:w-auto md:flex-1">Pendaftaran Sidang</h1>
        <x-breadcrumb parent="Pendaftaran Sidang" />
    </div>

    <div class="mt-3 p-4 rounded-md bg-white border border-gray-200">
        @include('components.alert-global')

        <div class="p-6">
            @if (!$rekomendasi)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Anda belum mendapatkan rekomendasi untuk mendaftar sidang tugas akhir dari semua dosen pembimbing Anda.</span>
            </div>
            @else
                @if ($user->role === 'Mahasiswa')
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">Pendaftaran Sidang Tugas Akhir</h2>
                    @if($pendaftaran->isEmpty())
                        {{-- FORM INPUT KALO BELUM ADA DATA --}}
                        <form action="{{ route('pendaftaran_sidang.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid gap-4 mb-4 grid-cols-1 md:grid-cols-2">
                                <div class="grid-cols-2 md:mr-2">
                                    <label for="nama_mahasiwa" class="block mb-2">Nama Mahasiswa</label>
                                    <input type="text" name="nama_mahasiwa" id="nama_mahasiwa" value="{{ $user->mahasiswa->nama_mahasiswa }}" class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                </div>
                                <div class="grid-cols-2 md:ml-2">
                                    <label for="tanggal_pendaftaran" class="block mb-2">Tanggal Pendaftaran</label>
                                    <input type="date" name="tanggal_pendaftaran" id="tanggal_pendaftaran" class="w-full rounded border border-gray-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                </div>
                                <div class="grid-cols-2 md:mr-2">
                                    <label for="file_tugas_akhir" class="block mb-2">File Tugas Akhir (PDF)</label>
                                    <input type="file" name="file_tugas_akhir" id="file_tugas_akhir" class="w-full rounded border border-gray-300 px-4 py-2 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" accept=".pdf" required>
                                </div>
                                <div class="grid-cols-2 md:ml-2">
                                    <label for="file_bebas_pinjaman_administrasi" class="block mb-2">Surat Bebas Pinjaman Administrasi</label>
                                    <input type="file" name="file_bebas_pinjaman_administrasi" id="file_bebas_pinjaman_administrasi" class="w-full rounded border border-gray-300 px-4 py-2 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" accept=".pdf" required>
                                </div>
                                <div class="grid-cols-2 md:mr-2">
                                    <label for="file_slip_pembayaran_semester_akhir" class="block mb-2">Slip Pembayaran Semester Akhir</label>
                                    <input type="file" name="file_slip_pembayaran_semester_akhir" id="file_slip_pembayaran_semester_akhir" class="w-full rounded border border-gray-300 px-4 py-2 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" accept=".pdf" required>
                                </div>
                                <div class="grid-cols-2 md:ml-2">
                                    <label for="file_transkip_sementara" class="block mb-2">Transkip Sementara</label>
                                    <input type="file" name="file_transkip_sementara" id="file_transkip_sementara" class="w-full rounded border border-gray-300 px-4 py-2 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" accept=".pdf" required>
                                </div>
                                <div class="grid-cols-2 md:mr-2">
                                    <label for="file_bukti_pembayaran_sidang_ta" class="block mb-2">Bukti Pembayaran Sidang TA</label>
                                    <input type="file" name="file_bukti_pembayaran_sidang_ta" id="file_bukti_pembayaran_sidang_ta" class="w-full rounded border border-gray-300 px-4 py-2 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" accept=".pdf" required>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2 mb-2">
                                <button type="submit" class=" flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded shadow-md transition duration-300">
                                    <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
                                </svg>
                                Simpan
                                </button>
                            </div>
                        </form>
                    @else
                        @foreach($pendaftaran as $item)
                            <div class="grid gap-4 mb-4 grid-cols-1 md:grid-cols-2">
                                <div class="grid-cols-2 md:mr-2">
                                    <label class="block mb-2">Tanggal Pendaftaran</label>
                                    <input type="text" class="w-full rounded border border-gray-300 px-3 py-2 bg-gray-50 cursor-default" value="{{ \Carbon\Carbon::parse($item->tanggal_pendaftaran)->translatedFormat('d M Y') }}" readonly>
                                </div>

                                {{-- File Tugas Akhir --}}
                                <div class="grid-cols-2 md:ml-2 relative">
                                    <label class="block mb-2">File Tugas Akhir (PDF)</label>
                                    <input type="text" class="w-full rounded border border-gray-300 px-3 py-2 bg-gray-50 pr-10 cursor-default" value="{{ $item->file_tugas_akhir ?? 'Belum ada file' }}" readonly>
                                    @if ($item->file_tugas_akhir)
                                        <a href="{{ route('pendaftaran_sidang.showFile', ['id' => $item->id, 'fileField' => 'file_tugas_akhir']) }}" target="_blank" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 mt-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>

                                {{-- Surat Bebas Pinjaman Administrasi --}}
                                <div class="grid-cols-2 md:mr-2 relative">
                                    <label class="block mb-2">Surat Bebas Pinjaman Administrasi</label>
                                    <input type="text" class="w-full rounded border border-gray-300 px-3 py-2 bg-gray-50 pr-10 cursor-default" value="{{ $item->file_bebas_pinjaman_administrasi ?? 'Belum ada file' }}" readonly>
                                    @if ($item->file_bebas_pinjaman_administrasi)
                                        <a href="{{ route('pendaftaran_sidang.showFile', ['id' => $item->id, 'fileField' => 'file_bebas_pinjaman_administrasi']) }}" target="_blank" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 mt-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>

                                {{-- Slip Pembayaran Semester Akhir --}}
                                <div class="grid-cols-2 md:ml-2 relative">
                                    <label class="block mb-2">Slip Pembayaran Semester Akhir</label>
                                    <input type="text" class="w-full rounded border border-gray-300 px-3 py-2 bg-gray-50 pr-10 cursor-default" value="{{ $item->file_slip_pembayaran_semester_akhir ?? 'Belum ada file' }}" readonly>
                                    @if ($item->file_slip_pembayaran_semester_akhir)
                                        <a href="{{ route('pendaftaran_sidang.showFile', ['id' => $item->id, 'fileField' => 'file_slip_pembayaran_semester_akhir']) }}" target="_blank" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 mt-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>

                                {{-- Transkip Sementara --}}
                                <div class="grid-cols-2 md:mr-2 relative">
                                    <label class="block mb-2">Transkip Sementara</label>
                                    <input type="text" class="w-full rounded border border-gray-300 px-3 py-2 bg-gray-50 pr-10 cursor-default" value="{{ $item->file_transkip_sementara ?? 'Belum ada file' }}" readonly>
                                    @if ($item->file_transkip_sementara)
                                        <a href="{{ route('pendaftaran_sidang.showFile', ['id' => $item->id, 'fileField' => 'file_transkip_sementara']) }}" target="_blank" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 mt-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                                {{-- Bukti Pembayaran Sidang TA --}}
                                <div class="grid-cols-2 md:ml-2 relative">
                                    <label class="block mb-2">Bukti Pembayaran Sidang TA</label>
                                    <input type="text" class="w-full rounded border border-gray-300 px-3 py-2 bg-gray-50 pr-10 cursor-default" value="{{ $item->file_bukti_pembayaran_sidang_ta ?? 'Belum ada file' }}" readonly>
                                    @if ($item->file_bukti_pembayaran_sidang_ta)
                                        <a href="{{ route('pendaftaran_sidang.showFile', ['id' => $item->id, 'fileField' => 'file_bukti_pembayaran_sidang_ta']) }}" target="_blank" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 mt-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                @endif
            @endif
        </div>
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



