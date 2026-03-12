@extends('layouts.app')

@section('content')
    <div class="p-4 sm:ml-64">
        <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
            <h1 class="text-2xl font-bold  text-left mb-4 md:mb-0 md:w-auto md:flex-1">Jadwal Bimbingan</h1>
            <x-breadcrumb parent="Bimbingan" item="Jadwal Bimbingan" />
        </div>
        <div class="px-10 py-8 mt-3 p-5 max-h-[500px] overflow-y-auto rounded-md bg-white border border-gray-200">
            @include('components.alert-global')

            @if(auth()->user()->role === 'Dosen' && auth()->user()->dosen)
                <!-- Modal toggle -->
                <div class="flex justify-between mb-4 flex-wrap">
                    <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="focus:outline-none text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-4">
                        <svg class="w-7 h-7 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Tambah Jadwal Bimbingan
                    </button>
                </div>

                <!-- Main modal -->
                <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                    <div class="relative p-4 w-full max-w-3xl max-h-full">
                        <!-- Modal content -->
                        <div class="relative bg-white rounded-lg shadow-sm">
                            <!-- Modal header -->
                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Form Tambah Jadwal Bimbingan
                                </h3>
                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-modal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                            </div>
                            <!-- Modal body -->
                            <form action="{{ route('jadwal_bimbingan.store') }}" method="POST" class="p-4 md:p-5">
                                @csrf
                                <div class="grid gap-4 mb-4 grid-cols-2">
                                    <div class="col-cols-2">
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Nama Dosen</label>
                                        <input type="text" name="dosen_id" id="dosen_id" value="{{ auth()->user()->dosen->nama_dosen }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" readonly />
                                    </div>
                                    <div class="grid-cols-2">
                                        <label for="tanggal" class="block mb-2 text-sm font-medium text-gray-900">Tanggal </label>
                                        <input type="date" name="tanggal" id="tanggal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="GKB 1.1" required />
                                    </div>
                                    <div class="grid-cols-2">
                                        <label for="waktu" class="block mb-2 text-sm font-medium text-gray-900">Waktu</label>
                                        <input type="time" name="waktu" id="waktu" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                                    </div>
                                    <div class="grid-cols-2">
                                        <label for="kuota" class="block mb-2 text-sm font-medium text-gray-900">Kuota</label>
                                        <input type="number" name="kuota" id="kuota" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Masukkan kuota" min="1" required />
                                    </div>
                                    <div class="grid-cols-2">
                                        <label for="durasi" class="block mb-2 text-sm font-medium text-gray-900">Durasi (menit)</label>
                                        <input type="number" name="durasi" id="durasi" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" min="1" placeholder="Contoh: 30" required />
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
                @if($jadwalBimbingan->isEmpty())
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                        <strong class="font-bold">Perhatian!</strong>
                        @if(auth()->user()->role === 'Mahasiswa')
                            <span class="block sm:inline">Belum ada jadwal bimbingan dari dosen pembimbing Anda!</span>
                        @elseif(auth()->user()->role === 'Dosen' && auth()->user()->dosen)
                            <span class="block sm:inline">Tidak ada data jadwal bimbingan Anda.</span>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr class="text-center">
                                    <th class="w-1/12 border border-gray-300 px-4 py-2">No.</th>
                                        @if(auth()->user()->role === 'Dosen' && (auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Super Admin' ))
                                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nama Dosen</th>
                                        @endif
                                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Tanggal</th>
                                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Waktu</th>
                                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">  Sisa Kuota</th>
                                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Durasi</th>
                                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Status</th>
                                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Jumlah Pendaftar</th>
                                        <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Detail</th>
                                        <th class="w-4 border border-gray-300 px-4 py-2">Aksi</th>
                                    </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalBimbingan as $jadwal)
                                    <tr class="hover:bg-gray-50 text-center">
                                        <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration + ($jadwalBimbingan->currentPage() - 1) * $jadwalBimbingan->perPage() }}</td>
                                        @if(auth()->user()->role === 'Dosen' && (auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Super Admin' ))
                                            <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->dosen->nama_dosen }}</td>
                                        @endif
                                        <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}</td>
                                        <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->waktu }}</td>
                                        <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->kuota }}</td>
                                        <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->durasi }} menit </td>
                                        <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">{{ $jadwal->status }}</td>
                                        <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                            {{ $jadwal->pendaftaranBimbingan->count() }}
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                            <a href="{{ route('jadwal_bimbingan.detail', $jadwal->id) }}">
                                                <button class="text-sm bg-blue-500 font-medium px-4 py-2 flex items-center justify-center gap-1  mx-auto rounded-lg text-white whitespace-nowrap">
                                                    <svg class="w-5 h-5 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                       <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm9.408-5.5a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2Z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Detail
                                                </button>
                                            </a>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2 whitespace-nowrap">
                                            <div class="flex justify-center space-x-2">
                                                @if ($jadwal->isUsedInLogbook)
                                                    <button class="text-sm items-center flex justify-center w-full px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200 disabled cursor-not-allowed">
                                                        <svg class="mr-1 w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Batalkan Jadwal
                                                    </button>
                                                @else
                                                    <button data-modal-target="popup-modal-{{ $jadwal->id }}" data-modal-toggle="popup-modal-{{ $jadwal->id }}" class="text-sm items-center flex justify-center w-full px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">
                                                        <svg class="mr-1 w-5 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm7.707-3.707a1 1 0 0 0-1.414 1.414L10.586 12l-2.293 2.293a1 1 0 1 0 1.414 1.414L12 13.414l2.293 2.293a1 1 0 0 0 1.414-1.414L13.414 12l2.293-2.293a1 1 0 0 0-1.414-1.414L12 10.586 9.707 8.293Z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Batalkan Jadwal
                                                    </button>
                                                @endif
                                                <!-- Modal -->
                                                <div id="popup-modal-{{ $jadwal->id }}" tabindex="-1"
                                                    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                                                    <div class="relative p-4 w-full max-w-md max-h-full">
                                                        <div class="relative bg-white rounded-lg shadow-sm">
                                                            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent
                                                                hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto
                                                                inline-flex justify-center items-center" data-modal-hide="popup-modal-{{ $jadwal->id }}">
                                                                <svg class="w-3 h-3" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                                </svg>
                                                                <span class="sr-only">Close modal</span>
                                                            </button>
                                                            <div class="p-4 md:p-5 text-center">
                                                                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                                </svg>
                                                                <h3 class="mb-5 text-lg font-normal text-gray-500">
                                                                    Apakah anda yakin ingin membatalkan <br> jadwal bimbingan ini?
                                                                </h3>
                                                                <form id="delete-form-{{ $jadwal->id }}" action="{{ route('jadwal_bimbingan.destroy', $jadwal->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                    <button type="submit" class="w-full sm:w-20 md:w-20 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 justify-center">
                                                                        Ya
                                                                    </button>
                                                                </form>
                                                                <button data-modal-hide="popup-modal-{{ $jadwal->id }}" type="button" class="w-full sm:w-20 md:w-20 py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-grey-200 rounded-lg border border-gray-200 hover:bg-gray-500 hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-100 justify-center">
                                                                        Tidak
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <nav aria-label="Page navigation example">
                        {{ $jadwalBimbingan->links() }} <!-- Ini akan menghasilkan pagination -->
                    </nav>
                @endif
            @endif

            @if(auth()->user()->role === 'Mahasiswa')
                @if (!$pengajuan)
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                        <strong class="font-bold">Perhatian!</strong>
                        <span class="block sm:inline">Belum ada jadwal bimbingan. Anda belum mengajukan pembimbing tugas akhir Anda.</span>
                    </div>
                @elseif ($pengajuan && $pengajuan->validasi === 'Menunggu')
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                        <strong class="font-bold">Perhatian!</strong>
                        <span class="block sm:inline">Belum ada jadwal bimbingan. Tunggu Koordinator Program Studi memberikan validasi pengajuan pembimbing Anda.</span>
                    </div>
                @elseif ($jadwalBimbingan->isEmpty())
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                        <strong class="font-bold">Perhatian!</strong>
                        @if(auth()->user()->role === 'Mahasiswa')
                            <span class="block sm:inline">Belum ada jadwal bimbingan dari dosen pembimbing Anda!</span>
                        @elseif(auth()->user()->role === 'Dosen' && auth()->user()->dosen)
                            <span class="block sm:inline">Tidak ada data jadwal bimbingan Anda.</span>
                        @endif
                    </div>
                @else
                    {{-- @if(auth()->user()->role === 'Mahasiswa') --}}
                        <div class="overflow-x-auto">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($jadwalBimbingan as $jadwal)
                                @php
                                    $pendaftaran = $jadwal->pendaftaranBimbingan
                                        ->where('mahasiswa_id', auth()->user()->mahasiswa->id)
                                        ->first();

                                    $pendaftarDiterima = $jadwal->pendaftaranBimbingan
                                        ->where('status_pendaftaran', 'Diterima')
                                        ->sortBy('created_at')
                                        ->values(); // reset keys

                                    $antrian = $pendaftarDiterima->search(fn($p) => $p->mahasiswa_id == auth()->user()->mahasiswa->id);

                                    $durasi = $jadwal->durasi ?? 30;
                                    $waktuMulai = \Carbon\Carbon::parse($jadwal->waktu)->addMinutes($durasi * $antrian);
                                @endphp

                                    {{-- Konten kartu --}}
                                    <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 {{ $jadwal->kuota == 0 ? 'bg-gray-200' : '' }}">
                                        <h3 class="text-xl font-semibold text-gray-800">{{ $jadwal->dosen->nama_dosen }}</h3>
                                        <p class="text-gray-600">Tanggal: <strong>{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') }}</strong></p>
                                        <p class="text-gray-600">Waktu: <strong>{{ $jadwal->waktu }}</strong></p>
                                        <p class="text-gray-600">Kuota Tersisa: <strong class="{{ $jadwal->kuota == 0 ? 'text-red-500' : 'text-red-600' }}">{{ $jadwal->kuota }}</strong></p>

                                        {{-- Jika ada pendaftaran --}}
                                        @if ($pendaftaran)
                                            @if ($pendaftaran->status_pendaftaran === 'Diterima')
                                                <div class="mt-4 w-full bg-yellow-200 text-yellow-700 p-2 rounded-lg hover:bg-yellow-200 transition text-center">
                                                    Anda sudah mendaftar! <br>
                                                    <strong>Jam Anda: {{ $waktuMulai->format('H:i') }} WIB</strong><br>
                                                    Mohon datang <strong>30 menit sebelumnya</strong>.
                                                </div>
                                            @elseif ($pendaftaran->status_pendaftaran === 'Ditolak')
                                                <div class="mt-4 p-3 bg-red-100 text-red-700 rounded-lg text-center">
                                                    Anda <strong>ditolak</strong> pada jadwal ini dan tidak dapat mendaftar ulang.
                                                </div>
                                            @else
                                                <button type="button" class="mt-4 w-full bg-gray-300 text-black font-semibold p-2 rounded-lg pointer-events-none" disabled>
                                                    Anda sudah mendaftar! Menunggu konfirmasi.
                                                </button>
                                            @endif
                                        @else
                                            @if ($jadwal->kuota > 0)
                                                <form action="{{ route('jadwal_bimbingan.daftar', $jadwal->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="mt-4 w-full bg-green-600 text-white font-semibold py-2 rounded-lg hover:bg-green-600 transition">
                                                        Daftar Bimbingan
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="mt-4 w-full bg-red-600 text-white py-2 rounded-lg pointer-events-none" disabled>
                                                    Maaf, Kuota Penuh!
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                @endif
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const modalToggleButtons = document.querySelectorAll('[data-modal-toggle]');

            modalToggleButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const modalId = button.getAttribute('data-modal-target');
                    const modal = document.getElementById(modalId);
                    modal.classList.toggle('hidden'); // Toggle modal visibility
                    modal.classList.toggle('flex'); // Toggle modal visibility
                });
            });
        });
    </script>
@endsection



