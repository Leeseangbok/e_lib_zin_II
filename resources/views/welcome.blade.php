<x-app-layout>
    {{-- Hero Section --}}
    <div class=" top-0 z-50">
        <div class="relative bg-gradient-to-r from-gray-800 via-gray-900 to-black shadow-lg rounded-b-2xl">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-48 lg:px-12 flex flex-col lg:flex-row lg:items-center lg:justify-between text-center lg:text-left">
                <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                    <span class="block">Discover your next great read.</span>
                    <span class="block text-indigo-400">Explore thousands of free books.</span>
                </h2>
                <div class="mt-8 flex flex-col gap-3 items-center lg:mt-0 lg:flex-row lg:flex-shrink-0 lg:gap-0">
                    <div class="inline-flex rounded-md shadow w-full lg:w-auto">
                        <a href="{{ route('books.index') }}"
                           class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition w-full lg:w-auto">
                            Browse All Books
                        </a>
                    </div>
                    <div class="inline-flex rounded-md shadow w-full lg:w-auto lg:ml-3 mt-3 lg:mt-0">
                        <a href="#category-filter"
                           class="inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 transition w-full lg:w-auto">
                            Explore with Categories
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="bg-gray-900">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="py-8 sm:py-12">

                @if (isset($homePageCollections) && count($homePageCollections) > 0)
                    @foreach ($homePageCollections as $collection)
                        <div class="mb-12">
                            <x-book-collection-shelf :title="$collection['name']" :books="collect($collection['books'])" />
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-12">
                        <h3 class="text-lg font-semibold text-white">No collections to display</h3>
                        <p class="mt-2 text-gray-400">Check back later for curated book collections.</p>
                    </div>
                @endif

                {{-- Featured Category Carousels --}}
                <div class="space-y-12 mt-12">
                    <x-book-collection-shelf title="Children's Books" topic="children" :books="$childrenBooks" />
                    <x-book-collection-shelf title="Fiction" topic="fiction" :books="$fictionBooks" />
                    <x-book-collection-shelf title="Mystery" topic="mystery" :books="$mysteryBooks" />
                </div>

                <div id="category-filter" class="mt-16">
                    <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                        <h3 class="text-2xl font-bold text-white mb-6 text-center">Browse by Category</h3>
                        <div class="flex flex-wrap gap-4 justify-center">
                            @foreach ($categories as $category)
                                <a href="{{ route('books.index', ['topic' => $category['slug']]) }}"
                                   class="px-6 py-3 bg-indigo-600 text-white rounded-lg text-lg font-semibold hover:bg-indigo-700 transition-colors duration-200 shadow-md min-w-[150px] text-center
                                   w-full sm:w-auto">
                                    {{ $category['name'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
