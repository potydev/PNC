{{-- @extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200">
        <h1 class="text-2xl font-bold text-left mb-4 md:mb-0 md:w-auto md:flex-1 whitespace-nowrap">Berita Acara Sidang Tugas Akhir</h1>
        <x-breadcrumb parent="Berita Acara" item="Sidang Tugas Akhir" />
    </div>
        <div class="font-times tracking-wide text-base sm:px-14 lg:px-60 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200 overflow-x-auto">
            <div class="min-w-[600px]">
                <div class="my-6 flex items-center">
                    <div class="flex justify-end w-2/12 text-right">
                        <img src="{{ asset('img/pnc.png') }}" alt="Logo PNC" class="h-20 w-auto mr-4">
                    </div>
                    <div class="w-10/12 text-center leading-none tracking-wide">
                        <p class="text-mb mb-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN,</p>
                        <p class="text-mb mb-0">RISET, DAN TEKNOLOGI</p>
                        <p class="text-lg font-bold whitespace-nowrap mb-0">POLITEKNIK NEGERI CILACAP</p>
                        <p class="text-xs mb-0">Jalan Dr. Soetomo No. 1, Sidakaya - CILACAP 53212 Jawa Tengah</p>
                        <p class="text-xs mb-0">Telepon: (0282) 533329, Fax: (0282) 537992</p>
                        <p class="text-xs mb-0">www.pnc.ac.id, Email: sekretariat@pnc.ac.id</p>
                    </div>
                </div>

                <hr style="border: 1px solid black; margin-top: 8px;">

                <h3 class="mt-6 text-lg font-bold text-center">BERITA ACARA {{ strtoupper($judulSidang) }}</h3>
                <p class="mt-6 text-lg text-justify"> Pada hari ini, {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }} bertempat di Kampus Politeknik Negeri Cilacap telah melaksanakan {{ strtolower($judulSidang) }} mahasiswa: </p>

                <table class="min-w-full border-collapse text-lg">
                    <tr><td class="w-2/5 py-1"><strong>Nama</strong></td><td class="w-3/5 py-1">: {{ $jadwal->mahasiswa->nama_mahasiswa }}</td></tr>
                    <tr><td class="w-2/5 py-1"><strong>NIM</strong></td><td class="w-3/5 py-1">: {{ $jadwal->mahasiswa->nim }}</td></tr>
                    <tr><td class="w-2/5 py-1"><strong>Program Studi</strong></td><td class="w-3/5 py-1">: {{ $jadwal->mahasiswa->programStudi->nama_prodi ?? '-' }}</td></tr>
                    <tr><td class="w-2/5 py-1"><strong>Judul Proposal</strong></td><td class="w-3/5 py-1">: {{ $jadwal->mahasiswa->proposal->judul_proposal ?? '-' }}</td></tr>
                </table>

                <p class="text-lg text-justify mt-4">Dalam pelaksanaan {{ strtolower($judulSidang) }} ini mahasiswa diuji oleh Tim Penguji:</p>
                <table class="text-lg min-w-full border-collapse">
                    <tr><td class="w-2/5 py-1"><strong>1. Penguji Utama</strong></td><td class="w-3/5 py-1">: {{ $jadwal->pengujiUtama->nama_dosen }}</td></tr>
                    <tr><td class="w-2/5 py-1"><strong>2. Penguji Pendamping</strong></td><td class="w-3/5 py-1">: {{ $jadwal->pengujiPendamping->nama_dosen }}</td></tr>
                </table>

                <p class="text-lg text-justify mt-4">Yang bertempat di ruangan {{ $jadwal->ruanganSidang->nama_ruangan ?? '-' }}.</p>
                <p class="text-lg mt-4 mb-2 text-justify">Berdasarkan hasil evaluasi, maka mahasiswa tersebut dinyatakan:</p>
                <div class="text-lg p-1 border border-gray-600 rounded-md bg-white text-center">
                    <span class="font-black text-base">{{ $status ?? 'Belum Dinilai' }}</span>
                </div>

                <p class="mt-8 text-right text-base">Cilacap, {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}</p>

                <table class="mt-8 w-full border border-black text-sm text-left table-fixed">
                    <tbody>
                        <tr>
                            <td class="border border-black p-1 w-1/2"></td><td class="border border-black p-1 text-center">Tanda Tangan</td></tr>
                        <tr>
                            <td class="border border-black p-2">Mahasiswa</td>
                            <td class="border border-black p-2 h-10"><img src="{{ asset('storage/' . $jadwal->mahasiswa->ttd_mahasiswa) }}" alt="TTD Mahasiswa" class="h-12 mx-auto"></td>
                        </tr>
                        <tr>
                            <td class="border border-black p-2">Pembimbing Utama</td>
                            <td class="border border-black p-2 h-10"><img src="{{ asset('storage/' . $jadwal->pembimbingUtama->ttd_dosen) }}" alt="TTD Pembimbing Utama" class="h-12 mx-auto"></td>
                        </tr>
                        <tr>
                            <td class="border border-black p-2">Pembimbing Pendamping</td>
                            <td class="border border-black p-2 h-10"><img src="{{ asset('storage/' . $jadwal->pembimbingPendamping->ttd_dosen) }}" alt="TTD Pembimbing Pendamping" class="h-10 mx-auto"></td>
                        </tr>
                        <tr>
                            <td class="border border-black p-2">Ketua Penguji</td>
                            <td class="border border-black p-2 h-10"><img src="{{ asset('storage/' . $jadwal->pengujiUtama->ttd_dosen) }}" alt="TTD Penguji Utama" class="h-12 mx-auto"></td>
                        </tr>
                        <tr>
                            <td class="border border-black p-2">Anggota Penguji</td>
                            <td class="border border-black p-2 h-10"><img src="{{ asset('storage/' . $jadwal->pengujiPendamping->ttd_dosen) }}" alt="TTD Penguji Utama" class="h-12 mx-auto"></td>
                        </tr>
                    </tbody>
                </table>
            <a href="{{ url()->previous() }}" class="inline-block mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Kembali
            </a>

            </div>
        </div>
</div>
@endsection --}}
