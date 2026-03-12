
<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0" aria-label="Sidebar">
    <div class="h-full px-3 overflow-y-auto bg-white">
        <ul class="space-y-2 font-medium">
            {{-- DASHBOARD --}}
            <li class="mb-2">
                <a href="{{ route('dashboard') }}"
                class="menu-button flex items-center p-2 rounded-lg
                        {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                    <svg class="w-7 h-6 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>

            {{-- PROFILE --}}
            <li class="mb-2">
                <a href="{{ route('profile.edit') }}"
                class="menu-button flex items-center p-2 rounded-lg {{ request()->routeIs('profile.edit') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                    <svg class="w-7 h-6 {{ request()->routeIs('profile.edit') ? 'text-white' : 'text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ms-3">Profile</span>
                </a>
            </li>

            {{-- DATA MASTER KAPRODI --}}
            @if (auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Super Admin')
                <li class="mb-2">
                    <button type="button" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group {{ request()->routeIs('user.*', 'mahasiswa.*', 'dosen.*', 'program_studi.*', 'tahun_ajaran.*', 'ruangan_sidang.*', 'rubrik_nilai.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}" aria-controls="dropdown-datamaster" data-collapse-toggle="dropdown-datamaster">
                        <svg class="w-7 h-6 {{ request()->routeIs('user.*', 'mahasiswa.*', 'dosen.*', 'program_studi.*', 'tahun_ajaran.*', 'ruangan_sidang.*', 'rubrik_nilai.*') ? 'text-white' : 'text-gray-500'  }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                            <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Data Master</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>
                    <ul id="dropdown-datamaster" class="py-2 space-y-2 submenu {{ request()->routeIs('user.*', 'mahasiswa.*', 'dosen.*', 'program_studi.*', 'tahun_ajaran.*', 'ruangan_sidang.*', 'rubrik_nilai.*') ? '' : 'hidden' }}" data-collapse-id="dropdown-datamaster">
                        <li>
                            <a href="{{ route('user.index') }}" class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group {{ request()->routeIs('user.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('user.*') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
                                </svg>
                                User
                            </a>
                        </li>
                        <li>
                             <a href="{{ route('mahasiswa.index') }}" class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group {{ request()->routeIs('mahasiswa.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('mahasiswa.*') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
                                </svg>
                                Mahasiswa
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dosen.index') }}" class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group {{ request()->routeIs('dosen.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('dosen.*') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z" clip-rule="evenodd"/>
                                </svg>
                                Dosen
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('program_studi.index') }}" class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group {{ request()->routeIs('program_studi.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('program_studi.*') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11 4.717c-2.286-.58-4.16-.756-7.045-.71A1.99 1.99 0 0 0 2 6v11c0 1.133.934 2.022 2.044 2.007 2.759-.038 4.5.16 6.956.791V4.717Zm2 15.081c2.456-.631 4.198-.829 6.956-.791A2.013 2.013 0 0 0 22 16.999V6a1.99 1.99 0 0 0-1.955-1.993c-2.885-.046-4.76.13-7.045.71v15.081Z" clip-rule="evenodd"/>
                                </svg>
                                Program Studi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tahun_ajaran.index') }}" class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group {{ request()->routeIs('tahun_ajaran.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('tahun_ajaran.*') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6.01-6a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm-10 4a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z" clip-rule="evenodd"/>
                                </svg>
                                Tahun Ajaran
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('ruangan_sidang.index') }}" class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group {{ request()->routeIs('ruangan_sidang.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('ruangan_sidang.*') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm2 8v-2h7v2H4Zm0 2v2h7v-2H4Zm9 2h7v-2h-7v2Zm7-4v-2h-7v2h7Z" clip-rule="evenodd"/>
                                </svg>
                                Ruangan Sidang
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('rubrik_nilai.index') }}" class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group {{ request()->routeIs('rubrik_nilai.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('rubrik_nilai.*') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm2 8v-2h7v2H4Zm0 2v2h7v-2H4Zm9 2h7v-2h-7v2Zm7-4v-2h-7v2h7Z" clip-rule="evenodd"/>
                                </svg>
                                Rubrik Nilai
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- PROPOSAL MAHASISWA, KAPRODI, SUPER ADMIN --}}
            @if (auth()->user()->role === 'Mahasiswa' || auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Super Admin')
                <li class="mb-2">
                    <a href="{{ route('proposal.index') }}" class="menu-button flex items-center p-2 rounded-lg {{ request()->routeIs('proposal.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}">
                        <svg class="w-7 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 {{ request()->routeIs('proposal.*') ? 'text-white' : 'text-gray-500' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">
                            @if (auth()->user()->role === 'Mahasiswa' && auth()->user()->mahasiswa)
                            Pengajuan Proposal
                            @elseif (auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Super Admin')
                            Proposal
                            @endif
                        </span>
                    </a>
                </li>
            @endif


            {{-- BIMBINGAN --}}
            <li class="mb-2">
                <button type="button"
                    class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group
                    {{ request()->routeIs('pengajuan_pembimbing.*', 'jadwal_bimbingan.*', 'logbook_bimbingan.*') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}"
                    aria-controls="dropdown-bimbingan" data-collapse-toggle="dropdown-bimbingan">
                    <svg class="w-7 h-6 {{ request()->routeIs('pengajuan_pembimbing.*', 'jadwal_bimbingan.*', 'logbook_bimbingan.*') ? 'text-white' : 'text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z"
                            clip-rule="evenodd" />
                        <path fill-rule="evenodd" d="M14 17h-4v-2h4v2Z" clip-rule="evenodd" />
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Bimbingan</span>
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2" d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <ul id="dropdown-bimbingan"
                    class="py-2 space-y-2 submenu {{ request()->routeIs('pengajuan_pembimbing.*', 'jadwal_bimbingan.*', 'logbook_bimbingan.*') ? '' : 'hidden' }}"
                    data-collapse-id="dropdown-bimbingan">
                    <li>
                        <a href="{{ route('pengajuan_pembimbing.index') }}"
                            class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group
                            {{ request()->routeIs('pengajuan_pembimbing.index', 'pengajuan_pembimbing.index.*') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                            <svg class="w-7 h-6 me-4 {{ request()->routeIs('pengajuan_pembimbing.index', 'pengajuan_pembimbing.index.*') ? 'text-white' : 'text-gray-500' }}"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                                    clip-rule="evenodd" />
                            </svg>
                            @if (auth()->user()->role === 'Mahasiswa' && auth()->user()->mahasiswa)
                                Pengajuan Pembimbing
                            @elseif (auth()->user()->role === 'Dosen' && auth()->user()->dosen)
                                Mahasiswa Bimbingan
                            @endif
                        </a>
                    </li>

                    @if (auth()->user()->role === 'Dosen' && auth()->user()->dosen && (auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->dosen->jabatan === 'Super Admin'))
                        <li>
                            <a href="{{ route('pengajuan_pembimbing.index_kaprodi') }}"
                                class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group
                                {{ request()->routeIs('pengajuan_pembimbing.index_kaprodi', 'pengajuan_pembimbing.index_kaprodi.*') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('pengajuan_pembimbing.index_kaprodi', 'pengajuan_pembimbing.index_kaprodi.*') ? 'text-white' : 'text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Data Pembimbing Mahasiswa
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ route('jadwal_bimbingan.index') }}"
                            class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group
                            {{ request()->routeIs('jadwal_bimbingan.index') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                            <svg class="w-7 h-6 me-4 {{ request()->routeIs('jadwal_bimbingan.index') ? 'text-white' : 'text-gray-500' }}"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                                    clip-rule="evenodd" />
                            </svg>
                            Jadwal Bimbingan
                        </a>
                    </li>

                    @if (auth()->user()->role === 'Dosen' && (auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Super Admin'))
                        <li>
                            <a href="{{ route('jadwal_bimbingan.index_kaprodi') }}"
                                class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group
                                {{ request()->routeIs('jadwal_bimbingan.index_kaprodi', 'jadwal_bimbingan.index_kaprodi.*' ) ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('jadwal_bimbingan.index_kaprodi', 'jadwal_bimbingan.index_kaprodi.*') ? 'text-white' : 'text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Daftar Jadwal Bimbingan
                            </a>
                        </li>
                    @elseif(auth()->user()->role === 'Mahasiswa' && auth()->user()->mahasiswa)
                        <li>
                            <a href="{{ route('logbook_bimbingan.index_mahasiswa') }}"
                                class="submenu-link flex items-center w-full p-2 transition duration-75 rounded-lg group
                                {{ request()->routeIs('logbook_bimbingan.*') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('logbook_bimbingan.*') ? 'text-white' : 'text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Logbook Bimbingan
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            {{-- PENDAFTARAN SIDANG MAHASISWA, KAPRODI, SUPER ADMIN --}}
            @if (auth()->user()->role === 'Mahasiswa' || auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Super Admin')
                <li class="mb-2">
                    @if (auth()->user()->role === 'Mahasiswa' && auth()->user()->mahasiswa)
                        <a href="{{ route('pendaftaran_sidang.index') }}" class="menu-button flex items-center p-2 rounded-lg {{ request()->routeIs('pendaftaran_sidang.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}">
                    @elseif (auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Super Admin')
                        <a href="{{ route('pendaftaran_sidang.index_kaprodi') }}" class="menu-button flex items-center p-2 rounded-lg {{ request()->routeIs('pendaftaran_sidang.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}">
                    @endif
                        <svg class="w-7 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 {{ request()->routeIs('pendaftaran_sidang.*') ? 'text-white' : 'text-gray-500' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Pendaftaran Sidang</span>
                    </a>
                </li>
            @endif

            {{-- JADWAL SIDANG --}}
            <li class="mb-2">
                <button type="button" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group
                    {{ request()->routeIs('jadwal_seminar_proposal.index', 'jadwal_seminar_proposal.dropdown-search', 'jadwal_sidang_tugas_akhir.index', 'jadwal_sidang_tugas_akhir.dropdown-search') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}"
                    aria-controls="dropdown-jadwalsidang"
                    data-collapse-toggle="dropdown-jadwalsidang">
                    <svg class="w-7 h-6 {{ request()->routeIs('jadwal_seminar_proposal.index', 'jadwal_seminar_proposal.dropdown-search', 'jadwal_sidang_tugas_akhir.index', 'jadwal_sidang_tugas_akhir.dropdown-search') ? 'text-white' : 'text-gray-500' }}"
                        xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 5V4a1 1 0 1 1 2 0v1h3V4a1 1 0 1 1 2 0v1h3V4a1 1 0 1 1 2 0v1h1a2 2 0 0 1 2 2v2H3V7a2 2 0 0 1 2-2h1ZM3 19v-8h18v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm5-6a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2H8Z" clip-rule="evenodd"/>
                    </svg>
                    <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Jadwal Sidang</span>
                    <svg class="w-3 h-3"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                    </svg>
                </button>

                <ul id="dropdown-jadwalsidang" class="{{ request()->routeIs('jadwal_seminar_proposal.index', 'jadwal_seminar_proposal.dropdown-search', 'jadwal_sidang_tugas_akhir.index', 'jadwal_sidang_tugas_akhir.dropdown-search') ? 'block' : 'hidden' }} py-2 space-y-2 submenu" data-collapse-id="dropdown-jadwalsidang">
                    <li>
                        <a href="{{ route('jadwal_seminar_proposal.index') }}" class="submenu-link flex items-center w-full p-2 rounded-lg group
                            {{ request()->routeIs('jadwal_seminar_proposal.index',  'jadwal_seminar_proposal.dropdown-search') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                            <svg class="w-7 h-6 me-4 {{ request()->routeIs('jadwal_seminar_proposal.index', 'jadwal_seminar_proposal.dropdown-search') ? 'text-white' : 'text-gray-500' }}"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6.01-6a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm-10 4a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z" clip-rule="evenodd"/>
                            </svg>
                            Seminar Proposal
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('jadwal_sidang_tugas_akhir.index') }}" class="submenu-link flex items-center w-full p-2 rounded-lg group
                            {{ request()->routeIs('jadwal_sidang_tugas_akhir.index', 'jadwal_sidang_tugas_akhir.dropdown-search') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                            <svg class="w-7 h-6 me-4 {{ request()->routeIs('jadwal_sidang_tugas_akhir.index', 'jadwal_sidang_tugas_akhir.dropdown-search') ? 'text-white' : 'text-gray-500' }}"
                                xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6.01-6a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm-10 4a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Z" clip-rule="evenodd"/>
                            </svg>
                            Sidang Tugas Akhir
                        </a>
                    </li>
                </ul>
            </li>

            {{-- PENILAIAN --}}
            @if(auth()->user()->role === 'Dosen' && auth()->user()->dosen)
                <li class="mb-2">
                    <button type="button"
                        class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group
                        {{ request()->routeIs('penilaian_sempro.*', 'penilaian_ta.*') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}"
                        aria-controls="dropdown-nilai" data-collapse-toggle="dropdown-nilai">
                        <svg class="w-7 h-6 {{ request()->routeIs('penilaian_sempro.*', 'penilaian_ta.*') ? 'text-white' :  'text-gray-500' }}"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M6 5V4a1 1 0 1 1 2 0v1h3V4a1 1 0 1 1 2 0v1h3V4a1 1 0 1 1 2 0v1h1a2 2 0 0 1 2 2v2H3V7a2 2 0 0 1 2-2h1ZM3 19v-8h18v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm5-6a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2H8Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="flex-1 ms-3 text-left whitespace-nowrap">Penilaian</span>
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>

                    <ul id="dropdown-nilai" class="{{ request()->routeIs('penilaian_sempro.*', 'penilaian_ta.*') ? 'block' : 'hidden' }} py-2 space-y-2 submenu" data-collapse-id="dropdown-nilai">
                        <li>
                            <a href="{{ route('penilaian_sempro.index') }}"
                                class="submenu-link flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg group
                                {{ request()->routeIs('penilaian_sempro.*') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                                <svg class="w-7 h-6 me-4  {{ request()->routeIs('penilaian_sempro.*') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Nilai Seminar Proposal
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('penilaian_ta.index') }}"
                                class="submenu-link flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg group
                                {{ request()->routeIs('penilaian_ta.index', 'penilaian_ta.form') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                                <svg class="w-7 h-6 me-4  {{ request()->routeIs('penilaian_ta.index', 'penilaian_ta.form') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Nilai Tugas Akhir
                            </a>
                        </li>
                        @if(auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->dosen->jabatan === 'Super Admin')
                            <li>
                                <a href="{{ route('penilaian_ta.rekap_nilai') }}"
                                    class="submenu-link flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg group
                                    {{ request()->routeIs('penilaian_ta.rekap_nilai') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                                    <svg class="w-7 h-6 me-4  {{ request()->routeIs('penilaian_ta.rekap_nilai') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    Rekap Nilai Tugas Akhir
                                </a>
                            </li>
                        @endif
                        {{-- @if(auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->dosen->jabatan === 'Super Admin')
                            <li>
                                <a href="{{ route('nilai.daftar_nilai') }}"
                                    class="submenu-link flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg group
                                    {{ request()->routeIs('nilai.daftar_nilai') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                                    <svg class="w-7 h-6 me-4 {{ request()->routeIs('nilai.daftar_nilai') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                        d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"
                                        clip-rule="evenodd" />
                                    </svg>
                                    Daftar Nilai Mahasiswa
                                </a>
                            </li>
                        @endif --}}
                    </ul>
                </li>
            @endif

            {{-- HASIL AKHIR SEMPRO --}}
            @if (auth()->user()->role === 'Mahasiswa' || auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Super Admin')
                <li class="mb-2">
                    <a href="{{ route('hasil_akhir_sempro.index') }}" class="menu-button flex items-center p-2 rounded-lg {{ request()->routeIs('hasil_akhir_sempro.*') ? 'bg-blue-600 text-white' : 'bg-white text-black'  }}">
                        <svg class="w-7 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 {{ request()->routeIs('hasil_akhir_sempro.*') ? 'text-white' : 'text-gray-500' }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Hasil Akhir Sempro</span>
                    </a>
                </li>
            @endif

            {{-- HASIL SIDANG --}}
            @if ( auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Koordinator Program Studi' || auth()->user()->role === 'Dosen' && auth()->user()->dosen && auth()->user()->dosen->jabatan === 'Super Admin')
                <li class="mb-2">
                    <a href="{{ route('hasil_sidang.tugas_akhir.index') }}"
                        class="menu-button flex items-center p-2 rounded-lg {{ request()->routeIs('hasil_sidang.tugas_akhir.*') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                        <svg class="w-7 h-6 {{ request()->routeIs('hasil_sidang.tugas_akhir.*') ? 'text-white' : 'text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="ms-3">Hasil Sidang TA</span>
                    </a>
                </li>
            @elseif ( auth()->user()->role === 'Mahasiswa' )
            <li class="mb-2">
                    <a href="{{ route('hasil_sidang.tugas_akhir.index_mahasiswa') }}"
                        class="menu-button flex items-center p-2 rounded-lg{{ request()->routeIs('hasil_sidang.tugas_akhir.*') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                        <svg class="w-7 h-6 {{ request()->routeIs('hasil_sidang.tugas_akhir.*') ? 'text-white' : 'text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/>
                        </svg>
                        <span class="ms-3">Hasil Sidang TA</span>
                    </a>
                </li>
            @endif


            {{-- BERITA ACARA --}}
            @if(auth()->user()->role === 'Mahasiswa' && auth()->user()->mahasiswa)
                <li class="mb-2">
                    <button type="button"
                        class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group
                        {{ request()->routeIs('berita_acara.*') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}"
                        aria-controls="dropdown-beritaacara" data-collapse-toggle="dropdown-beritaacara">
                        <svg class="w-7 h-6 {{ request()->routeIs('berita_acara.*') ? 'text-white' :  'text-gray-500' }}"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="flex-1 ms-3 text-left whitespace-nowrap">Berita Acara</span>
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="dropdown-beritaacara" class="{{ request()->routeIs('berita_acara.*') ? 'block' : 'hidden' }} py-2 space-y-2 submenu" data-collapse-id="dropdown-beritaacara">
                        <li>
                            <a href="{{ route('berita_acara.seminar_proposal') }}"
                                class="submenu-link flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg group
                                {{ request()->routeIs('berita_acara.seminar_proposal') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('berita_acara.seminar_proposal') ? 'text-white' : 'text-gray-500' }}"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4h-4Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Seminar Proposal
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('berita_acara.sidang_tugas_akhir') }}"
                                class="submenu-link flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg group
                                {{ request()->routeIs('berita_acara.sidang_tugas_akhir') ? 'bg-blue-600 text-white' : 'bg-white text-black' }}">
                                <svg class="w-7 h-6 me-4 {{ request()->routeIs('berita_acara.sidang_tugas_akhir') ? 'text-white' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M11 4.717c-2.286-.58-4.16-.756-7.045-.71A1.99 1.99 0 0 0 2 6v11c0 1.133.934 2.022 2.044 2.007 2.759-.038 4.5.16 6.956.791V4.717Zm2 15.081c2.456-.631 4.198-.829 6.956-.791A2.013 2.013 0 0 0 22 16.999V6a1.99 1.99 0 0 0-1.955-1.993c-2.885-.046-4.76.13-7.045.71v15.081Z"
                                        clip-rule="evenodd" />
                                </svg>
                                Sidang Tugas Akhir
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

        </ul>
    </div>
</aside>

{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Event listener untuk dropdown menu
        const dropdownButtons = document.querySelectorAll("[data-collapse-toggle]");

        dropdownButtons.forEach(button => {
            button.addEventListener("click", function () {
                let targetId = this.getAttribute("data-collapse-toggle");
                let targetMenu = document.getElementById(targetId);

                if (targetMenu.classList.contains("hidden")) {
                    targetMenu.classList.remove("hidden");
                } else {
                    targetMenu.classList.add("hidden");
                }
            });
        });
    });
</script> --}}



