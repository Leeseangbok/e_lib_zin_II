<x-app-layout>
    {{-- This slot is for the header in app.blade.php, we'll use it for the breadcrumbs --}}
    <x-slot name="header">
        <div class="flex items-center text-sm">
            <a href="{{ route('welcome') }}" class="text-gray-400 hover:text-white">Home</a>
            <span class="mx-2 text-gray-500">/</span>
            <a href="{{ route('books.index') }}" class="text-gray-400 hover:text-white">Books</a>
            @if ($book->category)
                <span class="mx-2 text-gray-500">/</span>
                <a href="{{ route('books.index', ['category' => $book->category->slug]) }}"
                    class="text-gray-400 hover:text-white">{{ $book->category->name }}</a>
            @endif
            <span class="mx-2 text-gray-500">/</span>
            <span class="text-white">{{ Str::limit($book->title, 30) }}</span>
        </div>
    </x-slot>

    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-900 border border-green-600 text-green-300 px-4 py-3 rounded-lg relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-10">
                <div class="w-full lg:w-1/4 lg:sticky top-6 h-max">
                    <div class="bg-gray-800 p-4 rounded-lg shadow-lg">
                        <img src="{{ $book->cover_image_url ?? 'https://via.placeholder.com/400x600.png?text=No+Cover' }}"
                            alt="Cover of {{ $book->title }}" class="w-full h-auto rounded-md shadow-md">
                        <div class="mt-4 flex flex-col gap-3">
                            <a href="{{ route('books.read', $book) }}" class="w-full text-center py-3 px-4 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 transition">Read Now</a>
                            @auth
                                @if (Auth::user()->favoriteBooks->contains($book))
                                    <form action="{{ route('library.remove', $book) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full py-3 px-4 bg-red-600 text-white font-bold rounded-md hover:bg-red-700 transition">Remove
                                            from Library</button>
                                    </form>
                                @else
                                    <form action="{{ route('library.add', $book) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full py-3 px-4 bg-blue-600 text-white font-bold rounded-md hover:bg-blue-700 transition">Add
                                            to Library</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-3/4">
                    <div class="bg-gray-800 p-8 rounded-lg shadow-lg">
                        <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight">{{ $book->title }}</h1>
                        <p class="mt-2 text-xl text-gray-400">by <a href="#"
                                class="text-indigo-400 hover:underline">{{ $book->author }}</a></p>

                        <div class="flex items-center mt-4">
                            <x-star-rating :rating="$book->reviews->avg('rating')" />
                            <span class="ml-2 text-gray-400">({{ number_format($book->reviews->avg('rating'), 1) }}
                                average rating)</span>
                        </div>

                        <div class="mt-6 border-t border-gray-700 pt-6">
                            <h3 class="text-2xl font-semibold text-white mb-4">Description</h3>
                            <p class="text-gray-300 leading-relaxed">{{ $book->description }}</p>
                        </div>

                        <div class="mt-6 border-t border-gray-700 pt-6">
                            <h3 class="text-2xl font-semibold text-white mb-4">Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-300">
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <span class="font-semibold text-white">Publication Date:</span>
                                    {{ $book->publication_date ? \Carbon\Carbon::parse($book->publication_date)->format('F j, Y') : 'N/A' }}
                                </div>
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <span class="font-semibold text-white">Publisher:</span>
                                    {{ $book->publisher ?? 'N/A' }}
                                </div>
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <span class="font-semibold text-white">Language:</span>
                                    {{ Str::upper($book->language) }}
                                </div>
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <span class="font-semibold text-white">Pages:</span>
                                    {{ $book->page_count ?? 'N/A' }}
                                </div>
                                <div class="bg-gray-700 p-4 rounded-lg col-span-1 md:col-span-2">
                                    <span class="font-semibold text-white">ISBN:</span> {{ $book->isbn ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="reviews" class="mt-10 bg-gray-800 p-8 rounded-lg shadow-lg">
                        <h3 class="text-3xl font-semibold text-white border-b border-gray-700 pb-4 mb-6">Community
                            Reviews ({{ $book->reviews->count() }})</h3>

                        @auth
                            <form action="{{ route('reviews.store', $book) }}" method="POST"
                                class="bg-gray-700 p-6 rounded-lg mb-8">
                                @csrf
                                <h4 class="text-xl font-semibold text-white mb-4">Leave a Review</h4>
                                <div class="space-y-4">
                                    <div>
                                        <label for="rating" class="block text-sm font-medium text-gray-300 mb-1">Your
                                            Rating</label>
                                        <select name="rating" id="rating"
                                            class="w-full bg-gray-800 border-gray-600 text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="" disabled selected>Select a rating...</option>
                                            <option value="5">★★★★★ (Excellent)</option>
                                            <option value="4">★★★★☆ (Great)</option>
                                            <option value="3">★★★☆☆ (Good)</option>
                                            <option value="2">★★☆☆☆ (Fair)</option>
                                            <option value="1">★☆☆☆☆ (Poor)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="content" class="block text-sm font-medium text-gray-300 mb-1">Your
                                            Review</label>
                                        <textarea name="content" id="content" rows="4"
                                            class="w-full bg-gray-800 border-gray-600 text-white rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            placeholder="Share your thoughts..."></textarea>
                                    </div>
                                    <div>
                                        <button type="submit"
                                            class="px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition">Submit
                                            Review</button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="bg-gray-700 p-6 rounded-lg text-center">
                                <p class="text-gray-300"><a href="{{ route('login') }}"
                                        class="text-indigo-400 font-semibold hover:underline">Log in</a> or <a
                                        href="{{ route('register') }}"
                                        class="text-indigo-400 font-semibold hover:underline">register</a> to leave a
                                    review.</p>
                            </div>
                        @endauth

                        <div class="space-y-6">
                            @forelse($book->reviews as $review)
                                <div class="border-t border-gray-700 pt-6">
                                    <div class="flex items-center">
                                        <div class="font-bold text-white">{{ $review->user->name }}</div>
                                        <div class="ml-auto text-xs text-gray-500">
                                            {{ $review->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="flex items-center mt-1 mb-2">
                                        <x-star-rating :rating="$review->rating" />
                                    </div>
                                    <p class="text-gray-300 leading-relaxed">{{ $review->content }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500 italic">This book has no reviews yet.</p>
                            @endforelse
                        </div>
                    </div>

                    @if ($relatedBooks->isNotEmpty())
                        <div class="mt-10">
                            <h3 class="text-3xl font-semibold text-white mb-6">Related Books</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                @foreach ($relatedBooks as $relatedBook)
                                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden group">
                                        <a href="{{ route('books.show', $relatedBook) }}">
                                            <img src="{{ $relatedBook->cover_image_url ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                                alt="{{ $relatedBook->title }}"
                                                class="h-64 w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                            <div class="p-4">
                                                <h4 class="font-bold text-white truncate">{{ $relatedBook->title }}
                                                </h4>
                                                <p class="text-sm text-gray-400">{{ $relatedBook->author }}</p>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif {{-- <-- THIS IS THE FIX --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
