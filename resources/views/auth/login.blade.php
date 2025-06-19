<x-guest-layout>
    <div class="flex w-full items-center justify-center p-2 sm:p-4">

        <div class="w-full max-w-sm sm:max-w-md rounded-lg p-4 sm:p-6">
            <div class="w-full flex flex-col justify-center">

                <h2 class="text-2xl sm:text-3xl text-center font-bold text-white mb-1 sm:mb-2">Sign In to E-Lib</h2>
                <p class="text-gray-300 mb-4 sm:mb-6 text-center text-sm sm:text-base">Access your digital library
                    account.</p>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email')" class="font-semibold text-gray-200 text-xs sm:text-sm" />
                        <x-text-input id="email" class="block mt-1 w-full text-sm sm:text-base py-2 px-3"
                            type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                            placeholder="Your Email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" class="font-semibold text-gray-200 text-xs sm:text-sm" />
                        <x-text-input id="password" class="block mt-1 w-full text-sm sm:text-base py-2 px-3"
                            type="password" name="password" required autocomplete="current-password"
                            placeholder="Your Password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="block mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                name="remember">
                            <span class="ms-2 text-sm text-gray-300">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="flex flex-col w-full sm:flex-col sm:items-center mt-8 gap-y-8">

                        <div class="flex flex-col justify-between w-full sm:flex-row sm:items-center gap-4 items-stretch">
                            <a class="underline text-sm text-center sm:text-left text-gray-300 hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 order-last sm:order-first"
                                href="{{ route('register') }}">
                                {{ __('Create an account') }}
                            </a>
                            <x-primary-button
                                class="ms-0 sm:ms-4 py-2 sm:py-3 px-4 sm:px-6 text-sm sm:text-base bg-gray-400 hover:bg-gray-700 justify-center">
                                {{ __('Log in') }}
                            </x-primary-button>
                        </div>
                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-300 hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-guest-layout>
