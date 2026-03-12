@extends('layouts.app')

@section('content')
<div class="p-4 sm:ml-64">
    <div class="px-10 py-6 mt-1 flex flex-col md:flex-row items-center h-auto rounded-md bg-white border border-gray-200 p-5">
        <h1 class="text-2xl font-bold text-left mb-4 md:mb-0 md:w-auto md:flex-1">Data User</h1>
        <x-breadcrumb parent="Data Master" item="User" />
    </div>
    <div class="px-10 py-8 mt-3 p-5 rounded-md bg-white border border-gray-200">
        {{-- <div class="flex justify-end mb-4"> --}}
            <div class="flex justify-between items-center mb-4 flex-wrap">
            <form id="searchForm" action="{{ route('user.dropdown-search') }}" method="GET" class="mb-0">
                <div class="flex flex-col md:flex-row gap-4 w-full mb-4">
                    <!-- Dropdown Role User -->
                    <div class="flex-1 min-w-[200px]">
                        <label for="role" class="block text-sm font-medium text-gray-900 dark:text-white mb-2 mr-2">Role User</label>
                        <select name="role" id="role"
                            class="block w-full p-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                            onchange="document.getElementById('searchForm').submit();">
                            <option value="">Semua Role</option>
                            <option value="Mahasiswa" {{ request()->get('role') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="Dosen" {{ request()->get('role') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                        </select>
                    </div>
                </div>
            </form>
            <form action="{{ route('user.search') }}" method="GET" class="w-full sm:max-w-xs mt-3" id="search-form">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="search" id="search-input" name="search"
                        class="block w-full p-2.5 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Cari data user disini"
                        required value="{{ request('search') }}"
                        oninput="document.querySelector('#search-form').submit();"
                    />
                </div>
            </form>
            </div>
        {{-- </div> --}}
        @if($user->isEmpty())
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative text-center" role="alert">
                <strong class="font-bold">Perhatian!</strong>
                <span class="block sm:inline">Tidak ada data user yang bisa ditampilkan!</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="mb-4 table-auto w-full border-collapse border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">No.</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Nama</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Email</th>
                            <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Role</th>
                            {{-- <th class="border border-gray-300 px-4 py-2 whitespace-nowrap">Aksi</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $data)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 text-center whitespace-nowrap">{{ $loop->iteration + ($user->currentPage() - 1) * $user->perPage() }}</td>
                            <td class="border px-4 py-2 whitespace-nowrap">{{ $data->name }}</td>
                            <td class="border px-4 py-2 whitespace-nowrap">{{ $data->email }}</td>
                            <td class="border px-4 py-2 whitespace-nowrap">{{ $data->role }}</td>
                            {{-- <td class="border px-4 py-2 text-center whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <form action="{{ route('user.resetPassword', $data) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-sm bg-red-500 text-white hover:bg-red-700 px-4 py-2 rounded flex items-center">
                                            <svg class="w-5 h-5 mr-1 text-white dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.651 7.65a7.131 7.131 0 0 0-12.68 3.15M18.001 4v4h-4m-7.652 8.35a7.13 7.13 0 0 0 12.68-3.15M6 20v-4h4"/>
                                                </svg>
                                            Reset Password
                                        </button>
                                    </form>
                                </div>
                            </td> --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav aria-label="Page navigation example">
                {{ $user->links() }} <!-- Ini akan menghasilkan pagination -->
            </nav>
        @endif
    </div>
</div>
@endsection
