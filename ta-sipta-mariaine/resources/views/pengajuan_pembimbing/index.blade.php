@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1 whitespace-nowrap">Pengajuan Pembimbing</h1>
        <x-breadcrumb parent="Bimbingan" item="Pengajuan Pembimbing" />
    </div>

    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
        @include('components.alert-global')
        @if ($user->role === 'Dosen')
            <form id="searchForm" action="{{ route('pengajuan_pembimbing.index.dropdown-search_dosen') }}" method="GET" class="mb-4">
                <div class="flex flex-col md:flex-row gap-4 w-full mb-4">
                    <div class="min-w-[200px]">
                        <label for="tahun_ajaran_id" class="block text-sm font-medium text-gray-700 mb-2">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" id="tahun_ajaran_id"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            onchange="this.form.submit()">
                            <option value="">Semua Tahun Ajaran</option>
                            @foreach ($tahunAjaranList as $tahun)
                                <option value="{{ $tahun->id }}" {{ request('tahun_ajaran_id') == $tahun->id ? 'selected' : '' }}>
                                    {{ $tahun->tahun_ajaran }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Sebagai dosen pembimbing -->
                    <div class="w-1/4 min-w-[310px]">
                        <label for="search_type" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">Sebagai pembimbing:</label>
                        <select name="search_type" id="search_type"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            onchange="document.getElementById('searchForm').submit();">
                            <option value="">Utama dan Pendamping</option>
                            <option value="Utama" {{ request('search_type') == 'Utama' ? 'selected' : '' }}>Utama</option>
                            <option value="Pendamping" {{ request('search_type') == 'Pendamping' ? 'selected' : '' }}>Pendamping</option>
                        </select>
                    </div>
                </div>
            </form>
        @endif


        @if(auth()->user()->role === 'Mahasiswa')
            @if (!$proposal)
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Anda belum unggah proposal. Silakan unggah proposal Anda terlebih dahulu.</span>
                </div>
            @else
                <div class="flex justify-between mb-4 flex-wrap">
                    @if($pengajuanPembimbing->isEmpty())
                        <!-- Tombol Tambah jika data belum ada -->
                        <button
                            data-modal-target="crud-modal"
                            data-modal-toggle="crud-modal"
                            class="focus:outline-none text-white font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-4 bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300"
                        >
                            <svg class="w-7 h-7 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                            </svg>
                            Tambah Pengajuan Pembimbing
                        </button>
                    @endif
                </div>
                <!-- Main modal -->
                <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                    <div class="relative p-4 w-full max-w-md max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow-sm">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Form Tambah Pengajuan Pembimbing
                                </h3>
                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <form action="{{ route('pengajuan_pembimbing.store') }}" method="POST" class="p-4 md:p-5" onsubmit="return validatePembimbing();">
                                @csrf
                                <div class="grid gap-4 mb-4 grid-cols-2">
                                    <div class="col-span-2">
                                        <label for='mahasiswa_id' class="block mb-2 text-sm font-medium text-gray-900">Mahasiswa</label>
                                        <input type="text" name="mahasiswa_id" id="mahasiswa_id" value="{{ auth()->user()->mahasiswa->nama_mahasiswa }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" readonly />
                                    </div>
                                    {{-- <div class="col-span-2">
                                        <label for="pembimbing_utama_id" class="block mb-2 text-sm font-medium text-gray-900">Pilih Dosen Pembimbing Utama</label>
                                        <select id="pembimbing_utama_id" name="pembimbing_utama_id" class="tom-select focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                            @foreach ($dosen as $pembimbingUtama)
                                                <option value="{{ $pembimbingUtama->id }}">{{ $pembimbingUtama->nama_dosen }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-span-2">
                                        <label for="pembimbing_pendamping_id" class="block mb-2 text-sm font-medium text-gray-900">Pilih Dosen Pembimbing Pendamping</label>
                                        <select id="pembimbing_pendamping_id" name="pembimbing_pendamping_id" class="tom-select focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                            @foreach ($dosen as $pembimbingPendamping)
                                                <option value="{{ $pembimbingPendamping->id }}">{{ $pembimbingPendamping->nama_dosen }}</option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    {{-- Pembimbing Utama: hanya dosen dari prodi yang sama --}}
                                    <div class="col-span-2">
                                        <label for="pembimbing_utama_id" class="block mb-2 text-sm font-medium text-gray-900">
                                            Pilih Dosen Pembimbing Utama
                                        </label>
                                        <select id="pembimbing_utama_id" name="pembimbing_utama_id" class="tom-select block w-full p-2.5">
                                            @foreach ($dosenPembimbingUtama as $pembimbingUtama)
                                                <option value="{{ $pembimbingUtama->id }}">{{ $pembimbingUtama->nama_dosen }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Pembimbing Pendamping: semua dosen --}}
                                    <div class="col-span-2">
                                        <label for="pembimbing_pendamping_id" class="block mb-2 text-sm font-medium text-gray-900">
                                            Pilih Dosen Pembimbing Pendamping
                                        </label>
                                        <select id="pembimbing_pendamping_id" name="pembimbing_pendamping_id" class="tom-select block w-full p-2.5">
                                            @foreach ($dosenPembimbingPendamping as $pembimbingPendamping)
                                                <option value="{{ $pembimbingPendamping->id }}">{{ $pembimbingPendamping->nama_dosen }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="flex justify-end space-x-2">
                                    <button type="button" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="crud-modal">
                                        <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        Batal
                                    </button>
                                    <button type="submit" class="text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                        <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                            <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
                                        </svg>
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if($pengajuanPembimbing->isEmpty())
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                        <strong class="font-bold">Perhatian!</strong>
                        <span class="block sm:inline">Anda belum menambahkan pengajuan dosen pembimbing Tugas Akhir Anda.</span>
                    </div>
                @else
                    <table class="w-full border border-gray-300 rounded-lg overflow-hidden shadow-md">
                        @foreach($pengajuanPembimbing as $pembimbing)
                            @if($pembimbing->validasi === 'Menunggu')
                                <div class="p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                                    Anda sudah mengajukan dosen pembimbing. Silakan tunggu validasi dari Koordinator Prodi terkait informasi resmi pembimbing Anda.
                                </div>
                            @elseif($pembimbing->validasi === 'Acc')
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:mb-4">
                                        <label class="block font-medium text-gray-700 whitespace-nowrap">Tanggal Pengajuan</label>
                                        <input type="text" value="{{ \Carbon\Carbon::parse($pembimbing->created_at)->translatedFormat('d F Y') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600 cursor-not-allowed" disabled>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block font-medium text-gray-700 whitespace-nowrap">Nama Mahasiswa</label>
                                        <input type="text" value="{{ $pembimbing->mahasiswa->nama_mahasiswa }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600 cursor-not-allowed" disabled>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:mb-4">
                                        <label class="block font-medium text-gray-700 whitespace-nowrap">Dosen Pembimbing Utama</label>
                                        <input type="text" value="{{ $pembimbing->pembimbingUtama ? $pembimbing->pembimbingUtama->nama_dosen : 'Tidak ada' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600 cursor-not-allowed" disabled>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block font-medium text-gray-700 whitespace-nowrap">Dosen Pembimbing Pendamping</label>
                                        <input type="text" value="{{ $pembimbing->pembimbingPendamping ? $pembimbing->pembimbingPendamping->nama_dosen : 'Tidak ada' }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-600 cursor-not-allowed" disabled>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </table>
                @endif
            @endif
        @endif

        @if (auth()->user()->role === 'Dosen' && auth()->user()->dosen)
            @if($pengajuanPembimbing->isEmpty())
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                    <strong class="font-bold">Perhatian!</strong>
                    <span class="block sm:inline">Tidak ada data mahasiswa bimbingan Anda.</span>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr class="text-center">
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">No.</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nama Mahasiswa</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Dosen Pembimbing Utama</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Dosen Pembimbing Pendamping</th>
                                <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Logbook Mahasiswa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengajuanPembimbing as $pembimbing)
                                <tr class="hover:bg-gray-50">
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $pembimbing->mahasiswa->nama_mahasiswa }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $pembimbing->pembimbingUtama ? $pembimbing->pembimbingUtama->nama_dosen : 'Tidak ada' }}</td>
                                    <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $pembimbing->pembimbingPendamping ? $pembimbing->pembimbingPendamping->nama_dosen : 'Tidak ada' }}</td>
                                    <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap align-middle">
                                        <a href="{{ route('logbook_bimbingan.show_mahasiswa', ['dosenId' => Auth::user()->dosen->id, 'mahasiswaId' => $pembimbing->mahasiswa->id]) }}">
                                            <button class="text-sm bg-blue-500 hover:bg-blue-600 font-medium px-4 py-2 transition duration-200 flex items-center justify-center gap-1 rounded-lg text-white mx-auto">
                                                <svg class="mr-1 w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M12.1429 11v9m0-9c-2.50543-.7107-3.19099-1.39543-6.13657-1.34968-.48057.00746-.86348.38718-.86348.84968v7.2884c0 .4824.41455.8682.91584.8617 2.77491-.0362 3.45995.6561 6.08421 1.3499m0-9c2.5053-.7107 3.1067-1.39542 6.0523-1.34968.4806.00746.9477.38718.9477.84968v7.2884c0 .4824-.4988.8682-1 .8617-2.775-.0362-3.3758.6561-6 1.3499m2-14c0 1.10457-.8955 2-2 2-1.1046 0-2-.89543-2-2s.8954-2 2-2c1.1045 0 2 .89543 2 2Z"/>
                                                </svg>
                                                Lihat Logbook
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <nav aria-label="Page navigation example">
                {{ $pengajuanPembimbing->links() }} <!-- Ini akan menghasilkan pagination -->
            </nav>

        @endif
    </div>
</div>

<script>
    function toggleModal(hideModalId, showModalId) {
        document.getElementById(hideModalId).classList.add('hidden');
        document.getElementById(showModalId).classList.remove('hidden');
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const utama = new TomSelect('#pembimbing_utama_id', {
            placeholder: 'Pilih Dosen Utama',
            onChange: hideSelected
        });
        const pendamping = new TomSelect('#pembimbing_pendamping_id', {
            placeholder: 'Pilih Dosen Pendamping',
            onChange: hideSelected
        });

        utama.clear();
        pendamping.clear();

        function hideSelected() {
            const val1 = utama.getValue();
            const val2 = pendamping.getValue();

            [utama, pendamping].forEach(select => {
                select.clearOptions();
                document.querySelectorAll(`#${select.input.id} option`).forEach(opt => {
                    const hide = (select === utama && opt.value === val2) || (select === pendamping && opt.value === val1);
                    opt.hidden = hide;
                    if (!hide) select.addOption({ value: opt.value, text: opt.textContent });
                });
                select.refreshOptions(false);
            });
        }

        hideSelected(); // inisialisasi
    });
</script>

@endsection

