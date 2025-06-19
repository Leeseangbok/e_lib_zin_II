@props(['categories'])

<div class="mt-12 bg-gray-800 rounded-xl p-6 shadow-2xl">
    {{-- Title for the category filter section --}}
    <h2 class="text-2xl font-bold text-white mb-6">Filter by Category</h2>
    <div class="bg-gray-900 p-4 rounded-lg shadow-inner">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach ($categories as $category)
                @php
                    $color = $category->color ?? 'indigo-600';
                    $bgColor = $category->bg_color ?? 'bg-gray-800';
                    $hoverBgColor = $category->hover_bg_color ?? 'hover:bg-indigo-700';
                @endphp
                <a href="{{ route('books.index', ['topic' => $category->slug]) }}"
                    class="flex flex-col items-center justify-center p-2 sm:p-3 min-h-[70px] {{ $bgColor }} {{ $hoverBgColor }} border border-gray-700 rounded-lg transition-all duration-200 shadow hover:scale-105 group">
                    <h3 class="font-semibold text-xs sm:text-sm text-white truncate">{{ $category->name }}</h3>
                </a>
            @endforeach
        </div>
    </div>
</div>
