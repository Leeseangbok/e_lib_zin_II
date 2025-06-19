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
                {{-- LEFT SIDEBAR --}}
                <div class="w-full lg:w-1/4 lg:sticky top-6 h-max">
                    <div class="bg-gray-800 p-2 sm:p-4 rounded-lg shadow-lg">
                        <img src="{{ $book->cover_image_url ?? 'https://via.placeholder.com/400x600.png?text=No+Cover' }}"
                             alt="Cover of {{ $book->title }}"
                             class="w-24 h-auto sm:w-full mx-auto rounded-md shadow-md">
                        <div class="mt-4 flex flex-col gap-2 sm:gap-3">
                            <a href="{{ route('books.read', $book->id) }}"
                               class="w-full text-center py-2 sm:py-3 px-2 sm:px-4 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 transition text-sm sm:text-base">Read Now</a>
                            @auth
                                @if (Auth::user()->favoriteBooks->contains($book))
                                    <form action="{{ route('library.remove', $book->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full py-2 sm:py-3 px-2 sm:px-4 bg-red-600 text-white font-bold rounded-md hover:bg-red-700 transition text-sm sm:text-base">Remove from Library</button>
                                    </form>
                                @else
                                    <form action="{{ route('library.add', $book->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full py-2 sm:py-3 px-2 sm:px-4 bg-blue-600 text-white font-bold rounded-md hover:bg-blue-700 transition text-sm sm:text-base">Add to Library</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                {{-- MAIN CONTENT --}}
                <div class="w-full lg:w-3/4">
                    <div class="bg-gray-800 p-3 sm:p-6 md:p-8 rounded-lg shadow-lg">
                        <h1 class="text-xl sm:text-3xl md:text-5xl font-bold text-white leading-tight break-words">{{ $book->title }}</h1>
                        <p class="mt-2 text-sm sm:text-xl text-gray-400">by <a href="#" class="text-indigo-400 hover:underline">{{ $book->author }}</a></p>

                        <div class="flex flex-col xs:flex-row items-start xs:items-center mt-4 gap-1 xs:gap-0">
                            <x-star-rating :rating="$book->reviews->avg('rating')" />
                            <span class="ml-0 xs:ml-2 text-gray-400 text-xs sm:text-base">({{ number_format($book->reviews->avg('rating'), 1) }} average rating)</span>
                        </div>

                        {{-- Description section --}}
                        <div class="mt-4 sm:mt-6 border-t border-gray-700 pt-4 sm:pt-6">
                            <h3 class="text-lg sm:text-2xl font-semibold text-white mb-2 sm:mb-4">Description</h3>
                            <p class="text-gray-300 leading-relaxed text-sm sm:text-base">
                                @if(!empty($book->subjects))
                                    This book covers subjects such as: {{ implode(', ', array_slice($book->subjects, 0, 5)) }}.
                                @else
                                    No description available.
                                @endif
                            </p>
                        </div>

                        {{-- Details section --}}
                        <div class="mt-4 sm:mt-6 border-t border-gray-700 pt-4 sm:pt-6">
                            <h3 class="text-lg sm:text-2xl font-semibold text-white mb-2 sm:mb-4">Details</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-4 text-gray-300">
                                <div class="bg-gray-700 p-2 sm:p-4 rounded-lg text-xs sm:text-base">
                                    <span class="font-semibold text-white">Publication Date:</span> N/A
                                </div>
                                <div class="bg-gray-700 p-2 sm:p-4 rounded-lg text-xs sm:text-base">
                                    <span class="font-semibold text-white">Publisher:</span> N/A
                                </div>
                                <div class="bg-gray-700 p-2 sm:p-4 rounded-lg text-xs sm:text-base">
                                    <span class="font-semibold text-white">Language:</span>
                                    {{-- THIS IS THE FIX: Provide an empty array [] as a default if $book->languages is null --}}
                                    {{ Str::upper(implode(', ', $book->languages ?? [])) }}
                                </div>
                                <div class="bg-gray-700 p-2 sm:p-4 rounded-lg text-xs sm:text-base">
                                    <span class="font-semibold text-white">Pages:</span> N/A
                                </div>
                                <div class="bg-gray-700 p-2 sm:p-4 rounded-lg col-span-1 sm:col-span-2 text-xs sm:text-base">
                                    <span class="font-semibold text-white">ISBN:</span> N/A
                                </div>
                            </div>
                        </div>

                        {{-- "About This eBook" section --}}
                        <div class="mt-4 sm:mt-6 border-t border-gray-700 pt-4 sm:pt-6">
                            <h3 class="text-lg sm:text-2xl font-semibold text-white mb-2 sm:mb-4">About This eBook</h3>
                            <div class="space-y-3 text-gray-300">
                                <div class="flex flex-wrap"><strong class="w-full sm:w-1/4 font-semibold text-white">Author:</strong><span class="w-full sm:w-3/4">{{ $book->author }}</span></div>
                                <div class="flex flex-wrap"><strong class="w-full sm:w-1/4 font-semibold text-white">Title:</strong><span class="w-full sm:w-3/4">{{ $book->title }}</span></div>
                                <div class="flex flex-wrap"><strong class="w-full sm:w-1/4 font-semibold text-white">Category:</strong><span class="w-full sm:w-3/4">{{ $book->media_type }}</span></div>
                                <div class="flex flex-wrap"><strong class="w-full sm:w-1/4 font-semibold text-white">EBook-No.:</strong><span class="w-full sm:w-3/4">{{ $book->id }}</span></div>
                                <div class="flex flex-wrap"><strong class="w-full sm:w-1/4 font-semibold text-white">Copyright Status:</strong><span class="w-full sm:w-3/4">{{ $book->copyright ? 'Copyrighted' : 'Public domain in the USA.' }}</span></div>
                                <div class="flex flex-wrap"><strong class="w-full sm:w-1/4 font-semibold text-white">Downloads:</strong><span class="w-full sm:w-3/4">{{ number_format($book->download_count) }} total</span></div>
                            </div>
                        </div>
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

                    @if (!empty($relatedBooks))
                        <h2 class="text-xl font-bold mb-4">You might also like</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach ($relatedBooks as $relatedBook)
                                {{-- Check if the book data is valid before trying to display it --}}
                                @if (isset($relatedBook['id']) && isset($relatedBook['title']))
                                    <a href="{{ route('books.show', $relatedBook['id']) }}" class="book-card-link">
                                        <div class="book-card">
                                            {{-- Use array syntax and check if the cover image exists --}}
                                            <img src="{{ $relatedBook['formats']['image/jpeg'] ?? asset('images/default_cover.png') }}"
                                                alt="Cover of {{ $relatedBook['title'] }}" class="book-cover">
                                            <div class="book-info">
                                                {{-- Use array syntax --}}
                                                <h3 class="book-title">{{ $relatedBook['title'] }}</h3>
                                                {{-- Use array syntax and check if authors exist --}}
                                                <p class="book-author">
                                                    {{ $relatedBook['authors'][0]['name'] ?? 'Unknown Author' }}</p>
                                            </div>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
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
