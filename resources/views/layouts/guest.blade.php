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
    <div class="flex h-screen bg-gray-900">
        <!-- Left Sidebar -->
        <div class="hidden lg:flex w-1/2 bg-purple-900 text-white p-12 flex-col justify-between relative">
            <div class="absolute top-10 left-10">
                <h1 class="text-4xl font-bold mb-4">Welcome to E-Lib</h1>
                <p class="text-lg">Your digital library awaits.</p>
            </div>
            <div class="my-auto">
                <img src="/background.png" alt="Bookshelf and reading glasses" class="max-w-md mx-auto">
            </div>
            <div class="text-sm">
                <p><strong>Email Support:</strong> support@elib.com</p>
                <p><strong>Phone Support:</strong> +1 (234) 567-890</p>
            </div>
        </div>
        <!-- Main Content -->
        <div class="w-full lg:w-1/2 bg-gray-900 p-8 sm:p-12 flex items-center justify-center">
            <div class="w-full flex flex-col justify-center">
                {{ $slot }}
            </div>
                 </div>
    </body>
</html>
