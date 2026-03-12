@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1 whitespace-nowrap">Data Pengajuan Pembimbing</h1>
        <x-breadcrumb parent="Pengajuan Pembimbing" />
    </div>

    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
        @include('components.alert-global')
            <div class="flex flex-col gap-2 mb-4">
                <form id="searchForm" action="{{ route('pengajuan_pembimbing.index_kaprodi.dropdown-search') }}" method="GET" class="w-full">
                    <div class="flex flex-wrap gap-4">
                        {{-- Tahun Ajaran --}}
                        <div class="flex-1 min-w-[200px]">
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

                        {{-- Pembimbing Utama --}}
                        <div class="flex-1 min-w-[200px]">
                            <label for="pembimbing_utama_id" class="block text-sm font-medium text-gray-900 mb-2">Pembimbing Utama</label>
                            <select name="pembimbing_utama_id" id="pembimbing_utama_id"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                onchange="this.form.submit()">
                                <option value="">Semua Pembimbing Utama</option>
                                @foreach($dosen as $d)
                                    <option value="{{ $d->id }}" {{ request()->get('pembimbing_utama_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama_dosen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pembimbing Pendamping --}}
                        <div class="flex-1 min-w-[200px]">
                            <label for="pembimbing_pendamping_id" class="block text-sm font-medium text-gray-900 mb-2">Pembimbing Pendamping</label>
                            <select name="pembimbing_pendamping_id" id="pembimbing_pendamping_id"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                onchange="this.form.submit()">
                                <option value="">Semua Pembimbing Pendamping</option>
                                @foreach($dosen as $d)
                                    <option value="{{ $d->id }}" {{ request()->get('pembimbing_pendamping_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama_dosen }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Validasi --}}
                        <div class="flex-1 min-w-[200px]">
                            <label for="validasi" class="block text-sm font-medium text-gray-900 mb-2">Status Validasi</label>
                            <select name="validasi" id="validasi"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                                onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="Menunggu" {{ request('validasi') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="Acc" {{ request('validasi') == 'Acc' ? 'selected' : '' }}>Acc</option>
                            </select>
                        </div>
                    </div>
                </form>

                <!-- Tombol Cetak -->
                @if ($user->role === 'Dosen' && $user->dosen->jabatan === 'Koordinator Program Studi')
                    <div class="ml-auto flex justify-end mb-1 mt-1">
                        <a href="{{ route('pengajuan_pembimbing.rekap_dosen', ['tahun_ajaran' => request('tahun_ajaran_id')]) }}"
                            target="_blank"
                            class="text-sm flex items-center justify-center w-full px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700 transition duration-200">
                            <svg class="w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M13 11.15V4a1 1 0 1 0-2 0v7.15L8.78 8.374a1 1 0 1 0-1.56 1.25l4 5a1 1 0 0 0 1.56 0l4-5a1 1 0 1 0-1.56-1.25L13 11.15Z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M9.657 15.874 7.358 13H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2h-2.358l-2.3 2.874a3 3 0 0 1-4.685 0ZM17 16a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2H17Z" clip-rule="evenodd" />
                            </svg>
                            Cetak Rekap Dosen Pembimbing
                        </a>
                    </div>
                @endif
            </div>

        @if($pengajuanPembimbing->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Daftar pengajuan pembimbing tidak ada.</span>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr class="text-center">
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">No.</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nama Mahasiswa</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Judul Tugas Akhir</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Dosen Pembimbing Utama</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Dosen Pembimbing Pendamping</th>
                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Status Validasi</th>
                        @if ($user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Koordinator Program Studi')
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuanPembimbing as $pembimbing)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration }}</td>
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $pembimbing->mahasiswa->nama_mahasiswa }}</td>
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $pembimbing->mahasiswa->proposal->judul_proposal }}</td>
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $pembimbing->pembimbingUtama ? $pembimbing->pembimbingUtama->nama_dosen : 'Tidak ada' }}</td>
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $pembimbing->pembimbingPendamping ? $pembimbing->pembimbingPendamping->nama_dosen : 'Tidak ada' }}</td>
                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $pembimbing->validasi }}</td>

                            @if ($user->role === 'Dosen' && $user->dosen && $user->dosen->jabatan === 'Koordinator Program Studi')
                                <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                    <div class="flex justify-center space-x-2">
                                        @if ($pembimbing->mahasiswa && !$pembimbing->mahasiswa->logbooks()->exists())
                                            <button data-modal-target="validasiModal-{{ $pembimbing->id }}" data-modal-toggle="validasiModal-{{ $pembimbing->id }}" class="text-sm flex items-center justify-center w-full px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700 transition duration-200">
                                                <svg class="mr-1 w-4 h-4 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z" clip-rule="evenodd"/>
                                                </svg>
                                                Validasi
                                            </button>
                                        @else
                                            <button class="text-sm flex items-center justify-center w-full px-3 py-2 bg-gray-400 text-white rounded-lg  hover:bg-gray-700 cursor-not-allowed" disabled>
                                                <svg class="mr-1 w-4 h-4 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                    <path fill-rule="evenodd" d="M12 2c-.791 0-1.55.314-2.11.874l-.893.893a.985.985 0 0 1-.696.288H7.04A2.984 2.984 0 0 0 4.055 7.04v1.262a.986.986 0 0 1-.288.696l-.893.893a2.984 2.984 0 0 0 0 4.22l.893.893a.985.985 0 0 1 .288.696v1.262a2.984 2.984 0 0 0 2.984 2.984h1.262c.261 0 .512.104.696.288l.893.893a2.984 2.984 0 0 0 4.22 0l.893-.893a.985.985 0 0 1 .696-.288h1.262a2.984 2.984 0 0 0 2.984-2.984V15.7c0-.261.104-.512.288-.696l.893-.893a2.984 2.984 0 0 0 0-4.22l-.893-.893a.985.985 0 0 1-.288-.696V7.04a2.984 2.984 0 0 0-2.984-2.984h-1.262a.985.985 0 0 1-.696-.288l-.893-.893A2.984 2.984 0 0 0 12 2Zm3.683 7.73a1 1 0 1 0-1.414-1.413l-4.253 4.253-1.277-1.277a1 1 0 0 0-1.415 1.414l1.985 1.984a1 1 0 0 0 1.414 0l4.96-4.96Z" clip-rule="evenodd"/>
                                                </svg>
                                                Validasi
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Modal Validasi -->
                                    <div id="validasiModal-{{ $pembimbing->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                        <div class="bg-white p-6 rounded-lg shadow-md">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Konfirmasi Validasi</h3>

                                            @if ($pembimbing->validasi === 'Acc')
                                                <!-- Pesan Jika Validasi Sudah "Acc" -->
                                                <div class="bg-yellow-100 border border-yellow-300 text-yellow-700 p-3 rounded mb-4">
                                                    Anda dapat mengubah validasi sebelum mahasiswa mengisi logbook bimbingan,<br>
                                                    dan mahasiswa atas nama {{ $pembimbing->mahasiswa->nama_mahasiswa }} belum mengisi logbook bimbingan.<br>
                                                    Namun, Anda sudah pernah memvalidasi ini.<br>
                                                    <b>Apakah Anda ingin mengubah data validasi?</b>
                                                </div>
                                            @elseif ($pembimbing->validasi === 'Menunggu')
                                                <!-- Pesan dan Alert Jika Validasi Masih "Menunggu" -->
                                                <div class="bg-yellow-100 border border-yellow-300 text-yellow-700 p-3 rounded mb-4">
                                                    <p class="text-gray-600 mb-4">Anda ingin <b>menerima</b> atau <b>memilih ulang</b> dosen pembimbing untuk {{ $pembimbing->mahasiswa->nama_mahasiswa }}?</p>
                                                </div>
                                            @endif

                                            <div class="flex justify-end space-x-2">
                                                <!-- Tombol Terima/Tidak -->
                                                <form action="{{ route('pengajuan_pembimbing.validasi', $pembimbing->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="validasi" value="{{ $pembimbing->validasi === 'Acc' ? 'Menunggu' : 'Acc' }}">
                                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-800">
                                                        {{ $pembimbing->validasi === 'Acc' ? 'Tidak' : 'Terima' }}
                                                    </button>
                                                </form>

                                                <!-- Pilih Ulang -->
                                                <button type="button" onclick="toggleModal('validasiModal-{{ $pembimbing->id }}', 'editModal-{{ $pembimbing->id }}')" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-700">Pilih Ulang</button>
                                                <!-- Batal -->
                                                <button data-modal-toggle="validasiModal-{{ $pembimbing->id }}" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                                    <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                    </svg>
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Edit -->
                                    <div id="editModal-{{ $pembimbing->id }}" tabindex="-1" aria-hidden="true" class="hidden flex overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                        <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Form Edit Pengajuan Pembimbing</h3>
                                            <form action="{{ route('pengajuan_pembimbing.update', $pembimbing->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="mb-4">
                                                    <label for="pembimbing_utama_id_edit" class="block mb-2 text-sm font-medium text-gray-900">Dosen Pembimbing Utama</label>
                                                    <select name="pembimbing_utama_id" class="pembimbing-utama-edit tom-select">
                                                        @foreach ($dosenPembimbingUtama as $pembimbingUtama)
                                                            <option value="{{ $pembimbingUtama->id }}" {{ $pembimbingUtama->id == $pembimbing->pembimbing_utama_id ? 'selected' : '' }}>{{ $pembimbingUtama->nama_dosen }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label for="pembimbing_pendamping_id_edit" class="block mb-2 text-sm font-medium text-gray-900">Dosen Pembimbing Pendamping</label>
                                                    <select name="pembimbing_pendamping_id" class="pembimbing-pendamping-edit tom-select">
                                                        @foreach ($dosenPembimbingPendamping as $pembimbingPendamping)
                                                            <option value="{{ $pembimbingPendamping->id }}" {{ $pembimbingPendamping->id == $pembimbing->pembimbing_pendamping_id ? 'selected' : '' }}>{{ $pembimbingPendamping->nama_dosen }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="flex justify-end space-x-2">
                                                    <button type="button" onclick="toggleModal('editModal-{{ $pembimbing->id }}', 'validasiModal-{{ $pembimbing->id }}')" class="text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
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
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        <nav aria-label="Page navigation example">
            {{ $pengajuanPembimbing->links() }} <!-- Ini akan menghasilkan pagination -->
        </nav>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[id^="editModal-"]').forEach(modal => {
            const utamaSelect = modal.querySelector('.pembimbing-utama-edit');
            const pendampingSelect = modal.querySelector('.pembimbing-pendamping-edit');

            const utama = new TomSelect(utamaSelect, {
                onChange: hideSelected
            });

            const pendamping = new TomSelect(pendampingSelect, {
                onChange: hideSelected
            });

            function hideSelected() {
                const val1 = utama.getValue();
                const val2 = pendamping.getValue();

                [utama, pendamping].forEach(select => {
                    select.clearOptions();
                    modal.querySelectorAll(`#${select.input.id} option`).forEach(opt => {
                        const hide = (select === utama && opt.value === val2) || (select === pendamping && opt.value === val1);
                        opt.hidden = hide;
                        if (!hide) select.addOption({ value: opt.value, text: opt.textContent });
                    });
                    select.refreshOptions(false);
                });
            }

            hideSelected(); // inisialisasi saat modal dibuka
        });
    });
</script>

<script>
    function toggleModal(hideModalId, showModalId) {
        document.getElementById(hideModalId).classList.add('hidden');
        document.getElementById(showModalId).classList.remove('hidden');
    }
</script>

@endsection
