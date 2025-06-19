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
                             {{-- Add to Library/Favorites Button --}}
                            <form action="{{ route('books.favorite.toggle') }}" method="POST">
                                @csrf
                                <input type="hidden" name="gutenberg_book_id" value="{{ $book['id'] }}">
                                <button type="submit"
                                        class="w-full py-2 px-4 font-bold rounded-md text-sm transition
                                        {{ $isFavorite ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-gray-600 hover:bg-gray-700 text-white' }}">
                                    {{ $isFavorite ? 'Remove from Library' : 'Add to Library' }}
                                </button>
                            </form>
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
                            @forelse ($book['authors'] as $author)
                                <a href="{{ route('books.index', ['search' => $author['name']]) }}"
                                   class="text-indigo-400 hover:underline">{{ $author['name'] }}</a>{{ !$loop->last ? ',' : '' }}
                            @empty
                                <span class="text-indigo-400">Unknown Author</span>
                            @endforelse
                        </p>

                        {{-- Description Section --}}
                        <div class="mt-6 border-t border-gray-700 pt-6">
                            <h3 class="text-2xl font-semibold text-white mb-4">Description</h3>
                            <div class="text-gray-300 leading-relaxed text-base prose prose-invert max-w-none">
                                 {{-- Using optional helper and providing a default message --}}
                                {!! $book['description'] ?? '<p>No description available.</p>' !!}
                            </div>
                        </div>

                        {{-- About This eBook Section --}}
                        <div class="mt-6 border-t border-gray-700 pt-6">
                            <h3 class="text-lg sm:text-2xl font-semibold text-white mb-2 sm:mb-4">About This eBook</h3>
                            <div class="space-y-3 text-gray-300 text-sm">
                                <div class="flex flex-wrap"><strong
                                        class="w-full sm:w-1/4 font-semibold text-white">Author:</strong><span
                                        class="w-full sm:w-3/4">{{ $book['authors'][0]['name'] ?? 'N/A' }}</span></div>
                                <div class="flex flex-wrap"><strong
                                        class="w-full sm:w-1/4 font-semibold text-white">EBook-No.:</strong><span
                                        class="w-full sm:w-3/4">{{ $book['id'] }}</span></div>
                                <div class="flex flex-wrap"><strong
                                        class="w-full sm:w-1/4 font-semibold text-white">Copyright:</strong><span
                                        class="w-full sm:w-3/4">{{ ($book['copyright'] ?? false) ? 'Yes' : 'No' }}</span>
                                </div>
                                <div class="flex flex-wrap"><strong
                                        class="w-full sm:w-1/4 font-semibold text-white">Downloads:</strong><span
                                        class="w-full sm:w-3/4">{{ number_format($book['download_count'] ?? 0) }} total</span>
                                </div>
                                <div class="flex flex-wrap"><strong
                                        class="w-full sm:w-1/4 font-semibold text-white">Language:</strong><span
                                        class="w-full sm:w-3/4">{{ implode(', ', $book['languages']) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Subjects & Bookshelves Section --}}
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

                    {{-- ### REVIEWS SECTION (CORRECTED) ### --}}
                    <div id="reviews" class="mt-6 sm:mt-10 bg-gray-800 p-4 sm:p-8 rounded-lg shadow-lg">
                        <h3 class="text-2xl sm:text-3xl font-semibold text-white border-b border-gray-700 pb-2 sm:pb-4 mb-4 sm:mb-6">
                            Community Reviews ({{ $reviews->count() }})
                        </h3>

                        @auth
                            <form action="{{ route('reviews.store', ['gutenbergBookId' => $book['id']]) }}" method="POST"
                                  class="bg-gray-700 p-4 sm:p-6 rounded-lg mb-6 sm:mb-8">
                                @csrf
                                <h4 class="text-lg sm:text-xl font-semibold text-white mb-2 sm:mb-4">Leave a Review</h4>
                                <div class="space-y-2 sm:space-y-4">
                                    <div>
                                        <label for="rating" class="block text-xs sm:text-sm font-medium text-gray-300 mb-1">Your Rating</label>
                                        <select name="rating" id="rating"
                                                class="w-full bg-gray-800 border-gray-600 text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs sm:text-base" required>
                                            <option value="" disabled selected>Select a rating...</option>
                                            <option value="5">★★★★★ (Excellent)</option>
                                            <option value="4">★★★★☆ (Great)</option>
                                            <option value="3">★★★☆☆ (Good)</option>
                                            <option value="2">★★☆☆☆ (Fair)</option>
                                            <option value="1">★☆☆☆☆ (Poor)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="comment" class="block text-xs sm:text-sm font-medium text-gray-300 mb-1">Your Review</label>
                                        <textarea name="comment" id="comment" rows="3"
                                                  class="w-full bg-gray-800 border-gray-600 text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs sm:text-base"
                                                  placeholder="Share your thoughts..." required minlength="10"></textarea>
                                    </div>
                                    <div>
                                        <button type="submit"
                                                class="w-full px-3 py-2 sm:px-5 sm:py-2.5 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition text-sm sm:text-base">
                                            Submit Review
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="bg-gray-700 p-4 sm:p-6 rounded-lg text-center">
                                <p class="text-gray-300 text-xs sm:text-base">
                                    <a href="{{ route('login') }}" class="text-indigo-400 font-semibold hover:underline">Log in</a> or
                                    <a href="{{ route('register') }}" class="text-indigo-400 font-semibold hover:underline">register</a> to leave a review.
                                </p>
                            </div>
                        @endauth

                        <div id="reviews-list" class="space-y-4 sm:space-y-6">
                            @forelse ($reviews as $review)
                                <div class="review-item border-t border-gray-700 pt-4 sm:pt-6">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center">
                                        <div class="font-bold text-white text-xs sm:text-base">
                                            {{ $review->user->name }}</div>
                                        <div class="sm:ml-auto text-xs text-gray-500">
                                            {{ $review->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="flex items-center mt-1 mb-2">
                                        <x-star-rating :rating="$review->rating" />
                                    </div>
                                    <p class="text-gray-300 leading-relaxed text-xs sm:text-base">
                                        {{ $review->comment }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500 italic text-xs sm:text-base">This book has no reviews yet.</p>
                            @endforelse
                        </div>

                        @if ($reviews->count() > 5)
                            <div class="text-center mt-4 sm:mt-6">
                                <button id="see-more-reviews"
                                        class="text-indigo-400 font-semibold hover:underline text-sm sm:text-base">See
                                    More</button>
                            </div>
                        @endif

                    </div> {{-- End of reviews div --}}
                    {{-- The extra @endif was here. It has been removed. --}}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    @if ($reviews->count() > 5)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const reviewsList = document.getElementById('reviews-list');
                const seeMoreButton = document.getElementById('see-more-reviews');
                const reviewItems = reviewsList.getElementsByClassName('review-item');

                for (let i = 5; i < reviewItems.length; i++) {
                    reviewItems[i].style.display = 'none';
                }

                if (seeMoreButton) {
                    seeMoreButton.addEventListener('click', function() {
                        for (let i = 5; i < reviewItems.length; i++) {
                            reviewItems[i].style.display = 'block';
                        }
                        seeMoreButton.style.display = 'none';
                    });
                }
            });
        </script>
    @endif
    @endpush
</x-app-layout>
