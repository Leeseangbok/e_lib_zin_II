@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Admin Dashboard') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                    <h4 class="text-white font-bold text-3xl">{{ $bookCount }}</h4>
                    <p class="text-gray-400">Total Books</p>
                </div>
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                    <h4 class="text-white font-bold text-3xl">{{ $userCount }}</h4>
                    <p class="text-gray-400">Total Users</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-white">Latest Books</h3>
                        <a href="{{ route('admin.books.index') }}" class="text-indigo-400 hover:underline">View All</a>
                    </div>
                    <ul class="divide-y divide-gray-700">
                        @forelse($latestBooks as $book)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="text-white font-semibold">{{ $book->title }}</p>
                                    <p class="text-sm text-gray-400">{{ $book->author }}</p>
                                </div>
                                <a href="{{ route('admin.books.edit', $book) }}" class="text-indigo-400 text-sm">Edit</a>
                            </li>
                        @empty
                            <p class="text-gray-400">No books found.</p>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold text-white">Latest Users</h3>
                        <a href="{{ route('admin.users.index') }}" class="text-indigo-400 hover:underline">View All</a>
                    </div>
                    <ul class="divide-y divide-gray-700">
                        @forelse($latestUsers as $user)
                            <li class="py-3">
                                <p class="text-white font-semibold">{{ $user->name }}</p>
                                <p class="text-sm text-gray-400">{{ $user->email }}</p>
                            </li>
                        @empty
                            <p class="text-gray-400">No users found.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
