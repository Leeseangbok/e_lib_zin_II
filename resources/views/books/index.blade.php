<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $categoryName ?? 'Browse All Books' }}
        </h2>
    </x-slot>

    <div class="py-8 px-2 sm:px-4 md:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-white">

                    <form method="GET" action="{{ route('books.index') }}" class="mb-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <input type="text" name="search" placeholder="Search for books by title or author..."
                                class="w-full bg-gray-900 border-gray-700 p-4 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                value="{{ request('search') }}">
                            <button type="submit"
                                class="sm:ml-4 inline-flex items-center justify-center px-8 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo-500 active:bg-indigo-700 disabled:opacity-25 transition">
                                Search
                            </button>
                        </div>
                    </form>

                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3 sm:gap-6">
                        @forelse ($books['results'] as $book)
                            <div class="flex flex-col bg-gray-900 rounded-lg overflow-hidden shadow hover:shadow-lg transition-shadow duration-300">
                                <a href="{{ route('books.show', $book['id']) }}" class="flex flex-col h-full">
                                    <img class="w-full object-cover rounded-t-lg transition-transform duration-300 ease-in-out hover:scale-105
                                        h-40 sm:h-48 md:h-56 lg:h-64 xl:h-72"
                                        src="{{ $book['formats']['image/jpeg'] ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                        alt="Cover of {{ $book['title'] }}">
                                    <div class="flex flex-col flex-grow p-2 sm:p-3">
                                        <h3 class="font-semibold text-xs sm:text-sm text-white truncate" title="{{ $book['title'] }}">
                                            {{ $book['title'] }}
                                        </h3>
                                        <p class="text-[10px] sm:text-xs text-gray-400 truncate">
                                            {{ $book['authors'][0]['name'] ?? 'Unknown Author' }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <p class="col-span-full text-center text-gray-400 py-10">No books found.</p>
                        @endforelse
                    </div>

                    {{-- Simplified Pagination for API --}}
                    <div class="mt-8 flex justify-between items-center">
                        {{-- Previous Page Link --}}
                        @if ($books['previous'])
                             {{-- We need to extract the page number from the API's URL --}}
                            @php
                                $prev_params = [];
                                parse_str(parse_url($books['previous'], PHP_URL_QUERY), $prev_params);
                                $prev_page = $prev_params['page'] ?? 1;
                            @endphp
                            <a href="{{ route('books.index', ['page' => $prev_page] + request()->except('page')) }}" class="px-4 py-2 text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg transition text-sm">
                                ← Previous
                            </a>
                        @else
                            <span class="px-4 py-2 text-gray-500 bg-gray-700 rounded-lg cursor-not-allowed text-sm">← Previous</span>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($books['next'])
                            @php
                                $next_params = [];
                                parse_str(parse_url($books['next'], PHP_URL_QUERY), $next_params);
                                $next_page = $next_params['page'] ?? 1;
                            @endphp
                            <a href="{{ route('books.index', ['page' => $next_page] + request()->except('page')) }}" class="px-4 py-2 text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg transition text-sm">
                                Next →
                            </a>
                        @else
                            <span class="px-4 py-2 text-gray-500 bg-gray-700 rounded-lg cursor-not-allowed text-sm">Next →</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
