<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg">
                <div class="p-8 text-gray-900">
                    {{-- Session Messages --}}
                    @if(session('success'))
                        <div class="mb-6 p-4 font-medium text-sm text-green-700 bg-green-100 rounded" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-6 p-4 font-medium text-sm text-red-700 bg-red-100 rounded" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Users Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-gray-500 border-separate border-spacing-y-2 hidden md:table">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 rounded-l-lg">Name</th>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3">Role</th>
                                    <th class="px-6 py-3">Joined Date</th>
                                    <th class="px-6 py-3 text-right rounded-r-lg">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr class="bg-white hover:bg-gray-50 shadow rounded">
                                        <td class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap">{{ $user->name }}</td>
                                        <td class="px-6 py-4">{{ $user->email }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-3 py-1 inline-flex text-xs font-bold rounded-full {{ $user->role == 'admin' ? 'bg-green-200 text-green-900' : 'bg-indigo-200 text-indigo-900' }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ $user->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-block px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded hover:bg-blue-200 transition">Edit</a>
                                                @if(auth()->id() !== $user->id && $user->id !== 1)
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-block px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded hover:bg-red-200 transition">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 bg-white rounded">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Mobile Card View --}}
                        <div class="md:hidden space-y-4">
                            @forelse ($users as $user)
                                <div class="bg-white shadow rounded p-4 flex flex-col space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                                        <span class="px-3 py-1 text-xs font-bold rounded-full {{ $user->role == 'admin' ? 'bg-green-200 text-green-900' : 'bg-indigo-200 text-indigo-900' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </div>
                                    <div class="text-gray-500 text-sm">{{ $user->email }}</div>
                                    <div class="text-gray-400 text-xs">Joined: {{ $user->created_at->format('M d, Y') }}</div>
                                    <div class="flex items-center justify-end space-x-2 pt-2">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-block px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded hover:bg-blue-200 transition">Edit</a>
                                        @if(auth()->id() !== $user->id && $user->id !== 1)
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-block px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded hover:bg-red-200 transition">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="bg-white rounded p-6 text-center text-gray-500">
                                    No users found.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Pagination Links --}}
                    <div class="mt-8 flex justify-center">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
