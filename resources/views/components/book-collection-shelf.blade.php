{{-- resources/views/components/book-collection-shelf.blade.php --}}
@props(['title', 'books'])

{{--
    The main container now has responsive padding.
    p-4 on small screens, p-6 on screens 640px and wider.
--}}
<div class="mt-10 shadow-lg bg-gray-800 rounded-lg p-4 sm:p-6">
    {{--
        The title now has a responsive font size.
        text-xl on small screens, text-2xl on screens 640px and wider.
    --}}
    <h2 class="text-xl sm:text-2xl font-semibold text-white">{{ $title }}</h2>

    @if ($books->isNotEmpty())
        <section class="splide mt-8" aria-labelledby="carousel-{{ \Illuminate\Support\Str::slug($title) }}-heading"
            data-splide='{"type":"loop","perPage":5, "perMove":1, "gap":"1rem", "pagination":false, "breakpoints": {"1024": {"perPage": 4}, "768": {"perPage": 3}, "640": {"perPage": 2} }}'>
            <div class="splide__track">
                <ul class="splide__list">
                    @foreach ($books as $book)
                        <li class="splide__slide">
                            {{--
                                Removed margin `m-2` as Splide's `gap` now handles spacing.
                                The card itself has p-2 padding.
                            --}}
                            <div class="flex flex-col bg-gray-900 p-2 rounded-lg overflow-hidden shadow-sm h-full">
                                <a href="{{ route('books.show', $book) }}">
                                    <img {{--
                                            Responsive image height.
                                            - Default (mobile): h-48 (12rem)
                                            - Medium screens (md): h-56 (14rem)
                                            - Large screens (lg): h-64 (16rem)
                                            This makes the card smaller on mobile.
                                        --}}
                                        class="h-48 md:h-56 lg:h-64 w-full object-cover rounded-lg transition-transform duration-300 ease-in-out hover:scale-105"
                                        src="{{ $book['formats']['image/jpeg'] ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                        alt="Cover of {{ $book['title'] }}">
                                </a>
                                {{--
                                    Used `flex-grow` to ensure the text container fills available space,
                                    making all cards in a row the same height.
                                --}}
                                <div class="py-4 flex flex-col flex-grow">
                                    <div>
                                        <p class="text-xs text-indigo-400 font-semibold uppercase truncate">
                                            {{ $book['category_name'] ?? 'Uncategorized' }}</p>
                                        <h3 class="font-semibold text-sm text-white truncate">{{ $book['title'] }}</h3>
                                        <p class="text-sm text-gray-400 truncate">
                                            {{ $book['authors'][0]['name'] ?? 'Unknown Author' }}</p>
                                    </div>
                                    <div class="mt-2">
                                        <x-star-rating :rating="$book['average_rating'] ?? 0" size="small" />
                                    </div>
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
