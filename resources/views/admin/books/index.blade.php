<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.books.index') }}" method="GET" class="mb-6">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center">
                            <input type="text" name="search"
                                class="w-full px-4 py-2 border rounded-t-md sm:rounded-l-md sm:rounded-t-none focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                placeholder="Search for books..." value="{{ request('search') }}">
                            <button type="submit"
                                class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white rounded-b-md sm:rounded-r-md sm:rounded-b-none hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-2 sm:mt-0">
                                Search
                            </button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 hidden md:table-header-group">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Author
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Downloads
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($books['results'] as $book)
                                    <tr class="hover:bg-gray-50 transition ease-in-out duration-150 flex flex-col md:table-row">
                                        <td class="px-6 py-4 whitespace-nowrap flex-1 md:table-cell">
                                            <div class="md:hidden font-semibold text-gray-500">Title:</div>
                                            <div class="text-sm font-medium text-gray-900">{{ $book['title'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap flex-1 md:table-cell">
                                            <div class="md:hidden font-semibold text-gray-500">Author:</div>
                                            <div class="text-sm text-gray-900">{{ $book['authors'][0]['name'] ?? 'Unknown' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 flex-1 md:table-cell">
                                            <div class="md:hidden font-semibold text-gray-500">Downloads:</div>
                                            {{ $book['download_count'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                            No books found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
