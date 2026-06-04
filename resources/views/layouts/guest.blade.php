<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Theme CSS -->
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    </head>
    <body class="bg-surface text-ink">
        <div class="auth-page">
            <div class="container container--sm">
                <div class="auth-card">
                    <div class="auth-header">
                        <a href="/" class="auth-logo">{{ config('app.name', 'Laravel') }}</a>
                    </div>

                    {{ $slot }}
                </div>
            </div>

            @include('layouts.footer')
        </div>
    </body>
</html>
