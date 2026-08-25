<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Duains &mdash; {{ __('Sign in') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/duains-tokens.css') }}?v={{ config('shop.version', 1) }}">
        <link rel="stylesheet" href="{{ asset('css/admin-login.css') }}?v={{ config('shop.version', 1) }}">
    </head>

@php $theme = ($_COOKIE['aimeos_backend_theme'] ?? '') == 'light' ? 'light' : 'dark'; @endphp
    <body class="du-auth {{ $theme }}">
        <main class="du-auth-wrap">
            <a class="du-auth-brand" href="{{ url('/') }}">Duains<span>&nbsp;Admin</span></a>

            <div class="du-card">
                {{ $slot }}
            </div>

            <p class="du-foot">&copy; {{ date('Y') }} Duains</p>
        </main>
    </body>
</html>
