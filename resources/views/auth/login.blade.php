<x-guest-layout>
    <div class="du-head">
        <h2>{{ __('Welcome back') }}</h2>
        <p>{{ __('Sign in to access your account') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Validation Errors -->
    <x-auth-validation-errors class="mb-4" :errors="$errors" />

    <form method="POST" action="{{ airoute('login') }}" class="du-form">
        @csrf

        <!-- Email Address -->
        <div class="du-field">
            <x-label for="email" :value="__('Email')" />
            <x-input id="email" type="email" name="email" :value="old('email')" autocomplete="email" required />
            <x-input-error :messages="$errors->get('email')" class="du-error" />
        </div>

        <!-- Password -->
        <div class="du-field">
            <x-label for="password" :value="__('Password')" />
            <x-input id="password"
                type="password"
                name="password"
                autocomplete="current-password"
                required />
            <x-input-error :messages="$errors->get('password')" class="du-error" />
        </div>

        <!-- Controls row: remember me + forgot password -->
        <div class="du-row">
            <label class="du-remember" for="remember_me">
                <input id="remember_me" type="checkbox" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="du-link" href="{{ airoute('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <!-- Primary action -->
        <button type="submit" class="du-btn">
            {{ __('Log in') }}
        </button>
    </form>
</x-guest-layout>
