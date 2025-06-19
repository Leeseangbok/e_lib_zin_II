<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ $categoryName ? 'Books in: ' . $categoryName : 'Browse All Books' }}
        </h2>
    </x-slot>

    <div class="px-2 sm:px-4 md:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-white">

                    <!-- Search Form -->
                    <form method="GET" action="{{ route('books.index') }}" class="mb-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <input type="text" name="search" placeholder="Search by title or author..."
                                class="w-full bg-gray-900 border-gray-100 p-4 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                value="{{ request('search') }}">
                            <button type="submit"
                                class="sm:ml-4 inline-flex items-center justify-center px-8 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo-500 active:bg-indigo-700 disabled:opacity-25 transition">
                                Search
                            </button>
                        </div>
                    </form>

                    <!-- Book Grid -->
                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3 sm:gap-6">
                        @forelse ($books as $book)
                            <div
                                class="flex flex-col bg-gray-900 rounded-lg overflow-hidden shadow hover:shadow-md transition-shadow duration-300">
                                <a href="{{ route('books.show', $book['id']) }}" class="flex flex-col h-full">
                                    <img class="w-full object-cover rounded-t-lg transition-transform duration-300 ease-in-out hover:scale-105
                    h-40 sm:h-48 md:h-56 lg:h-64 xl:h-72"
                                       src="{{ $book['formats']['image/jpeg'] ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                        alt="Cover of {{ $book['title'] }}">

                                    <div class="flex flex-col flex-grow p-2 sm:p-3 md:p-4">
                                        <h3 class="font-semibold text-xs sm:text-sm md:text-base text-white truncate">
                                             {{ $book['title'] }}
                                        </h3>
                                        <p class="text-[10px] sm:text-xs md:text-sm text-gray-300 truncate">
                                            {{ $book['authors'][0]['name'] ?? 'Unknown Author' }}
                                        </p>
                                    </div>
                                </a>
                            </div>

                        @empty
                            <p class="col-span-full text-center text-gray-400">No books found matching your search
                                criteria.</p>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center">
                        @if ($books->hasPages())
                            <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-8">
                                <div class="w-full max-w-full overflow-x-auto">
                                    <ul class="inline-flex whitespace-nowrap -space-x-px text-xs sm:text-sm">
                                        {{-- Previous Page --}}
                                        @if ($books->onFirstPage())
                                            <li>
                                                <span
                                                    class="px-2 sm:px-4 py-1 sm:py-2 text-gray-400 bg-gray-700 rounded-l-lg cursor-not-allowed text-xs sm:text-sm">←
                                                    Previous</span>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ $books->previousPageUrl() }}"
                                                    class="px-2 sm:px-4 py-1 sm:py-2 text-white bg-indigo-600 hover:bg-indigo-500 rounded-l-lg transition text-xs sm:text-sm">←
                                                    Previous</a>
                                            </li>
                                        @endif

                                        {{-- Pagination Numbers --}}
                                        @php
                                            $current = $books->currentPage();
                                            $last = $books->lastPage();
                                        @endphp

                                        {{-- First few pages --}}
                                        @for ($page = 1; $page <= min(3, $last); $page++)
                                            <li>
                                                @if ($page == $current)
                                                    <span
                                                        class="px-2 sm:px-4 py-1 sm:py-2 text-white bg-indigo-800 font-semibold text-xs sm:text-sm">{{ $page }}</span>
                                                @else
                                                    <a href="{{ $books->url($page) }}"
                                                        class="px-2 sm:px-4 py-1 sm:py-2 text-white bg-gray-700 hover:bg-indigo-600 transition text-xs sm:text-sm">{{ $page }}</a>
                                                @endif
                                            </li>
                                        @endfor

                                        {{-- Ellipsis if needed --}}
                                        @if ($last > 5)
                                            <li>
                                                <span
                                                    class="px-2 sm:px-4 py-1 sm:py-2 text-gray-400 bg-gray-700 text-xs sm:text-sm">...</span>
                                            </li>
                                        @elseif ($last == 5)
                                            <li>
                                                @if (4 == $current)
                                                    <span
                                                        class="px-2 sm:px-4 py-1 sm:py-2 text-white bg-indigo-800 font-semibold text-xs sm:text-sm">4</span>
                                                @else
                                                    <a href="{{ $books->url(4) }}"
                                                        class="px-2 sm:px-4 py-1 sm:py-2 text-white bg-gray-700 hover:bg-indigo-600 transition text-xs sm:text-sm">4</a>
                                                @endif
                                            </li>
                                        @endif

                                        {{-- Last few pages --}}
                                        @if ($last > 3)
                                            @for ($page = max($last - 1, 4); $page <= $last; $page++)
                                                <li>
                                                    @if ($page == $current)
                                                        <span
                                                            class="px-2 sm:px-4 py-1 sm:py-2 text-white bg-indigo-800 font-semibold text-xs sm:text-sm">{{ $page }}</span>
                                                    @else
                                                        <a href="{{ $books->url($page) }}"
                                                            class="px-2 sm:px-4 py-1 sm:py-2 text-white bg-gray-700 hover:bg-indigo-600 transition text-xs sm:text-sm">{{ $page }}</a>
                                                    @endif
                                                </li>
                                            @endfor
                                        @endif

                                        {{-- Next Page --}}
                                        @if ($books->hasMorePages())
                                            <li>
                                                <a href="{{ $books->nextPageUrl() }}"
                                                    class="px-2 sm:px-4 py-1 sm:py-2 text-white bg-indigo-600 hover:bg-indigo-500 rounded-r-lg transition text-xs sm:text-sm">Next
                                                    →</a>
                                            </li>
                                        @else
                                            <li>
                                                <span
                                                    class="px-2 sm:px-4 py-1 sm:py-2 text-gray-400 bg-gray-700 rounded-r-lg cursor-not-allowed text-xs sm:text-sm">Next
                                                    →</span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </nav>
                        @endif
                    </div>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>
