<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ __('Login') }} &mdash; Duains</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/duains-tokens.css') }}?v={{ config('shop.version', 1) }}">
        <link rel="stylesheet" href="{{ asset('css/admin-login.css') }}?v={{ config('shop.version', 1) }}">
    </head>

    {{-- Auth screens are a fixed dark brand experience --}}
    <body class="du-auth dark">
        <main class="du-split">
            <aside class="du-hero">
                <img class="du-hero-logo"
                     src="{{ asset('images/duains-logo.png') }}?v={{ config('shop.version', 1) }}"
                     alt="Duain Fragrances"
                     width="1230" height="1278">
                <p class="du-hero-tag">{{ __('Elegance in Every Scent') }}</p>
            </aside>

            <section class="du-pane">
                <div class="du-form-wrap">
                    {{ $slot }}
                </div>

                <p class="du-foot">&copy; {{ date('Y') }} Duain Fragrances. {{ __('All rights reserved.') }}</p>
            </section>
        </main>
    </body>
</html>
