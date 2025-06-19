<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="font-semibold text-lg sm:text-xl text-white truncate" title="{{ $book->title }}">
                    Reading: {{ Str::limit($book->title, 50) }}
                </h2>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('books.show', $book) }}" class="text-xs sm:text-sm text-gray-300 hover:text-white">&larr; Back to Details</a>
            </div>
        </div>
    </x-slot>

    @push('styles')
    <style>
        #viewer {
            width: 100%;
            height: calc(100vh - 150px);
        }
        @media (max-width: 640px) {
            #viewer {
                height: 60vh;
            }
        }
        /* Make EPUB text smaller and responsive */
        #viewer iframe, #viewer .epub-view {
            font-size: 14px !important;
        }
        @media (max-width: 640px) {
            #viewer iframe, #viewer .epub-view {
                font-size: 12px !important;
            }
        }
    </style>
    @endpush

    <div class="py-6 sm:py-12">
        <div class="max-w-full sm:max-w-4xl mx-auto px-2 sm:px-6 lg:px-8">
            <div id="viewer" class="bg-white shadow-lg rounded-lg"></div>
            <div class="mt-4 flex flex-col sm:flex-row justify-between gap-2">
                <button id="prev" class="px-4 py-2 text-xs sm:text-base rounded-md bg-indigo-600 text-white">&larr; Previous</button>
                <button id="next" class="px-4 py-2 text-xs sm:text-base rounded-md bg-indigo-600 text-white">Next &rarr;</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/epubjs/dist/epub.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var book = ePub("{{ $epubUrl }}");
            var rendition = book.renderTo("viewer", {
                width: "100%",
                height: "100%"
            });
            var displayed = rendition.display();

            // Set base font size for EPUB content
            rendition.themes.default({ 'body': { 'font-size': window.innerWidth < 640 ? '12px' : '14px' } });

            window.addEventListener('resize', function () {
                rendition.themes.default({ 'body': { 'font-size': window.innerWidth < 640 ? '12px' : '14px' } });
            });

            document.getElementById('prev').addEventListener('click', function () {
                rendition.prev();
            });

            document.getElementById('next').addEventListener('click', function () {
                rendition.next();
            });

            book.ready.then(function () {
                console.log('EPUB is ready');
            });
        });
    </script>
    @endpush
</x-app-layout>
