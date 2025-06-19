<x-admin-layout>
    {{-- This content will go into the `$header` slot in the layout --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    {{-- This content will go into the main `$slot` in the layout --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in as an Admin!") }}
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900">Admin Management</h3>
                    <div class="mt-4">
                        <a href="{{ route('admin.books.index') }}" class="text-blue-500 hover:underline">Manage Books</a>
                    </div>
                    <div class="mt-2">
                        <a href="{{ route('admin.users.index') }}" class="text-blue-500 hover:underline">Manage Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
