@extends('layouts.app')

@section('content')
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

            <h3 class="mt-6 text-lg font-bold text-center">CATATAN REVISI SEMINAR PROPOSAL</h3>
            <p class="mt-6 text-medium text-justify">Berikut merupakan catatan revisi dari dosen terhadap mahasiswa:</p>

            <table class="min-w-full border-collapse text-medium my-4">
                <tr>
                    <td class="w-2/5 py-1">Nama Mahasiswa</td>
                    <td class="w-3/5 py-1">: {{ $penilaian->mahasiswa->nama_mahasiswa ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="w-2/5 py-1">NIM</td>
                    <td class="w-3/5 py-1">: {{ $penilaian->mahasiswa->nim ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="w-2/5 py-1">Waktu Seminar</td>
                    <td class="w-3/5 py-1">: {{ $jadwal->waktu_mulai ?? '-' }} - {{ $jadwal->waktu_selesai ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="w-2/5 py-1">Ruangan</td>
                    <td class="w-3/5 py-1">: {{ $jadwal->ruanganSidang->nama_ruangan ?? '-' }}</td>
                </tr>
            </table>

            <form id="catatanForm" action="{{ route('penilaian_sempro.catatan.store') }}" method="POST" class="space-y-4 mt-4">
                @csrf
                <input type="hidden" name="mahasiswa_id" value="{{ $penilaian->mahasiswa_id }}">
                <input type="hidden" name="jadwal_seminar_proposal_id" value="{{ $penilaian->jadwal_seminar_proposal_id }}">

                <div>
                    <label for="catatan_revisi" class="block font-semibold text-sm mb-1">Catatan Revisi:</label>
                    <textarea name="catatan_revisi" id="catatan_revisi" rows="8"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring focus:border-blue-300"
                        {{ $penilaian->catatan_revisi ? 'disabled' : '' }}
                        required>{{ old('catatan_revisi', $penilaian->catatan_revisi) }}</textarea>
                </div>

                <div class="mt-10">
                    <div class="w-1/3 ml-auto text-center text-sm">
                        <p class="font-semibold">
                            Cilacap, {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}
                        </p>
                        <p class="my-4">
                            Dosen {{ ucwords(str_replace('_', ' ', $peran)) }}
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
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    @if ($penilaian->catatan_revisi)
                        <button type="button" id="editButton" class="mb-4 bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                            Edit Catatan
                        </button>
                    @endif

                    <button type="submit" id="submitButton"
                        class="mb-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                        {{ $penilaian->catatan_revisi ? 'hidden' : '' }}>
                        {{ $penilaian->catatan_revisi ? 'Simpan Perubahan' : 'Simpan Catatan' }}
                    </button>

                     <a href="{{ route('penilaian_sempro.index') }}" class="mb-4 bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                        Kembali
                    </a>
                    @if ($penilaian && $penilaian->catatan_revisi)
                        <a href="{{ route('penilaian_sempro.cetak_revisi', ['mahasiswa_id' => $penilaian->mahasiswa_id, 'jadwal_seminar_proposal_id' => $penilaian->jadwal_seminar_proposal_id]) }}"
                            class="mb-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-400" target="_blank">
                            Download
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('editButton')?.addEventListener('click', function () {
        document.getElementById('catatan_revisi').removeAttribute('disabled');
        document.getElementById('submitButton').removeAttribute('hidden');
        this.setAttribute('hidden', true);
    });
</script>
@endsection
