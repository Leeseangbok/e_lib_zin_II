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

                        {{-- About This eBook Section (Corrected) --}}
                        <div class="mt-6 border-t border-gray-700 pt-6">
                            <h3 class="text-lg sm:text-2xl font-semibold text-white mb-2 sm:mb-4">About This eBook</h3>
                            <div class="space-y-3 text-gray-300 text-sm">
                                <div class="flex flex-wrap"><strong
                                        class="w-full sm:w-1/4 font-semibold text-white">Author:</strong><span
                                        class="w-full sm:w-3/4">{{ Str::limit($book['author']) }}</span></div>
                                <div class="flex flex-wrap"><strong
                                        class="w-full sm:w-1/4 font-semibold text-white">EBook-No.:</strong><span
                                        class="w-full sm:w-3/4">{{ Str::limit($book['id']) }}</span></div>
                                <div class="flex flex-wrap"><strong
                                        class="w-full sm:w-1/4 font-semibold text-white">Copyright:</strong><span
                                        class="w-full sm:w-3/4">{{ Str::limit($book['copyright_status']) }}</span>
                                </div>
                                <div class="flex flex-wrap"><strong
                                        class="w-full sm:w-1/4 font-semibold text-white">Downloads:</strong><span
                                        class="w-full sm:w-3/4">{{ number_format($book['downloads']) }} total</span>
                                </div>
                                <div class="flex flex-wrap"><strong
                                        class="w-full sm:w-1/4 font-semibold text-white">Language:</strong><span
                                        class="w-full sm:w-3/4">{{ implode(', ', $book['languages']) }}</span>
                                </div>
                                <div class="flex flex-wrap"><strong
                                        class="w-full sm:w-1/4 font-semibold text-white">Published:</strong><span
                                        class="w-full sm:w-3/4">{{ $book['published_date'] ?? 'N/A' }}</span></div>
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
                    <div id="reviews" class="mt-6 sm:mt-10 bg-gray-800 p-4 sm:p-8 rounded-lg shadow-lg">
                        <h3
                            class="text-2xl sm:text-3xl font-semibold text-white border-b border-gray-700 pb-2 sm:pb-4 mb-4 sm:mb-6">
                            Community
                            Reviews ({{ isset($book['reviews']) ? count($book['reviews']) : 0 }})</h3>

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
                            @forelse(($book['reviews'] ?? []) as $review)
                                <div class="review-item border-t border-gray-700 pt-4 sm:pt-6">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center">
                                        <div class="font-bold text-white text-xs sm:text-base">
                                            {{ $review['user']['name'] ?? 'Anonymous' }}</div>
                                        <div class="sm:ml-auto text-xs text-gray-500">
                                            {{ isset($review['created_at']) ? \Carbon\Carbon::parse($review['created_at'])->diffForHumans() : '' }}
                                        </div>
                                    </div>
                                    <div class="flex items-center mt-1 mb-2">
                                        <x-star-rating :rating="$review['rating'] ?? 0" />
                                    </div>
                                    <p class="text-gray-300 leading-relaxed text-xs sm:text-base">
                                        {{ $review['content'] ?? '' }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500 italic text-xs sm:text-base">This book has no reviews yet.</p>
                            @endforelse
                        </div>

                        @if (isset($book['reviews']) && count($book['reviews']) > 5)
                            <div class="text-center mt-4 sm:mt-6">
                                <button id="see-more-reviews"
                                    class="text-indigo-400 font-semibold hover:underline text-sm sm:text-base">See
                                    More</button>
                            </div>
                        @endif
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
                                                <p class="book-author">
                                                    {{ $relatedBook['authors'][0]['name'] ?? 'N/A' }}</p>
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
    @if (isset($book['reviews']) && count($book['reviews']) > 5)
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
                } else if (seeMoreButton) {
                    seeMoreButton.style.display = 'none';
                }

                if (seeMoreButton) {
                    seeMoreButton.addEventListener('click', function() {
                        // Show all hidden reviews
                        for (let i = 5; i < reviewItems.length; i++) {
                            reviewItems[i].style.display = 'block';
                        }
                        // Hide the "See More" button after it's clicked
                        seeMoreButton.style.display = 'none';
                    });
                }
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
