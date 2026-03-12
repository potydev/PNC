<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SIPTA - Sistem Informasi Pengelolaan Tugas Akhir</title>
        <link rel="icon" href="{{ asset('img/jkb.png') }}" type="image/png">
        {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}

        <!-- Fonts -->
        {{-- <link rel="preconnect" href="https://fonts.bunny.net"> --}}
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

            <style>
        .active {
            background-color: blue; /* Warna latar biru saat aktif */
            color: white !important; /* Warna teks putih saat aktif */
        }

        .active svg {
            fill: white !important; /* Warna ikon putih saat aktif */
        }

        button:hover,
        a:hover {
            background-color: blue; /* Warna latar biru saat hover */
            color: white !important; /* Warna teks putih saat hover */
        }

        button:hover svg,
        a:hover svg {
            fill: white !important; /* Warna ikon putih saat hover */
        }

        .submenu {
            margin-left: 48px; /* Jarak ke kiri dari menu utama */
        }
    </style>

    {{-- Tom Select --}}
    <link href="https://cdn.jsdelivr.net/npm/tom-select@latest/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@latest/dist/js/tom-select.complete.min.js"></script>
    <style>
        .tom-select .option {
            color: black; /* teks putih untuk opsi */
        }

        .tom-select .option:hover {
            background-color: #6B7280; /* gray-600 */
            color: white; /* teks putih */
        }

        .tom-select .dropdown {
            background-color: #6B7280; /* gray-500 */
            color: white; /* teks putih */

        }
        .tom-select .dropdown-content {
            background-color: #6B7280; /* gray-500 */
            color: white; /* teks putih */
        }

        .tom-select .active {
            background-color: #6B7280; /* gray-500 */
            color: white; /* teks putih */
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    </head>
    <body class="font-sans antialiased flex flex-col">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navbar')
            @include('layouts.sidebar')

            <!-- Page Heading -->
            {{-- @isset($header)
                <header class="bg- shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset --}}

            <!-- Page Content -->
            <main class="sm:mt-[54px]">
                @yield('content')
            </main>
        </div>

{{-- <script>
    new TomSelect('#pembimbing_utama_id');
</script> --}}



<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>

<script src="//unpkg.com/alpinejs" defer></script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#datepicker-autohide", {
            dateFormat: "Y-m-d", // Sesuaikan format tanggal sesuai kebutuhan
            allowInput: true, // Memungkinkan pengguna untuk mengetik tanggal
        });
    });
</script>

<script>
    // Fokus pada input pencarian dan pindahkan kursor ke akhir saat halaman dimuat
    window.onload = function() {
        const searchInput = document.getElementById('search-input');
        searchInput.focus(); // Fokus pada input
        searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length); // Pindahkan kursor ke akhir
    };
</script>

{{-- jquery --}}
{{--
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#program_studi_id').select2({
            placeholder: "Pilih Program Studi",
            allowClear: true
        });
    });
</script> --}}

{{-- tom select --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    new TomSelect('#program_studi_id', {
        placeholder: 'Pilih Program Studi',
        maxItems: 1,
        allowEmptyOption: true,
        closeAfterSelect: true,
        plugins: ['dropdown_input'],
        onFocus: function () {
            this.open(); // Langsung buka saat input fokus
        }
    });
</script> --}}

    </body>
</html>
