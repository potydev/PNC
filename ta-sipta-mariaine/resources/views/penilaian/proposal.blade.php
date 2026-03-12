@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold text-left mb-4 md:mb-0 md:w-auto md:flex-1 whitespace-nowrap">Nilai Seminar Proposal</h1>
        <x-breadcrumb parent="Nilai" item="Seminar Proposal" />
    </div>

    <div class="px-10 py-8 mt-3 p-5 max-h-[500px] overflow-y-auto rounded-md bg-white border border-gray-200">
        @include('components.alert-global')

        @if ($seminar->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Belum ada data mahasiswa yang bisa Anda nilai.</span>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr class="text-center">
                        <th class="border border-gray-300 px-4 py-2">No</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nama Mahasiswa</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Judul Proposal</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Lihat Proposal</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Catatan Revisi</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($seminar as $item)
                        @php
                            $mahasiswa = $item->mahasiswa;
                            $penilaian = $mahasiswa->penilaianSempro->firstWhere('dosen_id', $dosenId);
                        @endphp
                        <tr class="text-center">
                            <td class="border border-gray-300 px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $mahasiswa->nama_mahasiswa }}</td>
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                @if ($mahasiswa->proposal && $mahasiswa->proposal->judul_proposal)
                                    {{ $mahasiswa->proposal->judul_proposal }}
                                @else
                                    <span class="text-red-500 italic">Belum ada judul</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if ($mahasiswa->proposal && $mahasiswa->proposal->file_proposal)
                                    <a href="{{ route('proposal.showFileProposal', $mahasiswa->proposal->id) }}" target="_blank"
                                    class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-4 rounded whitespace-nowrap">
                                     <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M4.998 7.78C6.729 6.345 9.198 5 12 5c2.802 0 5.27 1.345 7.002 2.78a12.713 12.713 0 0 1 2.096 2.183c.253.344.465.682.618.997.14.286.284.658.284 1.04s-.145.754-.284 1.04a6.6 6.6 0 0 1-.618.997 12.712 12.712 0 0 1-2.096 2.183C17.271 17.655 14.802 19 12 19c-2.802 0-5.27-1.345-7.002-2.78a12.712 12.712 0 0 1-2.096-2.183 6.6 6.6 0 0 1-.618-.997C2.144 12.754 2 12.382 2 12s.145-.754.284-1.04c.153-.315.365-.653.618-.997A12.714 12.714 0 0 1 4.998 7.78ZM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>
                                    </svg>
                                    Lihat Proposal
                                </a>
                                @else
                                    <span class="text-red-500 italic">Belum upload</span>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                <a href="{{ route('penilaian_sempro.catatan.form', [
                                    'mahasiswa_id' => $item->mahasiswa_id,
                                    'jadwal_seminar_proposal_id' => $item->id
                                ]) }}"
                                    class="inline-flex items-center gap-1 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-1 px-4 rounded">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M8 7V2.221a2 2 0 0 0-.5.365L3.586 6.5a2 2 0 0 0-.365.5H8Zm2 0V2h7a2 2 0 0 1 2 2v.126a5.087 5.087 0 0 0-4.74 1.368v.001l-6.642 6.642a3 3 0 0 0-.82 1.532l-.74 3.692a3 3 0 0 0 3.53 3.53l3.694-.738a3 3 0 0 0 1.532-.82L19 15.149V20a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Z" clip-rule="evenodd"/>
                                        <path fill-rule="evenodd" d="M17.447 8.08a1.087 1.087 0 0 1 1.187.238l.002.001a1.088 1.088 0 0 1 0 1.539l-.377.377-1.54-1.542.373-.374.002-.001c.1-.102.22-.182.353-.237Zm-2.143 2.027-4.644 4.644-.385 1.924 1.925-.385 4.644-4.642-1.54-1.54Zm2.56-4.11a3.087 3.087 0 0 0-2.187.909l-6.645 6.645a1 1 0 0 0-.274.51l-.739 3.693a1 1 0 0 0 1.177 1.176l3.693-.738a1 1 0 0 0 .51-.274l6.65-6.646a3.088 3.088 0 0 0-2.185-5.275Z" clip-rule="evenodd"/>
                                    </svg>
                                    Catatan
                                </a>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                @if ($penilaian && $penilaian->nilai !== null)
                                    <strong>{{ $penilaian->nilai }}</strong>
                                @else
                                   <button onclick="openModal('modal-seminar-{{ $item->id }}')"
                                    class="inline-flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-4 rounded">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M8 7V2.221a2 2 0 0 0-.5.365L3.586 6.5a2 2 0 0 0-.365.5H8Zm2 0V2h7a2 2 0 0 1 2 2v.126a5.087 5.087 0 0 0-4.74 1.368v.001l-6.642 6.642a3 3 0 0 0-.82 1.532l-.74 3.692a3 3 0 0 0 3.53 3.53l3.694-.738a3 3 0 0 0 1.532-.82L19 15.149V20a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Z" clip-rule="evenodd"/>
                                            <path fill-rule="evenodd" d="M17.447 8.08a1.087 1.087 0 0 1 1.187.238l.002.001a1.088 1.088 0 0 1 0 1.539l-.377.377-1.54-1.542.373-.374.002-.001c.1-.102.22-.182.353-.237Zm-2.143 2.027-4.644 4.644-.385 1.924 1.925-.385 4.644-4.642-1.54-1.54Zm2.56-4.11a3.087 3.087 0 0 0-2.187.909l-6.645 6.645a1 1 0 0 0-.274.51l-.739 3.693a1 1 0 0 0 1.177 1.176l3.693-.738a1 1 0 0 0 .51-.274l6.65-6.646a3.088 3.088 0 0 0-2.185-5.275Z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Nilai</span>
                                    </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Modal Form Penilaian -->
                        <div id="modal-seminar-{{ $item->id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                            <div class="bg-white rounded-lg w-full max-w-md p-6">
                                <h3 class="text-xl font-semibold mb-4">
                                    Isi Nilai Seminar Proposal: {{ $item->mahasiswa->nama_mahasiswa }}
                                </h3>
                                <form action="{{ route('penilaian_sempro.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="mahasiswa_id" value="{{ $item->mahasiswa_id }}">
                                    <input type="hidden" name="dosen_id" value="{{ $dosenId }}">
                                    <input type="hidden" name="jadwal_seminar_proposal_id" value="{{ $item->id }}">

                                    <div class="mb-4">
                                        <label class="block mb-2 text-sm font-medium text-gray-900">
                                            @if ($item->penguji_utama_id == $dosenId)
                                                Nilai Penguji Utama Seminar Proposal
                                            @elseif ($item->penguji_pendamping_id == $dosenId)
                                                Nilai Penguji Pendamping Seminar Proposal
                                            @else
                                                Anda bukan penguji di seminar ini.
                                            @endif
                                        </label>

                                        @if ($item->penguji_utama_id == $dosenId || $item->penguji_pendamping_id == $dosenId)
                                            <input type="number" name="nilai" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                                        @endif
                                    </div>

                                    <div class="flex justify-end gap-2 mt-4">
                                        <button type="button" onclick="closeModal('modal-seminar-{{ $item->id }}')" class="text-white bg-gray-600 hover:bg-gray-800 rounded-lg px-4 py-2">Batal</button>
                                        @if ($item->penguji_utama_id == $dosenId || $item->penguji_pendamping_id == $dosenId)
                                            <button type="submit" class="text-white bg-blue-600 hover:bg-blue-800 rounded-lg px-4 py-2">Simpan</button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }
</script>
@endsection
