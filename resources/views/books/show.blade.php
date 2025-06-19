<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center text-xs sm:text-sm">
            <a href="{{ route('welcome') }}" class="text-gray-400 hover:text-white">Home</a>
            <span class="mx-2 text-gray-500">/</span>
            <a href="{{ route('books.index') }}" class="text-gray-400 hover:text-white">Books</a>
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-white truncate max-w-[150px] sm:max-w-none">{{ Str::limit($book['title'], 40) }}</span>
        </div>
    </x-slot>

    <div class="py-4 md:py-8">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-6 lg:gap-10">
                {{-- LEFT SIDEBAR --}}
                <div class="w-full lg:w-1/4 lg:sticky top-6 h-max">
                    <div class="bg-gray-800 p-3 sm:p-4 rounded-lg shadow-lg">
                        <img src="{{ $book['cover_image_url'] ?? 'https://via.placeholder.com/400x600.png?text=No+Cover' }}"
                            alt="Cover of {{ $book['title'] }}" class="w-full mx-auto rounded-md shadow-md">
                        <div class="mt-4 flex flex-col gap-3">
                            <a href="{{ route('books.read', $book['id']) }}"
                               class="w-full text-center py-3 px-4 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 transition text-base">Read
                                Now</a>
                            <button disabled
                                class="w-full py-2 px-4 bg-gray-600 text-white font-bold rounded-md cursor-not-allowed text-sm"
                                title="Database features are disabled">Add to Library</button>
                        </div>
                    </div>
                </div>

                {{-- MAIN CONTENT --}}
                <div class="w-full lg:w-3/4">
                    <div class="bg-gray-800 p-4 sm:p-6 md:p-8 rounded-lg shadow-lg">
                        {{-- Book Title and Author --}}
                        <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold text-white leading-tight break-words">
                            {{ $book['title'] }}</h1>
                        <p class="mt-2 text-base sm:text-xl text-gray-400">
                            by
                            @if (!empty($book['authors']))
                                @foreach ($book['authors'] as $author)
                                    <a href="{{ route('books.index', ['search' => $author['name']]) }}"
                                        class="text-indigo-400 hover:underline">{{ $author['name'] }}</a>{{ !$loop->last ? ',' : '' }}
                                @endforeach
                            @else
                                <span class="text-indigo-400">Unknown Author</span>
                            @endif
                        </p>

                        {{-- Description Section --}}
                        <div class="mt-6 border-t border-gray-700 pt-6">
                            <h3 class="text-2xl font-semibold text-white mb-4">Description</h3>
                            <div class="text-gray-300 leading-relaxed text-base prose prose-invert max-w-none">
                                {{ $book['description'] }}
                            </div>
                        </div>

                        {{-- About This eBook Section --}}
                        <div class="mt-6 border-t border-gray-700 pt-6">
                            <h3 class="text-lg sm:text-2xl font-semibold text-white mb-2 sm:mb-4">About This eBook</h3>
                            <div class="space-y-3 text-gray-300 text-sm">
                                <div class="flex flex-wrap"><strong class="w-full sm:w-1/4 font-semibold text-white">Author:</strong><span class="w-full sm:w-3/4">{{ $book['author'] }}</span></div>
                                <div class="flex flex-wrap"><strong class="w-full sm:w-1/4 font-semibold text-white">EBook-No.:</strong><span class="w-full sm:w-3/4">{{ $book['id'] }}</span></div>
                                <div class="flex flex-wrap"><strong class="w-full sm:w-1/4 font-semibold text-white">Copyright:</strong><span class="w-full sm:w-3/4">{{ $book['copyright_status'] }}</span></div>
                                <div class="flex flex-wrap"><strong class="w-full sm:w-1/4 font-semibold text-white">Downloads:</strong><span class="w-full sm:w-3/4">{{ number_format($book['downloads']) }} total</span></div>
                                <div class="flex flex-wrap"><strong class="w-full sm:w-1/4 font-semibold text-white">Language:</strong><span class="w-full sm:w-3/4">{{ implode(', ', $book['languages']) }}</span></div>
                            </div>
                        </div>

                        {{-- Subjects & Categories Section --}}
                        <div class="mt-6 border-t border-gray-700 pt-6">
                            <h3 class="text-2xl font-semibold text-white mb-4">Subjects and Bookshelves</h3>
                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="w-full md:w-1/2">
                                    <h4 class="font-semibold text-white mb-2">Subjects</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($book['subjects'] as $subject)
                                            <span
                                                class="bg-gray-700 text-gray-300 text-xs font-medium px-2.5 py-1 rounded-full">{{ $subject }}</span>
                                        @empty
                                            <p class="text-gray-400 text-sm">No subjects listed.</p>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="w-full md:w-1/2">
                                    <h4 class="font-semibold text-white mb-2">Bookshelves</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @forelse($book['bookshelves'] as $shelf)
                                            <span
                                                class="bg-gray-700 text-gray-300 text-xs font-medium px-2.5 py-1 rounded-full">{{ $shelf }}</span>
                                        @empty
                                            <p class="text-gray-400 text-sm">Not part of any bookshelves.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Related Books Section --}}
                    @if (!empty($relatedBooks))
                        <div class="mt-10">
                             <h2 class="text-2xl font-bold mb-4 text-white">You might also like</h2>
                            <div class="related-books-grid">
                                @foreach ($relatedBooks as $relatedBook)
                                    @if (isset($relatedBook['id']))
                                        <a href="{{ route('books.show', $relatedBook['id']) }}" class="book-card-link">
                                            <div class="book-card">
                                                <img src="{{ $relatedBook['formats']['image/jpeg'] ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                                     alt="Cover of {{ $relatedBook['title'] }}" class="book-cover">
                                                <div class="book-info">
                                                    <h3 class="book-title">{{ $relatedBook['title'] }}</h3>
                                                    <p class="book-author">{{ $relatedBook['authors'][0]['name'] ?? 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .related-books-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        @media (min-width: 768px) {
            .related-books-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        @media (min-width: 1024px) {
            .related-books-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }
        .book-card-link {
            text-decoration: none;
        }
        .book-card {
            background-color: #1F2937;
            border-radius: 0.5rem;
            overflow: hidden;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2), 0 4px 6px -2px rgba(0, 0, 0, 0.1);
        }
        .book-cover {
            width: 100%;
            height: auto;
            aspect-ratio: 2/3;
            object-fit: cover;
        }
        .book-info {
            padding: 0.75rem;
        }
        .book-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #FFFFFF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .book-author {
            font-size: 0.75rem;
            color: #9CA3AF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</x-app-layout>
