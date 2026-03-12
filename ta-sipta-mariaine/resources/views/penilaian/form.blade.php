@extends('layouts.app')

@section('content')
    @if ($sidang->rubrik->isEmpty())
        <div class="p-4 sm:ml-64">
            <div class="px-8 py-8 rounded-md bg-white border border-gray-200">
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Belum ada data rubrik peran Anda dalam penilaian ini!</span>
                </div>
            </div>
        </div>
    @elseif(!$sidang->rubrik_valid)
        <div class="p-4 sm:ml-64">
            <div class="px-8 py-8 rounded-md bg-white border border-gray-200">
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">
                        Rubrik penilaian peran Anda belum valid (total saat ini {{ $sidang->total_persentase }}%).
                    </span>
                </div>
                <a href="{{ route('penilaian.tugas_akhir') }}" class="inline-block mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Kembali
                </a>
            </div>
        </div>
    @else
        <div class="sm:px-4 lg:px-40 py-4 sm:ml-64">
            @include('components.alert-global')

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
                    </div>

                    <hr style="border: 1px solid black; margin-top: 8px;">

                    <h3 class="mt-6 text-lg font-bold text-center">PENILAIAN TUGAS AKHIR</h3>
                    <p class="mt-6 text-medium text-justify"> Hasil Evaluasi Sidang Akhir Untuk Mahasiswa: </p>
                    <table class="min-w-full border-collapse text-medium">
                        <tr>
                            <td class="w-2/5 py-1">Nama</td>
                            <td class="w-3/5 py-1">: {{$sidang->mahasiswa->nama_mahasiswa }}</td>
                        </tr>
                        <tr>
                            <td class="w-2/5 py-1">NIM</td>
                            <td class="w-3/5 py-1">: {{$sidang->mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <td class="w-2/5 py-1">Program Studi</td>
                            <td class="w-3/5 py-1">: {{$sidang->mahasiswa->programStudi->nama_prodi }}</td>
                        </tr>
                        <tr>
                            <td class="w-2/5 py-1">Judul Tugas Akhir</td>
                            <td class="w-3/5 py-1">: {{$sidang->mahasiswa->proposal->judul_proposal ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-2/5 py-1">Waktu</td>
                            <td class="w-3/5 py-1">: {{$sidang->waktu_mulai ?? '-' }} - {{$sidang->waktu_selesai ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="w-2/5 py-1">Ruangan</td>
                            <td class="w-3/5 py-1">: {{$sidang->ruanganSidang->nama_ruangan}}</td>
                        </tr>
                    </table>
                    <form action="{{ route('penilaian_ta.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="mahasiswa_id" value="{{ $sidang->mahasiswa->id }}">
                        <input type="hidden" name="dosen_id" value="{{ $sidang->dosen_id }}">
                        <input type="hidden" name="jadwal_sidang_tugas_akhir_id" value="{{ $sidang->id }}">
                            <table class="w-full text-sm border mb-4">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="p-2 border w-[60%]">Kategori</th>
                                        <th class="p-2 border w-[10%] text-center">Bobot (%)</th>
                                        <th class="p-2 border w-[15%] text-center">Nilai</th>
                                        <th class="p-2 border w-[15%] text-center">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @php
                                        $totalJumlah = 0;
                                    @endphp --}}
                                    @foreach ($sidang->rubrik as $r)
                                    {{-- @php
                                        $jumlah = $r->nilai && $r->persentase ? $r->nilai * $r->persentase / 100 : 0;
                                        $totalJumlah += $jumlah;
                                    @endphp --}}
                                        @if ($r->show_kelompok)
                                            <tr class="bg-white font-semibold text-black-800">
                                                <td class="p-2 border" colspan="4">{{ $r->kelompok }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="p-2 border">
                                            @if ($r->kelompok)
                                                <span class="ml-4">— {{ $r->kategori }}</span>
                                            @else
                                                {{ $r->kategori }}
                                            @endif
                                            </td>
                                            <td class="p-2 border text-center">{{ $r->persentase }}</td>
                                            <td class="p-2 border text-center">
                                                <input type="number"
                                                    name="nilai[{{ $r->id }}]"
                                                    value="{{ $r->nilai }}"
                                                    step="0.01"
                                                    {{ $r->readonly ? 'readonly' : 'required' }}
                                                    class="w-full border-gray-200 rounded focus:ring-blue-500 focus:border-blue-500 text-center
                                                    {{ $r->readonly ? ' bg-gray-50 text-gray-700' : '' }}"
                                                >
                                            </td>
                                            <td class="p-2 border text-center">
                                                @php
                                                    $jumlah = $r->nilai && $r->persentase ? ($r->nilai * $r->persentase / 100) : null;
                                                @endphp
                                                {{ $jumlah !== null ? number_format($jumlah, 2) : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-gray-100 font-semibold">
                                        <td colspan="3" class="p-2 border text-right">Total</td>
                                        <td class="p-2 border text-center">{{ number_format($sidang->total_nilai_akhir, 2) ?? '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="mt-10">
                                <div class="w-1/3 ml-auto text-center text-sm">
                                    <p class="font-semibold">
                                        Cilacap, {{ \Carbon\Carbon::parse($sidang->tanggal)->translatedFormat('d F Y') }}
                                    </p>
                                    <p class="my-4">
                                        Dosen {{ ucwords(str_replace('_', ' ', $sidang->peran)) }}
                                    </p>

                                    <div class="my-4">
                                        @if ($dosen->ttd_dosen)
                                            <img src="{{ asset('storage/' . $dosen->ttd_dosen) }}" alt="TTD Dosen" class="mx-auto h-20">
                                        @else
                                            <p class="italic text-red-600">Belum ada ttd</p>
                                        @endif
                                    </div>

                                    <p class="font-semibold">
                                        {{ $dosen->nama_dosen ?? '-' }}
                                    </p>
                                    <p class="font-semibold">
                                        {{ $dosen->nip ?? '-' }}
                                    </p>
                                </div>
                            </div>

                            @if (!$sidang->sudah_dinilai_semua)
                                <div class="flex justify-end gap-2">
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                        Simpan Nilai
                                    </button>
                                    <a href="{{ route('penilaian_ta.index') }}"
                                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                                        Kembali
                                    </a>
                                </div>
                            @else
                                <div class="flex justify-end mb-4">
                                    <a href="{{ route('penilaian_ta.index') }}" class="mb-4 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                                        Kembali
                                    </a>
                                    @if (!$dosen->ttd_dosen)
                                        <p class="mt-2 ml-2 text-red-600">Tidak bisa Cetak! Belum ada ttd Anda.</p>
                                    @else
                                    <a href="{{ route('penilaian_ta.cetak', $sidang->mahasiswa->id) }}" target="_blank" class="mb-4 ml-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-400">
                                        Cetak PDF
                                    </a>
                                    @endif
                                </div>
                            @endif
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
