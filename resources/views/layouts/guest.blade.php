<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Jersey+15&family=Joti+One&family=Winky+Sans:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased overflow-hidden m-0 p-0 min-h-screen">
    <div class="flex flex-col sm:flex-row w-screen min-h-screen">
        <!-- Left Side (Hidden on small screens) -->
        <div class="hidden sm:flex w- winky sm:w-1/2 h-64 sm:h-screen bg-purple-800/90 text-white px-6 py-8 flex-col items-center shadow-lg relative overflow-hidden">
            <!-- Background image -->
            <div class="absolute inset-0 z-0 bg-cover bg-center opacity-50" style="background-image: url('/background.png');"></div>
            <div class="flex flex-col items-start justify-between h-full w-full relative z-10">
                <div class="flex items-center mb-6">
                    <img src="/e_lib_logo.png" alt="E-Lib Logo" class="w-16 h-16 rounded-full mr-4">
                    <h1 class="text-4xl joti font-bold">E-Lib</h1>
                </div>
                <div class="mt-20">
                    <h1 class="text-7xl font-bold mb-4">Welcome to E-Lib</h1>
                    <p class="text-lg">Your digital library awaits.</p>
                </div>
                <div class="text-sm">
                    <p><strong>Email Support:</strong> leeseangbok15@gmail.com</p>
                    <p><strong>Phone Support:</strong> +855 97 536 0168</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="w-full sm:w-1/2 flex flex-col sm:justify-center justify-between px-4 py-6 sm:py-8 bg-gradient-to-br from-gray-600 via-gray-500 to-gray-800 h-screen sm:h-auto">
            <!-- Header for small screens -->
            <div class="sm:hidden text-white text-center mt-5">
                <img src="/e_lib_logo.png" alt="E-Lib Logo" class="w-24 h-24 mx-auto mb-4 rounded-full">
                <h1 class="text-3xl font-bold">Welcome to E-Lib</h1>
                <p class="text-sm mt-1">Your digital library awaits.</p>
            </div>

            <!-- Main Content Box -->
            <div class="w-full max-w-2xl bg-gray-600 rounded-lg p-6 sm:p-8 shadow-lg mx-auto mt-6 sm:mt-0">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>
