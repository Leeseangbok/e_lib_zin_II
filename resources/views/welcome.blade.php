<x-app-layout>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css">
    @endpush

    <div class="pb-12">
        <div class="max-w-7xl mx-auto h-full px-4 sm:px-6 lg:px-8">
            <div class="relative shadow-lg bg-gray-300 rounded-b-lg h-[350px] sm:h-[400px] md:h-[500px] flex flex-col justify-center items-center p-4 sm:p-8 mb-12 text-center bg-cover bg-center bg-no-repeat"
                style="background-image: url('{{ asset('background.png') }}');">
                <div class="absolute inset-0 bg-gray-900 opacity-50 rounded-b-lg"></div>
                <div class="relative z-10">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white">Welcome to the Library</h1>
                    <p class="mt-2 text-base sm:text-lg text-gray-200">Discover classic literature from Project Gutenberg.</p>
                    <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('books.index') }}"
                            class="inline-block rounded-lg bg-indigo-600 px-5 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 w-full sm:w-auto text-center">Browse
                            All Books</a>
                        <a href="#categories-section"
                            class="inline-block rounded-lg bg-gray-200 px-5 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-300 w-full sm:w-auto text-center">Browse
                            Categories</a>
                    </div>
                </div>
            </div>

            <div>
                <div class="max-w-7xl mx-auto">
                    @foreach ($collections as $title => $books)
                        <x-book-carousel :title="$title" :books="$books" />
                    @endforeach

                    <div id="categories-section" class="mt-12">
                        <x-category-filter-list :categories="$categories" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var splides = document.querySelectorAll('.splide');
                for (var i = 0; i < splides.length; i++) {
                    new Splide(splides[i], {
                        perPage: 5,
                        perMove: 1,
                        gap: '1rem',
                        pagination: false,
                        breakpoints: {
                            1280: {
                                perPage: 4,
                            },
                            1024: {
                                perPage: 3,
                            },
                            768: {
                                perPage: 2,
                            },
                            640: {
                                perPage: 1,
                            },
                        },
                    }).mount();
                }
            });
        </script>
    @endpush

</x-app-layout>
