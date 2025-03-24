<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Insert anda punya email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4 relative">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Kata sandi" />
                
                <!-- Tombol Toggle Password -->
                <button type="button" onclick="togglePassword()" 
                        class="absolute inset-y-0 right-0 flex items-center pr-5 mr-2">
                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" 
                        class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" 
                        fill="currentColor">
                        <path fill-rule="evenodd" 
                            d="M10 3C5 3 1 8 1 10s4 7 9 7 9-5 9-7-4-7-9-7zm0 2a5 5 0 100 10A5 5 0 0010 5zm0 2a3 3 0 110 6 3 3 0 010-6z" 
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-200">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
        
                <a class="underline text-sm text-gray-600 dark:text-gray-200 hover:text-gray-900 dark:hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                    {{ __('Daftar akun?') }}
                </a>

                <span class="mx-4">|</span>

                <a class="underline text-sm text-gray-600 dark:text-gray-200 hover:text-gray-900 dark:hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Lupa Password?') }}
                </a>

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<script>
    function togglePassword() {
        let passwordInput = document.getElementById('password');
        let eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `<path fill-rule="evenodd" 
                                 d="M10 3C5 3 1 8 1 10s4 7 9 7 9-5 9-7-4-7-9-7zm0 2a5 5 0 100 10A5 5 0 0010 5z" 
                                 clip-rule="evenodd" />`;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `<path fill-rule="evenodd" 
                                 d="M10 3C5 3 1 8 1 10s4 7 9 7 9-5 9-7-4-7-9-7zm0 2a5 5 0 100 10A5 5 0 0010 5zm0 2a3 3 0 110 6 3 3 0 010-6z" 
                                 clip-rule="evenodd" />`;
        }
    }
</script>
