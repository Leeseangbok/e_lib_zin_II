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
                <h2 class="text-4xl font-bold text-white mb-2">Sign In to E-Lib</h2>
                <p class="text-gray-200 mb-8">Access your digital library account.</p>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('Email')" class="font-semibold"/>
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter email"/>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" class="font-semibold"/>
                        <x-text-input id="password" class="block mt-1 w-full"
                                      type="password"
                                      name="password"
                                      required autocomplete="current-password"
                                      placeholder="Enter password"/>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="block mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                            <span class="ms-2 text-sm text-gray-200">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        >
                        @if (Route::has('password.request'))
                            <a class="underline text-lg text-gray-200 hover:text-gray-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                        <a class="underline text-lg text-gray-200 hover:text-gray-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                            {{ __('Create an account') }}
                        </a>
                        <x-primary-button class="ms-4 px-8 py-5 text-2xl bg-purple-700 hover:bg-purple-800">
                            {{ __('Log in') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
