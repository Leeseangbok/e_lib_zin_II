<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-white leading-tight">
            {{ __('My Library') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-100">

                    @if ($favoriteBooks->isNotEmpty())
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-7 gap-2 sm:gap-4">

                            @foreach ($favoriteBooks as $book)
                                <div class="flex flex-col bg-gray-900 border border-gray-700 rounded-md sm:rounded-lg overflow-hidden h-full shadow-md">
                                    <a href="{{ route('books.show', $book) }}" class="group flex flex-col h-full">

                                        <div class="overflow-hidden">
                                            <img class="h-28 sm:h-48 w-full object-cover rounded-t-md sm:rounded-t-lg transition-transform duration-300 group-hover:scale-105"
                                                 src="{{ $book['cover_image_url'] ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                                 alt="Cover of {{ $book['title'] }}">
                                        </div>

                                        <div class="p-2 sm:p-3 text-white flex flex-col flex-grow">
                                            <h3 class="font-bold text-xs sm:text-base truncate" title="{{ $book['title'] }}">{{ $book['title'] }}</h3>
                                            <p class="text-xs text-gray-400 mt-1 hidden sm:block">{{ $book['author'] }}</p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $favoriteBooks->links() }}
                        </div>
                    @else
                        <p class="text-lg text-center py-12">
                            Your library is empty.
                            <a href="{{ route('books.index') }}" class="text-indigo-400 hover:underline font-semibold">Browse books</a>
                            to add some!
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
