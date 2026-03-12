<nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200">
    <div class="px-3 py-3 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-blue focus:outline-none focus:ring-2 focus:ring-gray-200">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                    </svg>
                </button>
                <a href="https://flowbite.com" class="flex ms-2 md:me-24">
                    <img src="{{ url('img/jkb.png') }}" alt="Deskripsi Gambar" class="w-8 h-8 ml-2 mr-3">
                    <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap">SIPTA</span>
                </a>
            </div>
            <div class="flex items-center">
                <div class="flex items-center ms-3">
                    <div>
                        <div class="flex">
                            <div class="px-4">
                                <div class="font-small text-base text-gray-800">{{ Auth::user()->name }}</div>
                            </div>
                            <button type="button" class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300" aria-expanded="false" data-dropdown-toggle="dropdown-user">
                                <span class="sr-only">Open user menu</span>
                                {{-- <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-5.jpg" alt="user photo"> --}}
                                @php
                                    $initial = strtoupper(substr(Auth::user()->name, 0, 1));
                                @endphp

                                <div class="w-8 h-8 rounded-full bg-gray-200 text-white flex items-center justify-center font-bold">
                                    {{ $initial }}
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-sm shadow-sm" id="dropdown-user">
                        <div class="py-1" role="non">
                                <div class="block px-4 py-2 text-sm text-gray-700">{{ Auth::user()->role }}</div>
                                <div class="block px-4 py-2 text-sm text-gray-700">{{ Auth::user()->email }}</div>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700" role="menuitem">Profile</a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
                                    @csrf
                                </form>
                                <a class="block px-4 py-2 text-sm text-gray-700" role="menuitem" onclick="event.preventDefault(); openLogoutModal();">
                                    {{ __('Log Out') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Modal Logout -->
<div id="logout-modal" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-45">
    <div class="relative p-4 w-full max-w-md">
        <div class="relative bg-white rounded-lg shadow-lg">
            <button type="button" class="absolute top-3 right-3 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center" onclick="closeLogoutModal()">
                <svg class="w-3 h-3" aria-hidden="true" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="p-4 text-center">
                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500">Apakah anda yakin ingin logout?</h3>
                <button id="confirm-logout" type="button" class="w-full sm:w-20 md:w-20 text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 justify-center" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Ya
                </button>
                <button type="button" class="w-full sm:w-20 md:w-20 py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-grey-200 rounded-lg border border-gray-200 hover:bg-gray-500 hover:text-white focus:z-10 focus:ring-4 focus:ring-gray-100 justify-center" onclick="closeLogoutModal()">
                    Tidak
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openLogoutModal() {
    document.getElementById('logout-modal').classList.remove('hidden');
}

function closeLogoutModal() {
    document.getElementById('logout-modal').classList.add('hidden');
}
</script>


