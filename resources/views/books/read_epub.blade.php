<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="font-semibold text-xl text-white truncate" title="{{ $book->title }}">
                    Reading: {{ Str::limit($book->title, 50) }}
                </h2>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('books.show', $book) }}" class="text-sm text-gray-300 hover:text-white">&larr; Back to Details</a>
            </div>
        </div>
    </x-slot>

    @push('styles')
    <style>
        #viewer {
            width: 100%;
            height: calc(100vh - 150px);
        }
    </style>
    @endpush

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div id="viewer" class="bg-white shadow-lg rounded-lg"></div>
            <div class="mt-4 flex justify-between">
                <button id="prev" class="px-6 py-2 rounded-md bg-indigo-600 text-white">&larr; Previous</button>
                <button id="next" class="px-6 py-2 rounded-md bg-indigo-600 text-white">Next &rarr;</button>
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
