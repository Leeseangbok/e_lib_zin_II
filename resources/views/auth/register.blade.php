<x-guest-layout>
    <div class="flex w-full items-center justify-center p-2 sm:p-4">

        <div class="w-full max-w-sm sm:max-w-md rounded-lg p-4 sm:p-6shadow-lg">
            <div class="w-full flex flex-col justify-center">

                <h2 class="text-2xl sm:text-3xl text-center font-bold text-white mb-1 sm:mb-2">Create Your Account</h2>
                <p class="text-gray-300 mb-4 sm:mb-6 text-center text-sm sm:text-base">Let's get you started with your free account.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div>
                        <x-input-label for="name" :value="__('Name')" class="font-semibold text-gray-200 text-xs sm:text-sm" />
                        <x-text-input id="name" class="block mt-1 w-full text-sm sm:text-base py-2 px-3" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4 sm:mt-2">
                        <x-input-label for="email" :value="__('Email')" class="font-semibold text-gray-200 text-xs sm:text-sm" />
                        <x-text-input id="email" class="block mt-1 w-full text-sm sm:text-base py-2 px-3"
                            type="email" name="email" :value="old('email')" required autocomplete="username"
                            placeholder="Your Email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-4 sm:mt-2">
                        <x-input-label for="password" :value="__('Password')" class="font-semibold text-gray-200 text-xs sm:text-sm" />
                        <x-text-input id="password" class="block mt-1 w-full text-sm sm:text-base py-2 px-3"
                            type="password" name="password" required autocomplete="new-password"
                            placeholder="Your Password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-4 sm:mt-2">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="font-semibold text-gray-200 text-xs sm:text-sm" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full text-sm sm:text-base py-2 px-3"
                            type="password" name="password_confirmation" required autocomplete="new-password"
                            placeholder="Confirm Password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex flex-col w-full sm:flex-col sm:items-center mt-8 gap-y-8">

                        <div class="flex flex-col justify-between w-full sm:flex-row sm:items-center gap-4 items-stretch">
                            <a class="underline text-sm text-center sm:text-left text-gray-300 hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 order-last sm:order-first"
                                href="{{ route('login') }}">
                                {{ __('Already registered?') }}
                            </a>
                            <x-primary-button
                                class="ms-0 sm:ms-4 py-2 sm:py-3 px-4 sm:px-6 text-sm sm:text-base bg-gray-400 hover:bg-gray-700 justify-center">
                                {{ __('Register') }}
                            </x-primary-button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-guest-layout>
