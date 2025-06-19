{{-- resources/views/admin/books/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Book') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Include the form partial --}}
                     @include('admin.books.partials.form', ['action' => route('admin.books.update', $book), 'method' => 'PUT'])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
