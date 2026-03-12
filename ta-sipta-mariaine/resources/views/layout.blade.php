<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>SIPTA JKB PNC</title>
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

    <script src="../path/to/flowbite/dist/flowbite.min.js"></script>
    <link rel="stylesheet" href="path/to/your/styles.css">
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    @include('layouts.sidebar')
    <div class="overlay" id="overlay"></div>

    @include('layouts.navbar')

    <!-- Tambahkan flex-1 agar main mengambil sisa tinggi layar -->
    <main class="container mx-auto mt-4 flex-1">
        @yield('content')
    </main>

    <!-- Footer akan tetap di bawah -->
    @include('layouts.footer')

    @vite('resources/js/app.js')
    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session()->has('alert.title') && session()->has('alert.text') && session()->has('alert.icon'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '{{ session('alert.title') }}',
                    text: '{{ session('alert.text') }}',
                    icon: '{{ session('alert.icon') }}'
                });
            });
        </script>
    @endif
    </body>

</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>

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
