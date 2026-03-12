@extends('layouts.app')
@section('content')
    @if (!$nilai || !$nilai->total_akhir)        <div class="p-4 sm:ml-64">
            <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
                <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1 whitespace-nowrap">Berita Acara Seminar Proposal</h1>
                <x-breadcrumb parent="Berita Acara" item="Seminar Proposal" />
            </div>
            <div class="px-10 py-8 mt-3 p-5 max-h-[500px] overflow-y-auto rounded-md bg-white border border-gray-200">
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Belum ada berita acara seminar proposal Anda.</span>
                </div>
            </div>
        </div>
    @else
        <div class="sm:px-4 lg:px-40 py-4 sm:ml-64">
            <div class="font-times tracking-wide text-base sm:px-12 lg:px-20 mt-3 p-2 rounded-md bg-white border border-gray-200 overflow-x-auto">
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
                        <hr class="border border-black my-4">
                    </div>

                    <hr style="border: 1px solid black; margin-top: 8px;">

                    <h3 class="mt-6 text-lg font-bold text-center">BERITA ACARA SIDANG PROPOSAL TUGAS AKHIR</h3>
                    <p class="mt-6 text-medium text-justify"> Pada hari ini, {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }} bertempat di Kampus Politeknik Negeri Cilacap telah melaksanakan Sidang Proposal Tugas Akhir Mahasiswa: </p>
                    <table class="min-w-full border-collapse text-medium">
                        <tr>
                            <td class="w-2/5 py-1">Nama</td>
                            <td class="w-3/5 py-1">: {{ $jadwal->mahasiswa->nama_mahasiswa }}</td>
                        </tr>
                        <tr>
                            <td class="w-2/5 py-1">NIM</td>
                            <td class="w-3/5 py-1">: {{ $jadwal->mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <td class="w-2/5 py-1">Program Studi</td>
                            <td class="w-3/5 py-1">: {{ $jadwal->mahasiswa->programStudi->nama_prodi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-2/5 py-1">Judul Proposal</td>
                            <td class="w-3/5 py-1">: {{ $jadwal->mahasiswa->proposal->judul_proposal ?? '-' }}</td>
                        </tr>
                    </table>

                    <p class="text-medium text-justify mt-4">Dalam pelaksanaan Sidang Proposal Tugas Akhir ini mahasiswa bersangkutan di Uji Proposal Tugas Akhir oleh Tim Penguji yang terdiri: </p>
                    <table class="text-medium min-w-full border-collapse">
                        <tr>
                            <td class="w-2/5 py-1">1. Penguji Utama</td>
                            <td class="w-3/5 py-1">: {{ $jadwal->pengujiUtama->nama_dosen }}</td>
                        </tr>
                        <tr>
                            <td class="lg:w-2/5 py-1">2. Penguji Pendamping</td>
                            <td class="lg:w-3/5 py-1">: {{ $jadwal->pengujiPendamping->nama_dosen }}</td>
                        </tr>
                    </table>

                    <p class="text-medium text-justify mt-4">Yang bertempat di ruangan {{ $jadwal->ruanganSidang->nama_ruangan ?? '-' }}.</p>
                    <p class="text-medium mt-4 mb-2 text-justify"> Dan berdasarkan hasil evaluasi, maka mahasiswa tersebut dinyatakan: </p>
                    <div class="text-medium p-1 border border-gray-600 rounded-md bg-white text-center">
                        @if ($status)
                            <span class="font-black text-base">{{ $status }}</span>
                        @else
                            <span class="font-black text-base">Belum Dinilai</span>
                        @endif
                    </div>

                    <p class="mt-8 text-right text-base">Cilacap, {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}</p>
                    <table class="mt-8 w-full border border-black text-sm text-left table-fixed">
                        <tbody>
                            <tr>
                                <td class="border border-black p-1 w-1/2 font-normal"></td>
                                <td class="border border-black p-1 text-center">Tanda Tangan</td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2">Mahasiswa</td>
                                <td class="border border-black p-2 h-10"><img src="{{ asset('storage/' . $jadwal->mahasiswa->ttd_mahasiswa) }}" alt="TTD Mahasiswa" class="h-12 mx-auto"></td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2">Ketua Penguji</td>
                                <td class="border border-black p-2 h-10"><img src="{{ asset('storage/' . $jadwal->pengujiUtama->ttd_dosen) }}" alt="TTD Penguji Utama" class="h-12 mx-auto"></td>
                            </tr>
                            <tr>
                                <td class="border border-black p-2">Anggota Penguji</td>
                                <td class="border border-black p-2 h-10"><img src="{{ asset('storage/' . $jadwal->pengujiPendamping->ttd_dosen) }}" alt="TTD Penguji Pendamping" class="h-12 mx-auto"></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="w-full flex justify-end mb-4 mt-4">
                        <a href="{{ route('berita_acara.seminar-proposal.cetak') }}"
                        target="_blank"
                        class="inline-flex text-sm items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded">
                            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M13 11.15V4a1 1 0 1 0-2 0v7.15L8.78 8.374a1 1 0 1 0-1.56 1.25l4 5a1 1 0 0 0 1.56 0l4-5a1 1 0 1 0-1.56-1.25L13 11.15Z" clip-rule="evenodd"/>
                                <path fill-rule="evenodd" d="M9.657 15.874 7.358 13H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-2.358l-2.3 2.874a3 3 0 0 1-4.685 0ZM17 16a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z" clip-rule="evenodd"/>
                            </svg>
                            Download Berita Acara
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
