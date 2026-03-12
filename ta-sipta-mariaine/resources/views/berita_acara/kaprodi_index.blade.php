{{-- @extends('layouts.app')

@section('content')
<div class="p-4">
    <h2 class="text-2xl font-bold mb-4">Daftar Berita Acara Mahasiswa</h2>
    <table class="min-w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">NIM</th>
                <th class="px-4 py-2">Seminar Proposal</th>
                <th class="px-4 py-2">Sidang Tugas Akhir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($mahasiswas as $mhs)
                <tr>
                    <td class="px-4 py-2">{{ $mhs->nama_mahasiswa }}</td>
                    <td class="px-4 py-2">{{ $mhs->nim }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('kaprodi.berita.seminar', $mhs->id) }}" class="text-blue-600 underline">Lihat</a>
                    </td>
                    <td class="px-4 py-2">
                        <a href="{{ route('kaprodi.berita.sidang', $mhs->id) }}" class="text-blue-600 underline">Lihat</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection --}}
