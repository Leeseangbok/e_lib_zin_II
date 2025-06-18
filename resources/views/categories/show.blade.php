<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Books in: {{ $category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($books->count())
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach ($books as $book)
                                <div class="flex flex-col bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                                    <a href="{{ route('books.show', $book) }}">
                                        <img class="h-64 w-full object-cover"
                                            src="{{ $book->cover_image_url ?? 'https://via.placeholder.com/300x400.png?text=No+Cover' }}"
                                            alt="Cover of {{ $book->title }}">
                                    </a>
                                    <div class="p-4 flex flex-col flex-grow">
                                        <h3 class="font-bold text-lg truncate">{{ $book->title }}</h3>
                                        <p class="text-sm text-gray-600">{{ $book->author }}</p>
                                        <div class="mt-auto pt-4">
                                            <a href="{{ route('books.show', $book) }}"
                                                class="text-indigo-600 hover:text-indigo-800">View Details &rarr;</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $books->links() }}
                        </div>
                    @else
                        <p>There are no books in this category yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
