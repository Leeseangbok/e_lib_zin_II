<nav x-data="{ open: false }" class="bg-gradient-to-r from-gray-700 to-gray-800 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo and Navigation -->
            <div class="flex items-center space-x-8">
                <a href="{{ route('welcome') }}" class="flex items-center space-x-2">
                    <x-application-logo class="block h-9 w-auto fill-current text-white" />
                    <span class="font-bold text-3xl text-white joti hidden sm:inline">E-Lib</span>
                </a>
                <div class="hidden md:flex space-x-6 winky">
                    <x-nav-link :href="route('welcome')" :active="request()->routeIs('welcome')">
                {{ __('Home') }}
            </x-nav-link>
            <x-nav-link :href="route('books.index')" :active="request()->routeIs('books.index')">
                {{ __('Books') }}
            </x-nav-link>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="flex-1 flex justify-center px-4">
                <form action="{{ route('books.index') }}" method="GET" class="w-full max-w-md">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search books..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-indigo-700 focus:ring-2 focus:ring-blue-400 focus:outline-none transition text-gray-100"
                            value="{{ request('search') }}">
                        <span class="absolute left-3 top-2.5 text-indigo-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                            </svg>
                        </span>
                    </div>
                </form>
            </div>

            <!-- User Auth / Links -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white hover:text-blue-600 cursor-pointer hover:border hover:border-gray-200 focus:outline-none transition">
                                <div class="text-md">{{ Auth::user()->name }}</div>
                                <svg class="ml-1 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20" fill="white">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('library.index')">
                                {{ __('My Library') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}"
                        class="font-semibold bg-indigo-600 px-5 py-2 rounded-lg text-white hover:bg-indigo-300 hover:text-gray-600 transition-colors duration-200 shadow focus:outline-none focus:ring-2 focus:ring-indigo-400">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="font-semibold bg-gray-500 px-5 py-2 rounded-lg text-white hover:bg-indigo-100 hover:text-indigo-700 transition-colors duration-200 shadow focus:outline-none focus:ring-2 focus:ring-indigo-400">Register</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>
