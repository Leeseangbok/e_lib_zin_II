{{-- resources/views/welcome.blade.php --}}
<x-app-layout>
    {{-- Hero Section --}}
    <div class="relative bg-gray-900">
        {{-- ... your existing hero section code ... --}}
    </div>

    {{-- Main Content --}}
    <div class="py-8 bg-gray-900 sm:py-12">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

            {{-- Category Carousel Section --}}
            <div x-data="{ activeTab: '{{ $categories[0]['slug'] ?? '' }}' }" class="mb-12">
                <h2 class="text-2xl font-bold tracking-tight text-white sm:text-3xl">Browse by Category</h2>
                <div class="mt-4">
                    <div class="sm:hidden">
                        <label for="tabs" class="sr-only">Select a category</label>
                        <select id="tabs" name="tabs" x-model="activeTab" class="block w-full rounded-md border-gray-700 bg-gray-800 text-white focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach ($categories as $category)
                                <option value="{{ $category['slug'] }}">{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="hidden sm:block">
                        <div class="border-b border-gray-700">
                            <nav class="flex -mb-px space-x-8" aria-label="Tabs">
                                @foreach ($categories as $category)
                                    <button @click="activeTab = '{{ $category['slug'] }}'"
                                            :class="{ 'border-indigo-500 text-indigo-400': activeTab === '{{ $category['slug'] }}', 'border-transparent text-gray-400 hover:text-gray-200 hover:border-gray-500': activeTab !== '{{ $category['slug'] }}' }"
                                            class="px-1 py-4 text-sm font-medium border-b-2 whitespace-nowrap">
                                        {{ $category['name'] }}
                                    </button>
                                @endforeach
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    @foreach ($categories as $category)
                        <div x-show="activeTab === '{{ $category['slug'] }}'" x-cloak>
                           <x-book-collection-shelf :title="$category['name']" :books="collect($categoryBooks[$category['slug']] ?? [])" />
                        </div>
                    @endforeach
                </div>
            </div>
            {{-- End of Category Carousel Section --}}

            {{-- Existing Book Collections --}}
            @if(isset($homePageCollections) && count($homePageCollections) > 0)
                @foreach($homePageCollections as $collection)
                    <x-book-collection-shelf :title="$collection['name']" :books="collect($collection['books'])" />
                @endforeach
            @else
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-white">No collections to display</h3>
                    <p class="mt-2 text-gray-400">Check back later for curated book collections.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
