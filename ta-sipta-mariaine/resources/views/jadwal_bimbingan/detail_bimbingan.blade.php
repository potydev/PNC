@extends('layouts.app')

@section('content')
    <div class="p-4 sm:ml-64">
        <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
            <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Detail Pendaftar Bimbingan</h1>
            <x-breadcrumb parent="Bimbingan" item="Detail Pendaftar Bimbingan" />
        </div>
        <div class="px-10 py-8 mt-3 p-5 max-h-[500px] overflow-y-auto rounded-md bg-white border border-gray-200">
            @include('components.alert-global')
            @if($jadwal->pendaftaranBimbingan && $jadwal->pendaftaranBimbingan->count() > 0)
            {{-- @if($jadwal->pendaftaranBimbingan->isEmpty()) --}}
            <table class="table-auto w-full border-collapse border border-gray-300 mb-4">
                    <thead class="bg-gray-100">
                        <tr class="text-center">
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">No</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">Nama Mahasiswa</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">NIM</th>
                            @if($user->role === 'Dosen' && ($user->dosen->jabatan === 'Koordinator Program Studi' || $user->dosen->jabatan === 'Super Admin' ))
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">Status</th>
                            @elseif($user->role === 'Dosen' && $user->dosen->jabatan === 'Dosen Biasa')
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">Aksi</th>
                            @endif
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwal->pendaftaranBimbingan as $pendaftaran)
                        <tr class="text-center">
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">{{ $loop->iteration }}</td>
                            <td class="border px-4 py-2">{{ $pendaftaran->mahasiswa->nama_mahasiswa }}</td>
                            <td class="border px-4 py-2">{{ $pendaftaran->mahasiswa->nim }}</td>

                            {{-- @if($user->role === 'Dosen' && ($user->dosen->jabatan === 'Koordinator Program Studi' || $user->dosen->jabatan === 'Super Admin' ))
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">
                                    {{ $pendaftaran->status_pendaftaran ?? 'Menunggu' }}
                                </td> --}}
                            {{-- @if($user->role === 'Dosen' && $user->dosen->jabatan === 'Dosen Biasa') --}}
                                <td class="w-2 border border-gray-300 px-4 py-2 whitespace-nowrap text-center">
                                    @if(is_null($pendaftaran->status_pendaftaran))
                                        <form action="{{ route('jadwal_bimbingan.konfirmasi', [$jadwal->id, $pendaftaran->id]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" name="aksi" value="terima" class="text-sm px-3 py-2 w-20 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">Terima</button>
                                            <button type="submit" name="aksi" value="tolak" class="text-sm px-3 py-2 w-20 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">Tolak</button>
                                        </form>
                                    @else
                                        <span class="font-semibold {{
                                            $pendaftaran->status_pendaftaran === 'Diterima' ? 'text-green-600' :
                                            ($pendaftaran->status_pendaftaran === 'Ditolak' ? 'text-red-600' : 'text-gray-600')
                                        }}">
                                            {{ $pendaftaran->status_pendaftaran }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom waktu (jika statusnya diterima) --}}
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap text-center">
                                    @if($pendaftaran->status_pendaftaran === 'Diterima')
                                        @php
                                            $pendaftarDiterima = $jadwal->pendaftaranBimbingan
                                                ->where('status_pendaftaran', 'Diterima')
                                                ->sortBy('created_at')
                                                ->values();

                                            $antrian = $pendaftarDiterima->search(fn($p) => $p->mahasiswa_id == $pendaftaran->mahasiswa_id);
                                            $durasi = $jadwal->durasi ?? 30;
                                            $waktuMulai = \Carbon\Carbon::parse($jadwal->waktu)->addMinutes($durasi * $antrian);
                                        @endphp
                                        {{ $waktuMulai->format('H:i') }}
                                    @else
                                        Belum Ada
                                    @endif
                                </td>
                            {{-- @endif --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Belum ada daftar mahasiswa yang mendaftar bimbingan ini.</span>
                </div>
            @endif
            {{-- @if($user->role === 'Dosen' && ($user->dosen->jabatan === 'Koordinator Program Studi' || $user->dosen->jabatan === 'Super Admin' ))
                <a href="{{ route('jadwal_bimbingan.index_kaprodi') }}">
                    <button class="flex items-center mb-2 text-white bg-gray-600 hover:bg-gray-800 font-medium rounded-lg text-sm px-5 py-2.5">
                        <svg class="mr-1 w-4 h-4 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m15 19-7-7 7-7"/>
                        </svg>
                        Kembali
                    </button>
                </a>
            @else --}}
                <a href="{{ route('jadwal_bimbingan.index') }}">
                    <button class="flex items-center mb-2 text-white bg-gray-600 hover:bg-gray-800 font-medium rounded-lg text-sm px-5 py-2.5">
                        <svg class="mr-1 w-4 h-4 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="m15 19-7-7 7-7"/>
                        </svg>
                        Kembali
                    </button>
                </a>
            {{-- @endif --}}
        </div>
    </div>

    {{-- <script>
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
    </script> --}}
@endsection

