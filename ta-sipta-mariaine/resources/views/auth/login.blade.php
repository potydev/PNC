<x-guest-layout>
    <!-- Logo -->
    <div class="flex justify-center mb-6 mt-6">
        <img src="{{ url('img/jkb.png') }}" alt="PNC Logo" class="w-20 h-20">
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <x-input-error :messages="$errors->get('sso')" class="mb-4" />
    <h2 class="text-xl font-black text-center text-gray-700">SIPTA JKB PNC</h2>
    <p class="text-xs text-center text-gray-500 mb-6">Silakan login terlebih dahulu!</p>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="('Email')" />
            <x-text-input id="email" class="block mt-1 w-full h-10 p-3 border rounded-md focus:ring focus:ring-blue-300"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="('Password')" />
            <x-text-input id="password" class="block mt-1 h-10 w-full p-3 border rounded-md focus:ring focus:ring-blue-300"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="flex items-center">
                <input id="remember_me" type="checkbox" class="text-blue-600 border-gray-300 rounded focus:ring-blue-500" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-blue-600 hover:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="mt-6 mb-6">
            <button type="submit" class="w-full h-10 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                {{ __('Log in') }}
            </button>
        </div>

        <div class="mb-6">
            <a href="{{ route('sso.redirect') }}" class="block w-full h-10 leading-10 text-center bg-gray-700 text-white rounded-md hover:bg-gray-800 transition">
                {{ __('Login dengan SSO') }}
            </a>
        </div>
    </form>
</x-guest-layout>
