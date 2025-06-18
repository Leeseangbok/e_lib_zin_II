<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('My Library') }}
        </h2>
    </x-slot>

    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($favoriteBooks->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                            @foreach ($favoriteBooks as $book)
                                {{-- We can reuse the book card component from the home page! --}}
                                <div
                                    class="flex flex-col bg-gray-900 border p-2 border-gray-200 rounded-lg overflow-hidden">
                                    <a href="{{ route('books.show', $book) }}" class="group">

                                        <img class="h-64 w-full object-cover rounded-t-sm transition-transform duration-300 group-hover:scale-105"
                                            src="{{ $book->cover_image_url ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                            alt="Cover of {{ $book->title }}">

                                        <div class="p-4 text-white flex flex-col flex-grow">
                                            <h3 class="font-bold text-lg truncate">{{ $book->title }}</h3>
                                            <p class="text-sm text-gray-300">{{ $book->author }}</p>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $favoriteBooks->links() }}
                        </div>
                    @else
                        <p>Your library is empty. <a href="{{ route('books.index') }}"
                                class="text-indigo-600 hover:underline">Browse books</a> to add some!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
