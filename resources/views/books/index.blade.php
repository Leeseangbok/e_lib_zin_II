<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $categoryName ?? 'Browse All Books' }}
        </h2>
        <p class="text-gray-400 text-sm mt-1">
            {{ $books['count'] }} book{{ $books['count'] !== 1 ? 's' : '' }} found
            @if (request('search'))
                for "{{ request('search') }}"
            @endif
        </p>
        </div>
    </x-slot>

    <div class="">
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
                            <div class="flex flex-col bg-gray-900 rounded-lg overflow-hidden shadow hover:shadow-lg transition-shadow duration-300 group">
                                <a href="{{ route('books.show', $book['id']) }}" class="flex flex-col h-full">
                                    <div class="relative overflow-hidden">
                                        <img class="w-full object-cover rounded-t-lg transition-transform duration-300 ease-in-out group-hover:scale-105
                                            h-40 sm:h-48 md:h-56 lg:h-64 xl:h-72"
                                            src="{{ $book['formats']['image/jpeg'] ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                            alt="Cover of {{ $book['title'] }}">
                                    </div>
                                    <div class="flex flex-col flex-grow p-2 sm:p-3 justify-between">
                                        <div>
                                            <p class="text-xs text-indigo-400 font-semibold uppercase truncate">{{ $book['category_name'] ?? 'Uncategorized' }}</p>
                                            <h3 class="font-semibold text-xs sm:text-sm text-white truncate mt-1" title="{{ $book['title'] }}">
                                                {{ $book['title'] }}
                                            </h3>
                                            <p class="text-[10px] sm:text-xs text-gray-400 truncate">
                                                {{ $book['authors'][0]['name'] ?? 'Unknown Author' }}
                                            </p>
                                        </div>
                                        <div class="mt-2">
                                            <x-star-rating :rating="$book['average_rating'] ?? 0" size="small" />
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <p class="col-span-full text-center text-gray-400 py-10">No books found.</p>
                        @endforelse
                    </div>

                    {{-- Numbered Pagination --}}
                    <div class="mt-8 flex justify-center items-center">
                        <nav class="flex items-center space-x-2">
                            @php
                                $totalBooks = $books['count'];
                                $perPage = 32; // As per Gutendex API default
                                $totalPages = ceil($totalBooks / $perPage);
                                $currentPage = request()->input('page', 1);
                                $window = 2; // Number of pages to show on each side of the current page
                            @endphp

                            {{-- Previous Page Link --}}
                            @if ($books['previous'])
                                @php
                                    $prev_params = [];
                                    parse_str(parse_url($books['previous'], PHP_URL_QUERY), $prev_params);
                                    $prev_page = $prev_params['page'] ?? $currentPage - 1;
                                @endphp
                                <a href="{{ route('books.index', ['page' => $prev_page] + request()->except('page')) }}" class="px-3 py-1 text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg transition text-sm">
                                    &larr;
                                </a>
                            @else
                                <span class="px-3 py-1 text-gray-500 bg-gray-700 rounded-lg cursor-not-allowed text-sm">&larr;</span>
                            @endif

                            {{-- Page Number Links --}}
                            @for ($i = 1; $i <= $totalPages; $i++)
                                @if ($i == 1 || $i == $totalPages || ($i >= $currentPage - $window && $i <= $currentPage + $window))
                                    <a href="{{ route('books.index', ['page' => $i] + request()->except('page')) }}" class="px-3 py-1 text-white rounded-lg transition text-sm {{ $i == $currentPage ? 'bg-indigo-800' : 'bg-indigo-600 hover:bg-indigo-500' }}">
                                        {{ $i }}
                                    </a>
                                @elseif ($i == $currentPage - $window - 1 || $i == $currentPage + $window + 1)
                                    <span class="px-3 py-1 text-gray-500">...</span>
                                @endif
                            @endfor

                            {{-- Next Page Link --}}
                            @if ($books['next'])
                                @php
                                    $next_params = [];
                                    parse_str(parse_url($books['next'], PHP_URL_QUERY), $next_params);
                                    $next_page = $next_params['page'] ?? $currentPage + 1;
                                @endphp
                                <a href="{{ route('books.index', ['page' => $next_page] + request()->except('page')) }}" class="px-3 py-1 text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg transition text-sm">
                                    &rarr;
                                </a>
                            @else
                                <span class="px-3 py-1 text-gray-500 bg-gray-700 rounded-lg cursor-not-allowed text-sm">&rarr;</span>
                            @endif
                        </nav>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
