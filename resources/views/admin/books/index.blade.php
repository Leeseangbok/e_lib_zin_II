{{-- resources/views/admin/books/index.blade.php --}}
<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Book Management') }}
            </h2>
            <a href="{{ route('admin.books.create') }}"
                class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('Add New Book') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="w-[2000px] mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.books.index') }}" method="GET" class="mb-6">
                        <div class="flex items-center">
                            <input type="text" name="search"
                                class="w-full px-4 py-2 border rounded-l-md focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                placeholder="Search by title, author, or ISBN..." value="{{ request('search') }}">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Author
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ISBN
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Language
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($books as $book)
                                    <tr class="hover:bg-gray-50 transition ease-in-out duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                        src="{{ $book->cover_image_url ?? 'https://via.placeholder.com/150' }}"
                                                        alt="{{ $book->title }}">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $book->title }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $book->isbn }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $book->author }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $book->category->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $book->isbn }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $book->language }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.books.edit', $book) }}"
                                                class="text-white px-4 py-1 rounded bg-indigo-600  hover:text-indigo-900">Edit</a>
                                            <form action="{{ route('admin.books.destroy', $book) }}" method="POST"
                                                class="inline-block ml-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-white px-2 py-1 rounded bg-red-600 hover:text-red-900"
                                                    onclick="return confirm('Are you sure you want to delete this book? This action cannot be undone.')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            <div class="text-center py-10">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900">No books found</h3>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    Get started by creating a new book.
                                                </p>
                                                <div class="mt-6">
                                                    <a href="{{ route('admin.books.create') }}"
                                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20"
                                                            fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                        New Book
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-8 p-12">
                    @if ($books->hasPages())
                        <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-8">
                            <ul class="inline-flex -space-x-px">
                                {{-- Previous Page Link --}}
                                @if ($books->onFirstPage())
                                    <li>
                                        <span
                                            class="px-4 py-2 text-sm text-gray-400 bg-gray-200 rounded-l-lg cursor-not-allowed">←
                                            Previous</span>
                                    </li>
                                @else
                                    <li>
                                        <a href="{{ $books->previousPageUrl() }}"
                                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 rounded-l-lg transition">
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
                                                class="px-4 py-2 text-sm text-white bg-indigo-500 font-semibold">{{ $page }}</span>
                                        @else
                                            <a href="{{ $books->url($page) }}"
                                                class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">{{ $page }}</a>
                                        @endif
                                    </li>
                                @endfor

                                {{-- Show ... if needed --}}
                                @if ($last > 5)
                                    <li>
                                        <span class="px-4 py-2 text-sm text-gray-400 bg-gray-200">...</span>
                                    </li>
                                @elseif ($last == 5)
                                    <li>
                                        @if (4 == $current)
                                            <span
                                                class="px-4 py-2 text-sm text-white bg-indigo-500 font-semibold">4</span>
                                        @else
                                            <a href="{{ $books->url(4) }}"
                                                class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">4</a>
                                        @endif
                                    </li>
                                @endif

                                {{-- Always show n-1, n if last > 3 --}}
                                @if ($last > 3)
                                    @for ($page = max($last - 1, 4); $page <= $last; $page++)
                                        <li>
                                            @if ($page == $current)
                                                <span
                                                    class="px-4 py-2 text-sm text-white bg-indigo-500 font-semibold">{{ $page }}</span>
                                            @else
                                                <a href="{{ $books->url($page) }}"
                                                    class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 transition">{{ $page }}</a>
                                            @endif
                                        </li>
                                    @endfor
                                @endif

                                {{-- Next Page Link --}}
                                @if ($books->hasMorePages())
                                    <li>
                                        <a href="{{ $books->nextPageUrl() }}"
                                            class="px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 rounded-r-lg transition">
                                            Next →
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <span
                                            class="px-4 py-2 text-sm text-gray-400 bg-gray-200 rounded-r-lg cursor-not-allowed">Next
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
</x-admin-layout>
