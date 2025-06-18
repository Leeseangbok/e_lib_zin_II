@props(['categories'])

<div class="mt-12 bg-gray-800 rounded-lg p-6 shadow-lg">
    {{-- Title for the category filter section --}}
    <h2 class="text-2xl font-semibold text-start text-white">Filter by Category</h2>
    <div class="mt-8 bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($categories as $category)
                {{-- Each link now goes to the main book index, passing the category slug as a query parameter --}}
                <a href="{{ route('books.index', ['category' => $category->slug]) }}" class="block p-4 text-center bg-gray-50 hover:bg-indigo-100 border border-gray-200 rounded-lg transition">
                    <h3 class="font-semibold text-lg text-indigo-700">{{ $category->name }}</h3>
                </a>
            @endforeach
        </div>
    </div>
</div>
