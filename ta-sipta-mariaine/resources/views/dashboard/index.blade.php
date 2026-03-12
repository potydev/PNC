@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64 mt-6">
    <div class="col-span-12 space-y-6 xl:col-span-7">
        <div class="px-10 py-8 rounded-xl border border-gray-200 bg-white">
            @if (auth()->user()->role === 'Mahasiswa')
                <h2>Selamat datang {{ Auth::user()->name }}, Anda login sebagai {{ Auth::user()->role }}</h2>
            @elseif (auth()->user() ->role === 'Dosen' && auth()->user()->dosen->jabatan === 'Koordinator Program Studi')
                <h2>Selamat datang {{ Auth::user()->name }}, Anda login sebagai {{ Auth::user()->role }} dan mempunyai jabatan {{ Auth::user()->dosen->jabatan }} {{ Auth::user()->dosen->programStudi->nama_prodi}} </h2>
            @elseif (auth()->user() ->role === 'Dosen' && auth()->user()->dosen->jabatan === 'Super Admin')
                <h2>Selamat datang {{ Auth::user()->name }}, Anda login sebagai {{ Auth::user()->role }} dan mempunyai jabatan {{ Auth::user()->dosen->jabatan }} </h2>
            @elseif (auth()->user() ->role === 'Dosen')
                <h2>Selamat datang {{ Auth::user()->name }}, Anda login sebagai {{ Auth::user()->role }}</h2>
            @endif
        </div>

        @if ( auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->role === 'Dosen')
            <div class="px-10 py-8 rounded-xl border border-gray-200 bg-white">
                @include('components.alert-global')

                <div class="grid grid-cols-1 gap-4 md:grid-cols-4 md:gap-6">
                    <!-- Jumlah User -->
                    <div class="rounded-2xl shadow-lg border border-gray-100 bg-white p-6 md:p-6 ">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100">
                            <svg class="fill-gray-800" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.80443 5.60156C7.59109 5.60156 6.60749 6.58517 6.60749 7.79851C6.60749 9.01185 7.59109 9.99545 8.80443 9.99545C10.0178 9.99545 11.0014 9.01185 11.0014 7.79851C11.0014 6.58517 10.0178 5.60156 8.80443 5.60156ZM5.10749 7.79851C5.10749 5.75674 6.76267 4.10156 8.80443 4.10156C10.8462 4.10156 12.5014 5.75674 12.5014 7.79851C12.5014 9.84027 10.8462 11.4955 8.80443 11.4955C6.76267 11.4955 5.10749 9.84027 5.10749 7.79851ZM4.86252 15.3208C4.08769 16.0881 3.70377 17.0608 3.51705 17.8611C3.48384 18.0034 3.5211 18.1175 3.60712 18.2112C3.70161 18.3141 3.86659 18.3987 4.07591 18.3987H13.4249C13.6343 18.3987 13.7992 18.3141 13.8937 18.2112C13.9797 18.1175 14.017 18.0034 13.9838 17.8611C13.7971 17.0608 13.4132 16.0881 12.6383 15.3208C11.8821 14.572 10.6899 13.955 8.75042 13.955C6.81096 13.955 5.61877 14.572 4.86252 15.3208ZM3.8071 14.2549C4.87163 13.2009 6.45602 12.455 8.75042 12.455C11.0448 12.455 12.6292 13.2009 13.6937 14.2549C14.7397 15.2906 15.2207 16.5607 15.4446 17.5202C15.7658 18.8971 14.6071 19.8987 13.4249 19.8987H4.07591C2.89369 19.8987 1.73504 18.8971 2.05628 17.5202C2.28015 16.5607 2.76117 15.2906 3.8071 14.2549ZM15.3042 11.4955C14.4702 11.4955 13.7006 11.2193 13.0821 10.7533C13.3742 10.3314 13.6054 9.86419 13.7632 9.36432C14.1597 9.75463 14.7039 9.99545 15.3042 9.99545C16.5176 9.99545 17.5012 9.01185 17.5012 7.79851C17.5012 6.58517 16.5176 5.60156 15.3042 5.60156C14.7039 5.60156 14.1597 5.84239 13.7632 6.23271C13.6054 5.73284 13.3741 5.26561 13.082 4.84371C13.7006 4.37777 14.4702 4.10156 15.3042 4.10156C17.346 4.10156 19.0012 5.75674 19.0012 7.79851C19.0012 9.84027 17.346 11.4955 15.3042 11.4955ZM19.9248 19.8987H16.3901C16.7014 19.4736 16.9159 18.969 16.9827 18.3987H19.9248C20.1341 18.3987 20.2991 18.3141 20.3936 18.2112C20.4796 18.1175 20.5169 18.0034 20.4837 17.861C20.2969 17.0607 19.913 16.088 19.1382 15.3208C18.4047 14.5945 17.261 13.9921 15.4231 13.9566C15.2232 13.6945 14.9995 13.437 14.7491 13.1891C14.5144 12.9566 14.262 12.7384 13.9916 12.5362C14.3853 12.4831 14.8044 12.4549 15.2503 12.4549C17.5447 12.4549 19.1291 13.2008 20.1936 14.2549C21.2395 15.2906 21.7206 16.5607 21.9444 17.5202C22.2657 18.8971 21.107 19.8987 19.9248 19.8987Z" fill=""/>
                            </svg>
                        </div>

                        <div class="mt-5 flex items-end justify-between">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Jumlah User</span>
                                <div class="mt-2 flex items-center">
                                    <h4 class="text-title-sm font-bold text-gray-800 dark:text-white/90">
                                        {{ $userCount }}
                                    </h4>
                                    <span class="ml-1 text-title-sm font-bold text-gray-800 dark:text-white/90">User</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Jumlah Mahasiswa -->
                    <div class="rounded-2xl shadow-lg border border-gray-100 bg-white p-6 md:p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100">
                            <svg class="fill-gray-800" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.80443 5.60156C7.59109 5.60156 6.60749 6.58517 6.60749 7.79851C6.60749 9.01185 7.59109 9.99545 8.80443 9.99545C10.0178 9.99545 11.0014 9.01185 11.0014 7.79851C11.0014 6.58517 10.0178 5.60156 8.80443 5.60156ZM5.10749 7.79851C5.10749 5.75674 6.76267 4.10156 8.80443 4.10156C10.8462 4.10156 12.5014 5.75674 12.5014 7.79851C12.5014 9.84027 10.8462 11.4955 8.80443 11.4955C6.76267 11.4955 5.10749 9.84027 5.10749 7.79851ZM4.86252 15.3208C4.08769 16.0881 3.70377 17.0608 3.51705 17.8611C3.48384 18.0034 3.5211 18.1175 3.60712 18.2112C3.70161 18.3141 3.86659 18.3987 4.07591 18.3987H13.4249C13.6343 18.3987 13.7992 18.3141 13.8937 18.2112C13.9797 18.1175 14.017 18.0034 13.9838 17.8611C13.7971 17.0608 13.4132 16.0881 12.6383 15.3208C11.8821 14.572 10.6899 13.955 8.75042 13.955C6.81096 13.955 5.61877 14.572 4.86252 15.3208ZM3.8071 14.2549C4.87163 13.2009 6.45602 12.455 8.75042 12.455C11.0448 12.455 12.6292 13.2009 13.6937 14.2549C14.7397 15.2906 15.2207 16.5607 15.4446 17.5202C15.7658 18.8971 14.6071 19.8987 13.4249 19.8987H4.07591C2.89369 19.8987 1.73504 18.8971 2.05628 17.5202C2.28015 16.5607 2.76117 15.2906 3.8071 14.2549ZM15.3042 11.4955C14.4702 11.4955 13.7006 11.2193 13.0821 10.7533C13.3742 10.3314 13.6054 9.86419 13.7632 9.36432C14.1597 9.75463 14.7039 9.99545 15.3042 9.99545C16.5176 9.99545 17.5012 9.01185 17.5012 7.79851C17.5012 6.58517 16.5176 5.60156 15.3042 5.60156C14.7039 5.60156 14.1597 5.84239 13.7632 6.23271C13.6054 5.73284 13.3741 5.26561 13.082 4.84371C13.7006 4.37777 14.4702 4.10156 15.3042 4.10156C17.346 4.10156 19.0012 5.75674 19.0012 7.79851C19.0012 9.84027 17.346 11.4955 15.3042 11.4955ZM19.9248 19.8987H16.3901C16.7014 19.4736 16.9159 18.969 16.9827 18.3987H19.9248C20.1341 18.3987 20.2991 18.3141 20.3936 18.2112C20.4796 18.1175 20.5169 18.0034 20.4837 17.861C20.2969 17.0607 19.913 16.088 19.1382 15.3208C18.4047 14.5945 17.261 13.9921 15.4231 13.9566C15.2232 13.6945 14.9995 13.437 14.7491 13.1891C14.5144 12.9566 14.262 12.7384 13.9916 12.5362C14.3853 12.4831 14.8044 12.4549 15.2503 12.4549C17.5447 12.4549 19.1291 13.2008 20.1936 14.2549C21.2395 15.2906 21.7206 16.5607 21.9444 17.5202C22.2657 18.8971 21.107 19.8987 19.9248 19.8987Z" fill=""/>
                            </svg>
                        </div>

                        <div class="mt-5 flex items-end justify-between">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Jumlah Mahasiwa</span>
                                <div class="mt-2 flex items-center">
                                    <h4 class="text-title-sm font-bold text-gray-800 dark:text-white/90">
                                        {{ $mahasiswaCount }}
                                    </h4>
                                    <span class="ml-1 text-title-sm font-bold text-gray-800 dark:text-white/90">Mahasiswa</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Jumlah Dosen -->
                    <div class="rounded-2xl shadow-lg border border-gray-100 bg-white p-6 md:p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100">
                            <svg class="fill-gray-800" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.80443 5.60156C7.59109 5.60156 6.60749 6.58517 6.60749 7.79851C6.60749 9.01185 7.59109 9.99545 8.80443 9.99545C10.0178 9.99545 11.0014 9.01185 11.0014 7.79851C11.0014 6.58517 10.0178 5.60156 8.80443 5.60156ZM5.10749 7.79851C5.10749 5.75674 6.76267 4.10156 8.80443 4.10156C10.8462 4.10156 12.5014 5.75674 12.5014 7.79851C12.5014 9.84027 10.8462 11.4955 8.80443 11.4955C6.76267 11.4955 5.10749 9.84027 5.10749 7.79851ZM4.86252 15.3208C4.08769 16.0881 3.70377 17.0608 3.51705 17.8611C3.48384 18.0034 3.5211 18.1175 3.60712 18.2112C3.70161 18.3141 3.86659 18.3987 4.07591 18.3987H13.4249C13.6343 18.3987 13.7992 18.3141 13.8937 18.2112C13.9797 18.1175 14.017 18.0034 13.9838 17.8611C13.7971 17.0608 13.4132 16.0881 12.6383 15.3208C11.8821 14.572 10.6899 13.955 8.75042 13.955C6.81096 13.955 5.61877 14.572 4.86252 15.3208ZM3.8071 14.2549C4.87163 13.2009 6.45602 12.455 8.75042 12.455C11.0448 12.455 12.6292 13.2009 13.6937 14.2549C14.7397 15.2906 15.2207 16.5607 15.4446 17.5202C15.7658 18.8971 14.6071 19.8987 13.4249 19.8987H4.07591C2.89369 19.8987 1.73504 18.8971 2.05628 17.5202C2.28015 16.5607 2.76117 15.2906 3.8071 14.2549ZM15.3042 11.4955C14.4702 11.4955 13.7006 11.2193 13.0821 10.7533C13.3742 10.3314 13.6054 9.86419 13.7632 9.36432C14.1597 9.75463 14.7039 9.99545 15.3042 9.99545C16.5176 9.99545 17.5012 9.01185 17.5012 7.79851C17.5012 6.58517 16.5176 5.60156 15.3042 5.60156C14.7039 5.60156 14.1597 5.84239 13.7632 6.23271C13.6054 5.73284 13.3741 5.26561 13.082 4.84371C13.7006 4.37777 14.4702 4.10156 15.3042 4.10156C17.346 4.10156 19.0012 5.75674 19.0012 7.79851C19.0012 9.84027 17.346 11.4955 15.3042 11.4955ZM19.9248 19.8987H16.3901C16.7014 19.4736 16.9159 18.969 16.9827 18.3987H19.9248C20.1341 18.3987 20.2991 18.3141 20.3936 18.2112C20.4796 18.1175 20.5169 18.0034 20.4837 17.861C20.2969 17.0607 19.913 16.088 19.1382 15.3208C18.4047 14.5945 17.261 13.9921 15.4231 13.9566C15.2232 13.6945 14.9995 13.437 14.7491 13.1891C14.5144 12.9566 14.262 12.7384 13.9916 12.5362C14.3853 12.4831 14.8044 12.4549 15.2503 12.4549C17.5447 12.4549 19.1291 13.2008 20.1936 14.2549C21.2395 15.2906 21.7206 16.5607 21.9444 17.5202C22.2657 18.8971 21.107 19.8987 19.9248 19.8987Z" fill=""/>
                            </svg>
                        </div>

                        <div class="mt-5 flex items-end justify-between">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Jumlah Dosen</span>
                                <div class="mt-2 flex items-center">
                                    <h4 class="text-title-sm font-bold text-gray-800 dark:text-white/90">
                                        {{ $dosenCount }}
                                    </h4>
                                    <span class="ml-1 text-title-sm font-bold text-gray-800 dark:text-white/90">Dosen</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Jumlah Program Studi -->
                    <div class="rounded-2xl shadow-lg border border-gray-100 bg-white p-6 md:p-6">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100">
                        <svg class="fill-gray-800" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M11.665 3.75621C11.8762 3.65064 12.1247 3.65064 12.3358 3.75621L18.7807 6.97856L12.3358 10.2009C12.1247 10.3065 11.8762 10.3065 11.665 10.2009L5.22014 6.97856L11.665 3.75621ZM4.29297 8.19203V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0366V11.6513C11.1631 11.6205 11.0777 11.5843 10.9942 11.5426L4.29297 8.19203ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19202L13.0066 11.5426C12.9229 11.5844 12.8372 11.6208 12.75 11.6516V20.037ZM13.0066 2.41456C12.3732 2.09786 11.6277 2.09786 10.9942 2.41456L4.03676 5.89319C3.27449 6.27432 2.79297 7.05342 2.79297 7.90566V16.0946C2.79297 16.9469 3.27448 17.726 4.03676 18.1071L10.9942 21.5857L11.3296 20.9149L10.9942 21.5857C11.6277 21.9024 12.3732 21.9024 13.0066 21.5857L19.9641 18.1071C20.7264 17.726 21.2079 16.9469 21.2079 16.0946V7.90566C21.2079 7.05342 20.7264 6.27432 19.9641 5.89319L13.0066 2.41456Z" fill="" />
                        </svg>
                        </div>

                        <div class="mt-5 flex items-end justify-between">
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">Jumlah Program Studi</span>
                                <div class="mt-2 flex items-center">
                                    <h4 class="text-title-sm font-bold text-gray-800 dark:text-white/90">
                                        {{ $programstudiCount }}
                                    </h4>
                                    <span class="ml-1 text-title-sm font-bold text-gray-800 dark:text-white/90">Program Studi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="mt-10 text-xl font-bold mb-4">Informasi Dosen</h2>
                <table class="w-full border border-gray-300 rounded-lg overflow-hidden shadow-md">
                    <form action="{{ route('dosen.unggah_ttd') }}" method="POST" enctype="multipart/form-data" class="mt-6 mb-4">
                        @csrf
                        <div class="w-full lg:w-1/2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Unggah Tanda Tangan (JPG/JPEG/PNG)</label>
                            <div class="flex items-center gap-2 overflow-hidden">
                                <!-- Input File -->
                                <input type="file" name="ttd_dosen" accept=".jpg,.jpeg,.png" class="flex-1 min-w-0 max-w-full text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 px-2 py-1" required>
                                <!-- Tombol Simpan -->
                                <button type="submit"
                                    class="inline-flex shrink-0 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                        <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
                                    </svg>Simpan
                                </button>
                            </div>
                            @if ($dosen->ttd_dosen)
                                <p class="mt-2 mb-2 text-sm text-gray-600">Tanda tangan saat ini:</p>
                                <img src="{{ asset('storage/' . $dosen->ttd_dosen) }}" alt="Tanda Tangan" style="width: 100px; height: auto;" class="mb-6">
                            @else
                                <p class="mt-1 mb-4 text-sm font-bold text-red-600">Tanda tangan belum diunggah!</p>
                            @endif
                        </div>
                    </form>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">Nama Dosen</label>
                            <input type="text" value="{{ $dosen->nama_dosen }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">NIP</label>
                            <input type="text" value="{{ $dosen->nip }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">Tempat Lahir</label>
                            <input type="text" value="{{ $dosen->tempat_lahir }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">Tanggal Lahir</label>
                            <input type="text" value="{{ $dosen->tanggal_lahir}}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">Jenis Kelamin</label>
                            <input type="text" value="{{ $dosen->jenis_kelamin }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                        @if (auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi')
                            <div>
                                <label class="block font-medium text-gray-700 whitespace-nowrap">Jabatan</label>
                                <input type="text" value="{{ $dosen->jabatan }} {{ $dosen->programStudi->nama_prodi }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                            </div>
                        @endif
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button data-modal-target="editModal-{{ $dosen->id }}" data-modal-toggle="editModal-{{ $dosen->id }}" class="mt-6 text-sm flex items-center justify-center w-24 px-3 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-600 transition duration-200">
                            <svg class="mr-0.5 w-4 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                            </svg>
                            Edit
                        </button>
                    </div>

                    <!-- Modal Edit -->
                    <div id="editModal-{{ $dosen->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                        <div class="relative p-4 w-full max-w-3xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow-sm">
                                <!-- Modal header -->
                                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                    <h3 class="mx-4 text-lg font-semibold text-gray-900">Form Edit Dosen</h3>
                                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="editModal-{{ $dosen->id }}">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <!-- Modal body -->
                                <form action="{{ route('dosen.profile.update',  $dosen->id) }}" method="POST" class="p-4 md:p-5">
                                @csrf
                                @method('PUT')
                                    <div class="mx-8 grid gap-4 mb-4 grid-cols-2">
                                        <div class="grid-cols-2">
                                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Dosen</label>
                                            <input type="text" name="name" id="name" value="{{ $dosen->user->name }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                        </div>
                                        <div class="grid-cols-2">
                                            <label for="nip" class="block mb-2 text-sm font-medium text-gray-900">NIP</label>
                                            <input type="text" name="nip" id="nip" value="{{ $dosen->nip }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                        </div>
                                        <div class="grid-cols-2">
                                            <label for="tempat_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tempat Lahir</label>
                                            <input type="text" name="tempat_lahir" id="tempat_lahir" value="{{ $dosen->tempat_lahir }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                        </div>
                                        <div class="grid-cols-2">
                                            <label for="tanggal_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                                            <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="{{ $dosen->tanggal_lahir }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-900">Jenis Kelamin</label>
                                            <div class="flex col-span-2">
                                                <div class="flex items-center mb-6">
                                                    <input id="Laki-laki" type="radio" name="jenis_kelamin" value="Laki-laki" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300" {{ $dosen->jenis_kelamin == 'Laki-laki' ? 'checked' : '' }}>
                                                    <label for="Laki-laki" class="block ml-2 mr-6 text-sm font-medium text-gray-900">Laki-laki</label>
                                                </div>
                                                <div class="flex items-center mb-6">
                                                    <input id="Perempuan" type="radio" name="jenis_kelamin" value="Perempuan" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300" {{ $dosen->jenis_kelamin == 'Perempuan' ? 'checked' : '' }}>
                                                    <label for="Perempuan" class="block ml-2 text-sm font-medium text-gray-900">Perempuan</label>
                                                    </div>
                                            </div>
                                            <input type="hidden" name="jabatan" value="{{ $dosen->jabatan }}">
                                        </div>

                                        @if (auth()->user()->role === 'Dosen' && auth()->user()->dosen && (auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->dosen->jabatan === 'Super Admin' ))
                                            <div class="grid-cols-2">
                                                <label class="block text-sm font-medium text-gray-900 whitespace-nowrap">Jabatan</label>
                                                <input type="hidden" name="jabatan" value="{{ $dosen->jabatan }}">
                                                @if (auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi')
                                                <input type="hidden" name="program_studi_id" value="{{ $dosen->program_studi_id }}">
                                                <input type="text" value="{{ $dosen->jabatan }} {{ $dosen->programStudi->nama_prodi }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 cursor-not-allowed" disabled>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mx-8 flex justify-end space-x-2">
                                        <button type="button" class="mb-6 text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="editModal-{{ $dosen->id }}">
                                            <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                            </svg>
                                            Batal
                                        </button>
                                        <button type="submit" class="mb-6 text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
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
                </table>
            </div>
        @elseif(auth()->user()->role === 'Mahasiswa')
            <div class="px-10 py-8 rounded-xl border border-gray-200 bg-white">
                @include('components.alert-global')

                <h2 class="text-xl font-bold mb-4">Informasi Mahasiswa</h2>
                <table class="w-full border border-gray-300 rounded-lg overflow-hidden shadow-md">
                    <form action="{{ route('mahasiswa.unggah_ttd') }}" method="POST" enctype="multipart/form-data" class="mt-6 mb-4">
                        @csrf
                        <div class="w-full lg:w-1/2">
                            <label class="block mb-2 text-sm font-medium text-gray-700">Unggah Tanda Tangan (JPG/JPEG/PNG)</label>
                            <div class="flex items-center gap-2 overflow-hidden">
                                <!-- Input File -->
                                <input type="file" name="ttd_mahasiswa" accept=".jpg,.jpeg,.png" class="flex-1 min-w-0 max-w-full text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 px-2 py-1" required>
                                <!-- Tombol Simpan -->
                                <button type="submit"
                                    class="inline-flex shrink-0 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                        <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
                                    </svg>Simpan
                                </button>
                            </div>
                            @if ($mahasiswa->ttd_mahasiswa)
                                <p class="mt-2 mb-2 text-sm text-gray-600">Tanda tangan saat ini:</p>
                                <img src="{{ asset('storage/' . $mahasiswa->ttd_mahasiswa) }}" alt="Tanda Tangan" style="width: 100px; height: auto;" class="mb-6">
                            @else
                                <p class="mt-1 mb-4 text-sm font-bold text-red-600">Tanda tangan belum diunggah!</p>
                            @endif
                        </div>
                    </form>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">Nama Mahasiswa</label>
                            <input type="text" value="{{ $mahasiswa->nama_mahasiswa }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">NIM</label>
                            <input type="text" value="{{ $mahasiswa->nim }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">Tempat Lahir</label>
                            <input type="text" value="{{ $mahasiswa->tempat_lahir }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">Tanggal Lahir</label>
                            <input type="text" value="{{ $mahasiswa->tanggal_lahir}}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">Jenis Kelamin</label>
                            <input type="text" value="{{ $mahasiswa->jenis_kelamin }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">Program Studi</label>
                            <input type="text" value="{{ $mahasiswa->programStudi->nama_prodi}}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:mb-4">
                            <label class="block font-medium text-gray-700 whitespace-nowrap">Tahun Ajaran</label>
                            <input type="text" value="{{ $mahasiswa->tahunAjaran->tahun_ajaran }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-white text-gray-600 cursor-not-allowed" disabled>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button data-modal-target="editModal-{{ $mahasiswa->id }}" data-modal-toggle="editModal-{{ $mahasiswa->id }}" class="mt-6 text-sm flex items-center justify-center w-24 px-3 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-600 transition duration-200">
                            <svg class="mr-0.5 w-4 h-5 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                            </svg>
                            Edit
                        </button>
                    </div>

                    <!-- Modal Edit -->
                    <div id="editModal-{{ $mahasiswa->id }}" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-full max-h-full bg-black bg-opacity-45">
                        <div class="relative p-4 w-full max-w-3xl max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow-sm">
                                <!-- Modal header -->
                                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                                    <h3 class="mx-4 text-lg font-semibold text-gray-900">Form Edit Mahasiswa</h3>
                                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="editModal-{{ $mahasiswa->id }}">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>
                                <!-- Modal body -->
                                <form action="{{ route('mahasiswa.profile.update', $mahasiswa->id) }}" method="POST" class="p-4 md:p-5">
                                    @csrf
                                    @method('PUT')
                                    <div class="mx-8 grid gap-4 mb-4 grid-cols-2">
                                        <div class="grid-cols-2">
                                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Mahasiswa</label>
                                            <input type="text" name="name" id="name" value="{{ $mahasiswa->user->name }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                        </div>
                                        <div class="grid-cols-2">
                                            <label for="nim" class="block mb-2 text-sm font-medium text-gray-900">NIM</label>
                                            <input type="text" name="nim" id="nim" value="{{ $mahasiswa->nim }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                        </div>
                                        <div class="grid-cols-2">
                                            <label for="tempat_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tempat Lahir</label>
                                            <input type="text" name="tempat_lahir" id="edit_tempat_lahir" value="{{ $mahasiswa->tempat_lahir }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                        </div>
                                        <div class="grid-cols-2">
                                        <label for="tanggal_lahir" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Lahir</label>
                                            <input type="date" name="tanggal_lahir" id="edit_tanggal_lahir" value="{{ $mahasiswa->tanggal_lahir }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-900">Jenis Kelamin</label>
                                            <div class="flex col-span-2">
                                                <div class="flex items-center mb-6">
                                                    <input id="Laki-laki" type="radio" name="jenis_kelamin" value="Laki-laki" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300" {{ $mahasiswa->jenis_kelamin == 'Laki-laki' ? 'checked' : '' }}>
                                                    <label for="Laki-laki" class="block ml-2 mr-6 text-sm font-medium text-gray-900">Laki-laki</label>
                                                </div>
                                                <div class="flex items-center mb-6">
                                                    <input id="Perempuan" type="radio" name="jenis_kelamin" value="Perempuan" class="w-4 h-4 border-gray-300 focus:ring-2 focus:ring-blue-300" {{ $mahasiswa->jenis_kelamin == 'Perempuan' ? 'checked' : '' }}>
                                                    <label for="Perempuan" class="block ml-2 text-sm font-medium text-gray-900">Perempuan</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid-cols-2">
                                            <label for="program_studi" class="block mb-2 text-sm font-medium text-gray-900">Program Studi</label>
                                            <select id="program_studi" name="program_studi_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                <option selected disabled>Pilih Program Studi</option>
                                                @foreach ($programStudi as $prodi)
                                                <option value="{{ $prodi->id }}" {{ $prodi->id == $mahasiswa->program_studi_id ? 'selected' : '' }}>{{ $prodi->nama_prodi }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="grid-cols-2">
                                            <label for="edit_tahun_ajaran" class="block mb-2 text-sm font-medium text-gray-900">Tahun Ajaran</label>
                                            <select id="edit_tahun_ajaran" name="tahun_ajaran_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                <option selected disabled>Pilih Tahun Ajaran</option>
                                                @foreach ($tahunAjaran as $tahun)
                                                <option value="{{ $tahun->id }}" {{ $tahun->id == $mahasiswa->tahun_ajaran_id ? 'selected' : '' }}>{{ $tahun->tahun_ajaran }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mx-8 flex justify-end space-x-2">
                                        <button type="button" class="mb-6 text-white inline-flex items-center bg-gray-600 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" data-modal-toggle="editModal-{{ $mahasiswa->id }}">
                                            <svg class="me-2 w-2 h-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                            </svg>
                                            Batal
                                        </button>
                                        <button type="submit" class="mb-6 text-white inline-flex items-center bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                            <svg class="me-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">                                                    <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                                                <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
                                            </svg>
                                            Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection


