@extends('layouts.app')


@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold text-left mb-4 md:mb-0 md:w-auto md:flex-1">Card Dosen Pembimbing</h1>
        <x-breadcrumb parent="Bimbingan" item="Card Dosen Pembimbing" />
    </div>

    <div class="px-10 py-6 mt-3 p-5 rounded-md bg-white border border-gray-200">
        @if($user->role === 'Mahasiswa')
            @if (!$pengajuan)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Belum ada logbook bimbingan. Anda belum mengajukan pembimbing tugas akhir Anda.</span>
                </div>
            @elseif ($pengajuan && $pengajuan->validasi === 'Menunggu')
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Belum ada logbook bimbingan. Tunggu Koordinator Program Studi memberikan validasi pengajuan pembimbing Anda.</span>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Card untuk Pembimbing Utama -->
                    @if($pengajuan->pembimbingUtama)
                        <div class="bg-blue-500 text-white text-center p-4 rounded-lg shadow transition-transform transform hover:scale-105 cursor-pointer"
                            onclick="window.location='{{ route('logbook_bimbingan.show_mahasiswa', [$pengajuan->pembimbingUtama->id, $pengajuan->mahasiswa_id]) }}'">
                            <h2 class="text-lg font-bold">Pembimbing Utama</h2>
                            <p>Nama Dosen: {{ $pengajuan->pembimbingUtama->nama_dosen }}</p>
                        </div>
                    @else
                        <div class="bg-gray-300 text-gray-500 text-center p-4 rounded-lg shadow">
                            <h2 class="text-lg font-bold">Pembimbing Utama</h2>
                            <p>Belum ada dosen</p>
                        </div>
                    @endif

                    <!-- Card untuk Pembimbing Pendamping -->
                    @if($pengajuan->pembimbingPendamping)
                        <div class="bg-gray-500 text-white text-center p-4 rounded-lg shadow transition-transform transform hover:scale-105 cursor-pointer"
                            onclick="window.location='{{ route('logbook_bimbingan.show_mahasiswa', [$pengajuan->pembimbingPendamping->id, $pengajuan->mahasiswa_id]) }}'">
                            <h2 class="text-lg font-bold">Pembimbing Pendamping</h2>
                            <p>Nama Dosen: {{ $pengajuan->pembimbingPendamping->nama_dosen }}</p>
                        </div>
                    @else
                        <div class="bg-gray-300 text-gray-500 text-center p-4 rounded-lg shadow">
                            <h2 class="text-lg font-bold">Pembimbing Pendamping</h2>
                            <p>Belum ada dosen</p>
                        </div>
                    @endif
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
