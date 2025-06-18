@props(['title', 'books'])

<div class="mt-10 shadow-lg bg-gray-800 rounded-lg p-6">
    {{-- Title for the carousel section --}}
    <h2 class="text-2xl font-semibold text-white">{{ $title }}</h2> <!-- Removed text-center -->

    @if($books->isNotEmpty())
        <!-- The Splide Carousel -->
        <section class="splide mt-8" aria-labelledby="carousel-{{ \Illuminate\Support\Str::slug($title) }}-heading">
            <div class="splide__track">
                <ul class="splide__list">
                    @foreach($books as $book)
                        <li class="splide__slide">
                            <div class="flex flex-col bg-gray-900 p-2 rounded-lg overflow-hidden shadow-sm m-2">
                                <a href="{{ route('books.show', $book) }}">
                                    <img
                                        class="h-64 w-full object-cover rounded-lg transition-transform duration-300 ease-in-out hover:scale-105"
                                        src="{{ $book->cover_image_url ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                        alt="Cover of {{ $book->title }}">
                                </a>
                                <div class="py-4 flex flex-col flex-grow">
                                    <h3 class="font-semibold text-sm winky truncate">{{ $book->title }}</h3>
                                    <p class="text-sm text-gray-600 truncate">{{ $book->author }}</p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @else
        <p class="text-center text-gray-500 mt-4">No books to display in this section yet.</p>
    @endif
</div>
