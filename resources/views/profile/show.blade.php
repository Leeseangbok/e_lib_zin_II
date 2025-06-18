<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __("Dashboard for") }} {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-2xl font-semibold text-gray-800">Your Library</h3>
                @if($favoriteBooks->isNotEmpty())
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($favoriteBooks as $book)
                            <div class="flex flex-col bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                <a href="{{ route('books.show', $book) }}">
                                    <img class="h-64 w-full object-cover" src="{{ $book->cover_image_url ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}" alt="Cover of {{ $book->title }}">
                                </a>
                                <div class="p-4 flex flex-col flex-grow">
                                    <h3 class="font-bold text-lg truncate">{{ $book->title }}</h3>
                                    <p class="text-sm text-gray-600 truncate">{{ $book->author }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('library.index') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">View all in library &rarr;</a>
                    </div>
                @else
                    <div class="mt-4 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-600">
                            Your library is empty. <a href="{{ route('books.index') }}" class="text-indigo-600 hover:underline">Browse books</a> to add some!
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
