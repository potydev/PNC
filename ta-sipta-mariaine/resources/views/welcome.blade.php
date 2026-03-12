<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Pengelolaan Tugas Akhir Mahasiswa</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-4xl font-bold text-blue-600">SIPTA</h1>
            <nav class="flex items-center">
                <img src="{{ url('img/pnc.png') }}" alt="Logo PNC" class="text-gray-700 hover:text-blue-600 mr-4 w-10 h-auto">
                <img src="{{ url('img/jkb.png') }}" alt="Logo JKB" class="text-gray-700 hover:text-blue-600 w-10 h-auto">
            </nav>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center">
        <div class="container mx-auto flex flex-col lg:flex-row items-center">
            <div class="lg:w-3/5 p-6 text-center lg:text-left">
                <h2 class="text-3xl font-semibold text-gray-800 mb-4">Selamat Datang di Sistem Informasi Pengelolaan Tugas Akhir</h2>
                <p class="text-lg text-gray-600 mb-8">Kelola tugas akhir mahasiswa dengan mudah dan efisien.</p>
                <div class="flex justify-center lg:justify-start space-x-4">
                    <a href="{{ route('login') }}" class="bg-green-500 text-white px-6 py-3 rounded-md shadow hover:bg-green-700 transition w-28 text-center">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-500 text-white px-6 py-3 rounded-md shadow hover:bg-blue-700 transition w-28 text-center">Register</a>
                </div>
            </div>
            <div class="lg:w-2/5 p-6">
                <img src="{{ url('img/gkb.jpg') }}" alt="Gedung Kuliah Bersama" class="rounded-3xl shadow-lg max-w-full h-auto">
            </div>
        </div>
    </main>

    <footer class="bg-white text-center py-4">
        <p class="text-gray-600">&copy; 2025 Sistem Informasi Tugas Akhir Mahasiswa</p>
    </footer>
</body>
</html>
