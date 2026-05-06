<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QuizQuest - Master</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen">
        {{-- Panggil Navbar kamu --}}
        @include('layouts.navigation')

        {{-- Tempat Header (Slot Berwarna/Judul) --}}
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        {{-- Tempat Konten Utama ($slot) --}}
        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>