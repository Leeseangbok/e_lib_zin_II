<x-guest-layout>
    <div class="flex h-screen bg-gray-900">
        <div class="hidden lg:flex w-1/2 bg-purple-900 text-white p-12 flex-col justify-between relative">
            <div class="absolute top-10 left-10">
                 <h1 class="text-4xl font-bold mb-4">Welcome to E-Lib</h1>
                 <p class="text-lg">Your digital library awaits.</p>
            </div>
            <div class="my-auto">
                 <img src="/background.png" alt="Bookshelf and reading glasses" class="max-w-md mx-auto">
            </div>
            <div class="text-sm">
                 <p><strong>Email Support:</strong> support@elib.com</p>
                 <p><strong>Phone Support:</strong> +1 (234) 567-890</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 bg-gray-900 p-8 sm:p-12 flex items-center justify-center">
            <div class="w-full flex flex-col justify-center">
                <h2 class="text-4xl font-bold text-white mb-2">Create Your Account</h2>
                <p class="text-gray-200 mb-8">Let's get you started with your free account.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Name')" class="font-semibold"/>
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your username" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="email" :value="__('Email')" class="font-semibold"/>
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Enter email"/>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" class="font-semibold"/>
                        <x-text-input id="password" class="block mt-1 w-full"
                                      type="password"
                                      name="password"
                                      required autocomplete="new-password"
                                      placeholder="Enter password"/>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="font-semibold"/>
                        <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                      type="password"
                                      name="password_confirmation" required autocomplete="new-password"
                                      placeholder="Enter confirm password"/>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a class="underline text-lg text-gray-200 hover:text-gray-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                            {{ __('Already registered?') }}
                        </a>
                        <x-primary-button class="ms-4 px-8 py-5 text-2xl bg-purple-700 hover:bg-purple-800">
                            Register
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
