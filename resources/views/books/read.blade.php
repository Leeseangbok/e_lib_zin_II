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

    <div x-data="chapterReader({{ Js::from($chapters) }})" x-cloak class="flex flex-col lg:flex-row">

        <aside class="w-full lg:w-80 bg-white dark:bg-gray-800 lg:h-[calc(100vh-69px)] lg:sticky top-[69px] border-r border-gray-200 dark:border-gray-700 p-6 overflow-y-auto">
            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Chapters</h3>
            <ul class="space-y-1">
                <template x-for="(chapter, index) in chapters" :key="index">
                    <li>
                        <a href="#"
                           @click.prevent="jumpToChapter(index)"
                           :class="{
                               'bg-indigo-600 text-white': currentChapterIndex === index,
                               'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700': currentChapterIndex !== index
                           }"
                           class="block p-3 rounded-md text-sm font-medium transition-colors duration-150 truncate"
                           x-text="chapter.title">
                        </a>
                    </li>
                </template>
            </ul>
        </aside>

        <main class="w-full py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 mb-6 flex items-center justify-end">
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Font Size:</span>
                        <button @click="fontSize = 'text-base'" :class="{ 'bg-indigo-600 text-white': fontSize === 'text-base', 'bg-gray-200 dark:bg-gray-700': fontSize !== 'text-base' }" class="px-3 py-1 text-sm rounded-md transition">S</button>
                        <button @click="fontSize = 'text-lg'" :class="{ 'bg-indigo-600 text-white': fontSize === 'text-lg', 'bg-gray-200 dark:bg-gray-700': fontSize !== 'text-lg' }" class="px-3 py-1 text-sm rounded-md transition">M</button>
                        <button @click="fontSize = 'text-xl'" :class="{ 'bg-indigo-600 text-white': fontSize === 'text-xl', 'bg-gray-200 dark:bg-gray-700': fontSize !== 'text-xl' }" class="px-3 py-1 text-sm rounded-md transition">L</button>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-8 sm:p-12">
                    <template x-if="chapters.length > 0 && currentChapter">
                        <div>
                            <h3 class="text-3xl font-bold text-center mb-8 text-gray-800 dark:text-gray-200" x-text="currentChapter.title"></h3>

                            <div :class="fontSize" class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-loose font-serif">
                                <div x-html="currentChapter.content"></div>
                            </div>
                        </div>
                    </template>
                    <template x-if="chapters.length === 0 || !currentChapter">
                        <div class="text-center">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Could Not Load Book</h3>
                            <p class="mt-2 text-gray-600 dark:text-gray-400">We were unable to parse the chapters for this book.</p>
                        </div>
                    </template>
                </div>

                <div class="mt-6 flex justify-between">
                     <button @click="prevChapter()" :disabled="currentChapterIndex === 0" class="px-6 py-2 rounded-md bg-indigo-600 text-white disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                         &larr; <span class="ml-2 hidden sm:inline">Previous</span>
                     </button>
                     <button @click="nextChapter()" :disabled="currentChapterIndex === chapters.length - 1" class="px-6 py-2 rounded-md bg-indigo-600 text-white disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                         <span class="mr-2 hidden sm:inline">Next</span> &rarr;
                     </button>
                </div>
            </div>
        </main>
    </div>

    @push('scripts')
    <script>
        function chapterReader(chapters) {
            return {
                chapters: chapters,
                currentChapterIndex: 0,
                fontSize: 'text-lg',
                get currentChapter() {
                    if (!this.chapters || this.chapters.length === 0) {
                        return { title: 'No Content', content: '' };
                    }
                    return this.chapters[this.currentChapterIndex];
                },
                nextChapter() {
                    if (this.currentChapterIndex < this.chapters.length - 1) {
                        this.currentChapterIndex++;
                        window.scrollTo(0, 0);
                    }
                },
                prevChapter() {
                    if (this.currentChapterIndex > 0) {
                        this.currentChapterIndex--;
                        window.scrollTo(0, 0);
                    }
                },
                jumpToChapter(index) {
                    this.currentChapterIndex = parseInt(index);
                    window.scrollTo(0, 0);
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
