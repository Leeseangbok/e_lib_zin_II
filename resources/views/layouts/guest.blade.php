<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-gray-900 min-h-screen antialiased">
    <div class="flex flex-col items-center min-h-screen bg-gray-900">
        <!-- Compact Header -->
        <div class="w-full max-w-md bg-purple-900 text-white rounded-b-lg px-4 py-6 flex flex-col items-center shadow-md">
            <h1 class="text-xl sm:text-2xl font-bold mb-1">Welcome to E-Lib</h1>
            <p class="text-sm sm:text-base mb-2">Your digital library awaits.</p>
            <img src="/background.png" alt="Bookshelf and reading glasses" class="w-20 sm:w-28 mb-2">
            <div class="text-xs text-center">
                <p><strong>Email:</strong> support@elib.com</p>
                <p><strong>Phone:</strong> +1 (234) 567-890</p>
            </div>
        </div>
        <!-- Main Content -->
        <div class="w-full max-w-md bg-gray-900 flex flex-col items-center justify-center flex-1">
            <div class="w-full">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
