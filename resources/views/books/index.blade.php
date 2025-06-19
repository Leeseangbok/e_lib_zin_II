<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- If a category is being filtered, show its name. Otherwise, show "Browse Books". --}}
            {{ $categoryName ? 'Books in: ' . $categoryName : 'Browse All Books' }}
        </h2>
    </x-slot>

    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    <form method="GET" action="{{ route('books.index') }}" class="mb-6">
                        <div class="flex">
                            <input type="text" name="search" placeholder="Search by title or author..."
                                class="w-full bg-gray-900 border-gray-100 p-4 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                value="{{ request('search') }}">
                            <button type="submit"
                                class="ml-4 inline-flex items-center px-8 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring-indigo-500 active:bg-indigo-700 disabled:opacity-25 transition">Search</button>
                        </div>
                    </form>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        @forelse ($books as $book)
                            <div class="flex flex-col bg-gray-900 p-2 rounded-lg overflow-hidden">
                                <a href="{{ route('books.show', $book) }}">
                                    <img class="h-64 w-full object-cover rounded-t-lg transition-transform duration-300 ease-in-out hover:scale-105"
                                        src="{{ $book->cover_image_url ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                        alt="Cover of {{ $book->title }}">
                                    <div class="py-4 flex flex-col flex-grow winky">
                                        <h3 class="font-semibold text-sm text-whtie truncate">{{ $book->title }}</h3>
                                        <p class="text-sm text-gray-300">{{ $book->author }}</p>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <p class="col-span-full">No books found matching your search criteria.</p>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        @if ($books->hasPages())
                            <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-8">
                                <ul class="inline-flex -space-x-px">
                                    {{-- Previous Page Link --}}
                                    @if ($books->onFirstPage())
                                        <li>
                                            <span
                                                class="px-4 py-2 text-sm text-gray-400 bg-gray-700 rounded-l-lg cursor-not-allowed">←
                                                Previous</span>
                                        </li>
                                    @else
                                        <li>
                                            <a href="{{ $books->previousPageUrl() }}"
                                                class="px-4 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-500 rounded-l-lg transition">
                                                ← Previous
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @php
                                        $current = $books->currentPage();
                                        $last = $books->lastPage();
                                    @endphp

                                    {{-- Always show 1, 2, 3 --}}
                                    @for ($page = 1; $page <= min(3, $last); $page++)
                                        <li>
                                            @if ($page == $current)
                                                <span
                                                    class="px-4 py-2 text-sm text-white bg-indigo-800 font-semibold">{{ $page }}</span>
                                            @else
                                                <a href="{{ $books->url($page) }}"
                                                    class="px-4 py-2 text-sm text-white bg-gray-700 hover:bg-indigo-600 transition">{{ $page }}</a>
                                            @endif
                                        </li>
                                    @endfor

                                    {{-- Show ... if needed --}}
                                    @if ($last > 5)
                                        <li>
                                            <span class="px-4 py-2 text-sm text-gray-400 bg-gray-700">...</span>
                                        </li>
                                    @elseif ($last == 5)
                                        <li>
                                            @if (4 == $current)
                                                <span
                                                    class="px-4 py-2 text-sm text-white bg-indigo-800 font-semibold">4</span>
                                            @else
                                                <a href="{{ $books->url(4) }}"
                                                    class="px-4 py-2 text-sm text-white bg-gray-700 hover:bg-indigo-600 transition">4</a>
                                            @endif
                                        </li>
                                    @endif

                                    {{-- Always show n-1, n if last > 3 --}}
                                    @if ($last > 3)
                                        @for ($page = max($last - 1, 4); $page <= $last; $page++)
                                            <li>
                                                @if ($page == $current)
                                                    <span
                                                        class="px-4 py-2 text-sm text-white bg-indigo-800 font-semibold">{{ $page }}</span>
                                                @else
                                                    <a href="{{ $books->url($page) }}"
                                                        class="px-4 py-2 text-sm text-white bg-gray-700 hover:bg-indigo-600 transition">{{ $page }}</a>
                                                @endif
                                            </li>
                                        @endfor
                                    @endif

                                    {{-- Next Page Link --}}
                                    @if ($books->hasMorePages())
                                        <li>
                                            <a href="{{ $books->nextPageUrl() }}"
                                                class="px-4 py-2 text-sm text-white bg-indigo-600 hover:bg-indigo-500 rounded-r-lg transition">
                                                Next →
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <span
                                                class="px-4 py-2 text-sm text-gray-400 bg-gray-700 rounded-r-lg cursor-not-allowed">Next
                                                →</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
