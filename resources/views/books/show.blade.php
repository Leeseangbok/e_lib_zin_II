<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center text-xs sm:text-sm">
            <a href="{{ route('welcome') }}" class="text-gray-400 hover:text-white">Home</a>
            <span class="mx-2 text-gray-500">/</span>
            <a href="{{ route('books.index') }}" class="text-gray-400 hover:text-white">Books</a>
            @if ($book->category)
                <span class="mx-2 text-gray-500">/</span>
                <a href="{{ route('books.index', ['category' => $book->category->slug]) }}"
                    class="text-gray-400 hover:text-white">{{ $book->category->name }}</a>
            @endif
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-white truncate max-w-[120px] sm:max-w-none">{{ Str::limit($book->title, 30) }}</span>
        </div>
    </x-slot>

    <div class="">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-900 border border-green-600 text-green-300 px-2 py-2 sm:px-4 sm:py-3 rounded-lg relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-4 sm:gap-6 lg:gap-10">
                <div class="w-full lg:w-1/4 lg:sticky top-6 h-max">
                    <div class="bg-gray-800 p-2 sm:p-4 rounded-lg shadow-lg">
                        <img src="{{ $book->cover_image_url ?? 'https://via.placeholder.com/400x600.png?text=No+Cover' }}"
                            alt="Cover of {{ $book->title }}"
                            class="w-24 h-auto sm:w-full mx-auto rounded-md shadow-md">
                        <div class="mt-4 flex flex-col gap-2 sm:gap-3">
                            <a href="{{ route('books.read', $book) }}"
                                class="w-full text-center py-2 sm:py-3 px-2 sm:px-4 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 transition text-sm sm:text-base">Read
                                Now</a>
                            @auth
                                @if (Auth::user()->favoriteBooks->contains($book))
                                    <form action="{{ route('library.remove', $book) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full py-2 sm:py-3 px-2 sm:px-4 bg-red-600 text-white font-bold rounded-md hover:bg-red-700 transition text-sm sm:text-base">Remove
                                            from Library</button>
                                    </form>
                                @else
                                    <form action="{{ route('library.add', $book) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full py-2 sm:py-3 px-2 sm:px-4 bg-blue-600 text-white font-bold rounded-md hover:bg-blue-700 transition text-sm sm:text-base">Add
                                            to Library</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-3/4">
                    <div class="bg-gray-800 p-3 sm:p-6 md:p-8 rounded-lg shadow-lg">
                        <h1 class="text-xl sm:text-3xl md:text-5xl font-bold text-white leading-tight break-words">
                            {{ $book->title }}</h1>
                        <p class="mt-2 text-sm sm:text-xl text-gray-400">by <a href="#"
                                class="text-indigo-400 hover:underline">{{ $book->author }}</a></p>

                        <div class="flex flex-col xs:flex-row items-start xs:items-center mt-4 gap-1 xs:gap-0">
                            <x-star-rating :rating="$book->reviews->avg('rating')" />
                            <span
                                class="ml-0 xs:ml-2 text-gray-400 text-xs sm:text-base">({{ number_format($book->reviews->avg('rating'), 1) }}
                                average rating)</span>
                        </div>

                        <div class="mt-4 sm:mt-6 border-t border-gray-700 pt-4 sm:pt-6">
                            <h3 class="text-lg sm:text-2xl font-semibold text-white mb-2 sm:mb-4">Description</h3>
                            <p class="text-gray-300 leading-relaxed text-sm sm:text-base">{{ $book->description }}</p>
                        </div>

                        <div class="mt-4 sm:mt-6 border-t border-gray-700 pt-4 sm:pt-6">
                            <h3 class="text-lg sm:text-2xl font-semibold text-white mb-2 sm:mb-4">Details</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 text-gray-300">
                                <div class="bg-gray-700 p-2 sm:p-4 rounded-lg text-xs sm:text-base">
                                    <span class="font-semibold text-white">Publication Date:</span>
                                    {{ $book->publication_date ? \Carbon\Carbon::parse($book->publication_date)->format('F j, Y') : 'N/A' }}
                                </div>
                                <div class="bg-gray-700 p-2 sm:p-4 rounded-lg text-xs sm:text-base">
                                    <span class="font-semibold text-white">Publisher:</span>
                                    {{ $book->publisher ?? 'N/A' }}
                                </div>
                                <div class="bg-gray-700 p-2 sm:p-4 rounded-lg text-xs sm:text-base">
                                    <span class="font-semibold text-white">Language:</span>
                                    {{ Str::upper($book->language) }}
                                </div>
                                <div class="bg-gray-700 p-2 sm:p-4 rounded-lg text-xs sm:text-base">
                                    <span class="font-semibold text-white">Pages:</span>
                                    {{ $book->page_count ?? 'N/A' }}
                                </div>
                                <div
                                    class="bg-gray-700 p-2 sm:p-4 rounded-lg col-span-1 sm:col-span-2 text-xs sm:text-base">
                                    <span class="font-semibold text-white">ISBN:</span> {{ $book->isbn ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        {{-- Gutenberg Details Section --}}
                        @if (!empty($gutenbergData['subjects']) || !empty($gutenbergData['bookshelves']))
                            <div class="mt-4 sm:mt-6 border-t border-gray-700 pt-4 sm:pt-6">
                                <h3 class="text-lg sm:text-2xl font-semibold text-white mb-2 sm:mb-4">Book Details</h3>
                                <div class="flex flex-col gap-2 sm:gap-4 text-gray-300">
                                    @if (!empty($gutenbergData['subjects']))
                                        <div>
                                            <h4 class="font-semibold text-white mb-1 sm:mb-2">Subjects:</h4>
                                            <div class="flex flex-wrap gap-1 sm:gap-2">
                                                @foreach (array_slice($gutenbergData['subjects'], 0, 10) as $subject)
                                                    <span
                                                        class="bg-gray-700 text-xs sm:text-sm font-medium px-2 sm:px-3 py-1 rounded-full">{{ $subject }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if (!empty($gutenbergData['bookshelves']))
                                        <div>
                                            <h4 class="font-semibold text-white mb-1 sm:mb-2">Bookshelves:</h4>
                                            <div class="flex flex-wrap gap-1 sm:gap-2">
                                                @foreach (array_slice($gutenbergData['bookshelves'], 0, 10) as $bookshelf)
                                                    <span
                                                        class="bg-gray-700 text-xs sm:text-sm font-medium px-2 sm:px-3 py-1 rounded-full">{{ $bookshelf }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        {{-- End of Gutenberg Details Section --}}

                    </div>

                    <div id="reviews" class="mt-6 sm:mt-10 bg-gray-800 p-4 sm:p-8 rounded-lg shadow-lg">
                        <h3
                            class="text-2xl sm:text-3xl font-semibold text-white border-b border-gray-700 pb-2 sm:pb-4 mb-4 sm:mb-6">
                            Community
                            Reviews ({{ $book->reviews->count() }})</h3>

                        @auth
                            <form action="{{ route('reviews.store', $book) }}" method="POST"
                                class="bg-gray-700 p-4 sm:p-6 rounded-lg mb-6 sm:mb-8">
                                @csrf
                                <h4 class="text-lg sm:text-xl font-semibold text-white mb-2 sm:mb-4">Leave a Review</h4>
                                <div class="space-y-2 sm:space-y-4">
                                    <div>
                                        <label for="rating"
                                            class="block text-xs sm:text-sm font-medium text-gray-300 mb-1">Your
                                            Rating</label>
                                        <select name="rating" id="rating"
                                            class="w-full bg-gray-800 border-gray-600 text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs sm:text-base"
                                            required>
                                            <option value="" disabled selected>Select a rating...</option>
                                            <option value="5">★★★★★ (Excellent)</option>
                                            <option value="4">★★★★☆ (Great)</option>
                                            <option value="3">★★★☆☆ (Good)</option>
                                            <option value="2">★★☆☆☆ (Fair)</option>
                                            <option value="1">★☆☆☆☆ (Poor)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="content"
                                            class="block text-xs sm:text-sm font-medium text-gray-300 mb-1">Your
                                            Review</label>
                                        <textarea name="content" id="content" rows="3"
                                            class="w-full bg-gray-800 border-gray-600 text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs sm:text-base"
                                            placeholder="Share your thoughts..." required minlength="10"></textarea>
                                    </div>
                                    <div>
                                        <button type="submit"
                                            class="w-full px-3 py-2 sm:px-5 sm:py-2.5 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition text-sm sm:text-base">Submit
                                            Review</button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="bg-gray-700 p-4 sm:p-6 rounded-lg text-center">
                                <p class="text-gray-300 text-xs sm:text-base"><a href="{{ route('login') }}"
                                        class="text-indigo-400 font-semibold hover:underline">Log in</a> or <a
                                        href="{{ route('register') }}"
                                        class="text-indigo-400 font-semibold hover:underline">register</a> to leave a
                                    review.</p>
                            </div>
                        @endauth

                        <div id="reviews-list" class="space-y-4 sm:space-y-6">
                            @forelse($book->reviews as $review)
                                <div class="review-item border-t border-gray-700 pt-4 sm:pt-6">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center">
                                        <div class="font-bold text-white text-xs sm:text-base">
                                            {{ $review->user->name }}</div>
                                        <div class="sm:ml-auto text-xs text-gray-500">
                                            {{ $review->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="flex items-center mt-1 mb-2">
                                        <x-star-rating :rating="$review->rating" />
                                    </div>
                                    <p class="text-gray-300 leading-relaxed text-xs sm:text-base">
                                        {{ $review->content }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500 italic text-xs sm:text-base">This book has no reviews yet.</p>
                            @endforelse
                        </div>

                        @if ($book->reviews->count() > 5)
                            <div class="text-center mt-4 sm:mt-6">
                                <button id="see-more-reviews"
                                    class="text-indigo-400 font-semibold hover:underline text-sm sm:text-base">See
                                    More</button>
                            </div>
                        @endif
                    </div>

                    @if ($relatedBooks->isNotEmpty())
                        <div class="mt-6 sm:mt-10">
                            <h3 class="text-2xl sm:text-3xl font-semibold text-white mb-4 sm:mb-6">Related Books</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach ($relatedBooks as $relatedBook)
                                    <div
                                        class="flex flex-col bg-gray-900 rounded-lg overflow-hidden shadow hover:shadow-md transition-shadow duration-300 h-full">
                                        <a href="{{ route('books.show', $relatedBook) }}"
                                            class="flex flex-col h-full">
                                            <img class="w-full object-cover rounded-t-lg transition-transform duration-300 ease-in-out hover:scale-105 aspect-[2/3] max-h-56 md:max-h-72 lg:max-h-80"
                                                src="{{ $relatedBook->cover_image_url ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                                alt="Cover of {{ $relatedBook->title }}">
                                            <div class="flex flex-col flex-grow p-2 sm:p-3 md:p-4">
                                                <h3
                                                    class="font-semibold text-xs sm:text-sm md:text-base text-white truncate">
                                                    {{ $relatedBook->title }}
                                                </h3>
                                                <p class="text-[10px] sm:text-xs md:text-sm text-gray-300 truncate">
                                                    {{ $relatedBook->author }}
                                                </p>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if ($book->reviews->count() > 5)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const reviewsList = document.getElementById('reviews-list');
                const seeMoreButton = document.getElementById('see-more-reviews');
                const reviewItems = reviewsList.getElementsByClassName('review-item');

                // Initially hide reviews after the 5th one
                if (reviewItems.length > 5) {
                    for (let i = 5; i < reviewItems.length; i++) {
                        reviewItems[i].style.display = 'none';
                    }
                } else {
                    seeMoreButton.style.display = 'none';
                }

                seeMoreButton.addEventListener('click', function() {
                    // Show all hidden reviews
                    for (let i = 5; i < reviewItems.length; i++) {
                        reviewItems[i].style.display = 'block';
                    }
                    // Hide the "See More" button after it's clicked
                    seeMoreButton.style.display = 'none';
                });
            });
        </script>
    @endif
    <style>
        @media (max-width: 640px) {

            .sm\:p-4,
            .sm\:p-6,
            .sm\:p-8 {
                padding: 1rem !important;
            }

            .sm\:gap-3,
            .sm\:gap-4,
            .sm\:gap-6,
            .sm\:gap-8 {
                gap: 0.5rem !important;
            }

            .sm\:text-base,
            .sm\:text-xl,
            .sm\:text-2xl,
            .sm\:text-3xl,
            .sm\:text-4xl,
            .sm\:text-5xl {
                font-size: 1rem !important;
            }

            .sm\:w-full {
                width: 100% !important;
            }

            .sm\:h-64 {
                height: 10rem !important;
            }

            .sm\:col-span-2 {
                grid-column: span 1 / span 1 !important;
            }
        }
    </style>
</x-app-layout>
