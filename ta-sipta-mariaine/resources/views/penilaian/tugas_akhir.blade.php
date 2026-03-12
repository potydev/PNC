@extends('layouts.app')

@section('content')
    <div class="p-4 sm:ml-64">
        <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
            <h1 class="text-2xl font-bold text-left mb-4 md:mb-0 md:w-auto md:flex-1 whitespace-normal">Nilai Tugas Akhir</h1>
            <x-breadcrumb parent="Penilaian" item="Sidang Tugas Akhir" />
        </div>
        <div class="px-10 py-8 mt-3 p-5 max-h-[500px] overflow-y-auto rounded-md bg-white border border-gray-200">
            @if (auth()->user()->role === 'Dosen' && auth()->user()->dosen)
                @if($sidang->isEmpty())
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                        <strong class="font-bold">Perhatian!</strong>
                        <span class="block sm:inline">Belum ada data mahasiswa yang bisa Anda nilai.</span>
                    </div>
                @else
                    <table class="min-w-full bg-white shadow rounded-lg">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="w-1 border border-gray-300 px-4 py-2">No.</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nama Mahasiswa</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Jenis Sidang</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">File Tugas Akhir</th>
                                <th class="w-1 border border-gray-300 px-4 py-2">Catatan Revisi</th>
                                <th class="w-1 border border-gray-300 px-4 py-2">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($sidang as $item)
                            <tr class="border-t">
                                <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $item->mahasiswa->nama_mahasiswa }}</td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $item->jenis_sidang }}</td>
                                <td class="w-2 border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    @if ($item->mahasiswa?->pendaftaranSidang?->file_tugas_akhir)
                                        <a href="{{ route('pendaftaran_sidang.showFile', ['id' => $item->id, 'fileField' => 'file_tugas_akhir']) }}" target="_blank">
                                            <button class="text-sm bg-blue-500 font-medium px-4 py-2 flex items-center justify-center gap-1  mx-auto rounded-lg text-white whitespace-nowrap">
                                                <svg class="w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 17v-5h1.5a1.5 1.5 0 1 1 0 3H5m12 2v-5h2m-2 3h2M5 10V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1v6M5 19v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-1M10 3v4a1 1 0 0 1-1 1H5m6 4v5h1.375A1.627 1.627 0 0 0 14 15.375v-1.75A1.627 1.627 0 0 0 12.375 12H11Z"/>
                                                </svg>
                                                Lihat File TA
                                            </button>
                                        </a>
                                    @else
                                        <span class="text-red-500 italic">Belum ada file tugas akhir</span>
                                    @endif
                                </td>
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    <a href="{{ route('penilaian_ta.catatan.form', ['mahasiswa_id' => $item->mahasiswa->id, 'jadwal_sidang_tugas_akhir_id' => $item->id]) }}"
                                        class="inline-flex items-center gap-1 bg-yellow-500 hover:bg-yellow-600 text-white text-md font-semibold py-1 px-4 rounded">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M8 7V2.221a2 2 0 0 0-.5.365L3.586 6.5a2 2 0 0 0-.365.5H8Zm2 0V2h7a2 2 0 0 1 2 2v.126a5.087 5.087 0 0 0-4.74 1.368v.001l-6.642 6.642a3 3 0 0 0-.82 1.532l-.74 3.692a3 3 0 0 0 3.53 3.53l3.694-.738a3 3 0 0 0 1.532-.82L19 15.149V20a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Z" clip-rule="evenodd"/>
                                            <path fill-rule="evenodd" d="M17.447 8.08a1.087 1.087 0 0 1 1.187.238l.002.001a1.088 1.088 0 0 1 0 1.539l-.377.377-1.54-1.542.373-.374.002-.001c.1-.102.22-.182.353-.237Zm-2.143 2.027-4.644 4.644-.385 1.924 1.925-.385 4.644-4.642-1.54-1.54Zm2.56-4.11a3.087 3.087 0 0 0-2.187.909l-6.645 6.645a1 1 0 0 0-.274.51l-.739 3.693a1 1 0 0 0 1.177 1.176l3.693-.738a1 1 0 0 0 .51-.274l6.65-6.646a3.088 3.088 0 0 0-2.185-5.275Z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Catatan</span>
                                    </a>
                                </td>
                                <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">
                                    @if ($item->pembimbing_utama_id == $dosenId && $item->mahasiswa->nilai && $item->mahasiswa->nilai->nilai_ta_utama !== null)
                                        <span class="font-black">{{ $item->mahasiswa->nilai->nilai_ta_utama }}</span>
                                    @elseif ($item->pembimbing_pendamping_id == $dosenId && $item->mahasiswa->nilai && $item->mahasiswa->nilai->nilai_ta_pendamping !== null)
                                        <span class="font-black">{{ $item->mahasiswa->nilai->nilai_ta_pendamping }}</span>
                                    @elseif ($item->penguji_utama_id == $dosenId && $item->mahasiswa->nilai && $item->mahasiswa->nilai->nilai_ta_penguji_utama !== null)
                                        <span class="font-black">{{ $item->mahasiswa->nilai->nilai_ta_penguji_utama }}</span>
                                    @elseif ($item->penguji_pendamping_id == $dosenId && $item->mahasiswa->nilai && $item->mahasiswa->nilai->nilai_ta_penguji_pendamping !== null)
                                        <span class="font-black">{{ $item->mahasiswa->nilai->nilai_ta_penguji_pendamping }}</span>
                                    @elseif ( $item->pembimbing_utama_id == $dosenId || $item->pembimbing_pendamping_id == $dosenId || $item->penguji_utama_id == $dosenId || $item->penguji_pendamping_id == $dosenId )
                                        <a href="{{ route('penilaian_ta.form', $item->id) }}"
                                            class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-md font-semibold py-1 px-4 rounded">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                                <path fill-rule="evenodd" d="M8 7V2.221a2 2 0 0 0-.5.365L3.586 6.5a2 2 0 0 0-.365.5H8Zm2 0V2h7a2 2 0 0 1 2 2v.126a5.087 5.087 0 0 0-4.74 1.368v.001l-6.642 6.642a3 3 0 0 0-.82 1.532l-.74 3.692a3 3 0 0 0 3.53 3.53l3.694-.738a3 3 0 0 0 1.532-.82L19 15.149V20a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Z" clip-rule="evenodd"/>
                                                <path fill-rule="evenodd" d="M17.447 8.08a1.087 1.087 0 0 1 1.187.238l.002.001a1.088 1.088 0 0 1 0 1.539l-.377.377-1.54-1.542.373-.374.002-.001c.1-.102.22-.182.353-.237Zm-2.143 2.027-4.644 4.644-.385 1.924 1.925-.385 4.644-4.642-1.54-1.54Zm2.56-4.11a3.087 3.087 0 0 0-2.187.909l-6.645 6.645a1 1 0 0 0-.274.51l-.739 3.693a1 1 0 0 0 1.177 1.176l3.693-.738a1 1 0 0 0 .51-.274l6.65-6.646a3.088 3.088 0 0 0-2.185-5.275Z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Nilai</span>
                                        </a>
                                    @else
                                        <span class="text-gray-500 italic">Bukan penguji/pembimbing</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
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
