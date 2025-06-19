<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex-1 min-w-0">
                <h2 class="font-semibold text-lg sm:text-sm text-white truncate" title="{{ $book['title'] }}">
                    Reading: {{ Str::limit($book['title'], 50) }}
                </h2>
            </div>
            <div class="flex items-center space-x-2 sm:space-x-4 mt-2 sm:mt-0">
                <a href="{{ route('books.show', $book['id']) }}" class="text-xs sm:text-sm text-gray-300 hover:text-white">&larr; Back to Details</a>
            </div>
        </div>
    </x-slot>

    <div x-data="chapterReader({{ Js::from($chapters) }})" x-cloak class="flex flex-col lg:flex-row relative">

        <!-- Mobile Menu Button -->
        <div class="lg:hidden flex justify-end px-4 py-3">
            <button @click="isChapterMenuOpen = true"
                    class="text-sm font-medium text-white bg-indigo-600 px-4 py-2 rounded-md">
                ☰ Chapters
            </button>
        </div>

        <!-- Desktop Sidebar -->
        <aside class="hidden lg:block w-full lg:w-72 bg-white dark:bg-gray-800 lg:h-[calc(100vh-69px)] lg:sticky top-[69px] border-b lg:border-b-0 lg:border-r border-gray-200 dark:border-gray-700 px-4 py-4 sm:px-6 sm:py-6 overflow-y-auto">
            <h3 class="text-sm sm:text-base font-semibold text-gray-800 dark:text-gray-200 mb-4">Chapters</h3>
            <ul class="space-y-1">
                <template x-for="(chapter, index) in chapters" :key="index">
                    <li>
                        <a href="#"
                           @click.prevent="jumpToChapter(index)"
                           :class="{
                               'bg-indigo-600 text-white': currentChapterIndex === index,
                               'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700': currentChapterIndex !== index
                           }"
                           class="block px-3 py-2 rounded-md text-xs sm:text-sm font-medium transition truncate"
                           x-text="chapter.title">
                        </a>
                    </li>
                </template>
            </ul>
        </aside>

        <!-- Mobile Chapter Drawer -->
        <div x-show="isChapterMenuOpen" x-transition class="fixed inset-0 z-40 bg-black/50 flex lg:hidden">
            <div class="bg-white dark:bg-gray-800 w-64 p-4 overflow-y-auto shadow-lg h-full z-50">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-200">Chapters</h3>
                    <button @click="isChapterMenuOpen = false" class="text-gray-600 dark:text-gray-300 hover:text-red-500">
                        ✕
                    </button>
                </div>
                <ul class="space-y-1">
                    <template x-for="(chapter, index) in chapters" :key="index">
                        <li>
                            <a href="#"
                               @click.prevent="jumpToChapter(index); isChapterMenuOpen = false"
                               :class="{
                                   'bg-indigo-600 text-white': currentChapterIndex === index,
                                   'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700': currentChapterIndex !== index
                               }"
                               class="block px-3 py-2 rounded-md text-sm font-medium transition truncate"
                               x-text="chapter.title">
                            </a>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <!-- Main Reader Content -->
        <main class="w-full py-4 sm:py-8">
            <div class="max-w-full sm:max-w-4xl mx-auto px-2 sm:px-6 lg:px-8">
                <!-- Font Size Controls -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-2 sm:p-4 mb-4 sm:mb-6 flex items-center justify-end">
                    <div class="flex items-center space-x-1 sm:space-x-2">
                        <span class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300">Font:</span>
                        <button @click="fontSize = 'text-base'" :class="{ 'bg-indigo-600 text-white': fontSize === 'text-base', 'bg-gray-200 dark:bg-gray-700': fontSize !== 'text-base' }" class="px-2 py-1 text-xs sm:text-sm rounded-md transition">S</button>
                        <button @click="fontSize = 'text-lg'" :class="{ 'bg-indigo-600 text-white': fontSize === 'text-lg', 'bg-gray-200 dark:bg-gray-700': fontSize !== 'text-lg' }" class="px-2 py-1 text-xs sm:text-sm rounded-md transition">M</button>
                        <button @click="fontSize = 'text-xl'" :class="{ 'bg-indigo-600 text-white': fontSize === 'text-xl', 'bg-gray-200 dark:bg-gray-700': fontSize !== 'text-xl' }" class="px-2 py-1 text-xs sm:text-sm rounded-md transition">L</button>
                    </div>
                </div>

                <!-- Chapter Content -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4 sm:p-8">
                    <template x-if="chapters.length > 0 && currentChapter">
                        <div>
                            <h3 class="text-xl sm:text-3xl font-bold text-center mb-4 sm:mb-8 text-gray-800 dark:text-gray-200" x-text="currentChapter.title"></h3>
                            <div :class="fontSize" class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 leading-relaxed font-serif">
                                <div x-html="currentChapter.content"></div>
                            </div>
                        </div>
                    </template>
                    <template x-if="chapters.length === 0 || !currentChapter">
                        <div class="text-center">
                            <h3 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-gray-200">Could Not Load Book</h3>
                            <p class="mt-1 sm:mt-2 text-gray-600 dark:text-gray-400">We were unable to parse the chapters for this book.</p>
                        </div>
                    </template>
                </div>

                <!-- Chapter Navigation -->
                <div class="mt-4 sm:mt-6 flex justify-between gap-2">
                    <button @click="prevChapter()" :disabled="currentChapterIndex === 0" class="px-3 sm:px-6 py-2 rounded-md bg-indigo-600 text-white disabled:opacity-50 disabled:cursor-not-allowed flex items-center text-xs sm:text-base">
                        &larr; <span class="ml-1 sm:ml-2 hidden sm:inline">Previous</span>
                    </button>
                    <button @click="nextChapter()" :disabled="currentChapterIndex === chapters.length - 1" class="px-3 sm:px-6 py-2 rounded-md bg-indigo-600 text-white disabled:opacity-50 disabled:cursor-not-allowed flex items-center text-xs sm:text-base">
                        <span class="mr-1 sm:mr-2 hidden sm:inline">Next</span> &rarr;
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
                isChapterMenuOpen: false,
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
