<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Edit User') }}: <span class="font-normal">{{ $user->name }}</span>
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12 bg-gray-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="p-4 sm:p-8 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" class="text-gray-800" />
                            <x-text-input id="name" class="block mt-2 w-full" type="text" name="name"
                                :value="old('name', $user->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-gray-800" />
                            <x-text-input id="email" class="block mt-2 w-full" type="email" name="email"
                                :value="old('email', $user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div>
                            <x-input-label for="role" :value="__('Role')" class="text-gray-800" />
                            <select name="role" id="role"
                                class="block mt-2 w-full py-2 sm:py-6 px-2 sm:px-4 text-gray-900 bg-gray-200 border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="user" @selected(old('role', $user->role) == 'user')>User</option>
                                <option value="admin" @selected(old('role', $user->role) == 'admin')>Admin</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <x-input-label for="password" :value="__('New Password (optional)')" class="text-gray-800" />
                            <x-text-input id="password" class="block mt-2 w-full" type="password" name="password"
                                autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave blank to keep the current
                                password.</p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="text-gray-800" />
                            <x-text-input id="password_confirmation" class="block mt-2 w-full" type="password"
                                name="password_confirmation" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700 gap-4">
                            <a href="{{ route('admin.users.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-400 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition w-full sm:w-auto justify-center">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button class="w-full sm:w-auto justify-center">
                                {{ __('Update User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
