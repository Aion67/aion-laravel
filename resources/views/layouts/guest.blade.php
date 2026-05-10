<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Aion') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased bg-slate-50">
        <div class="min-h-screen flex flex-col">
            <header class="border-b border-slate-200 bg-white/90 backdrop-blur-sm">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                            <x-application-logo class="h-12 w-auto" />
                        </a>

                        <nav class="flex items-center gap-4 text-sm font-medium">
                            <a href="{{ url('/') }}" class="text-slate-600 hover:text-slate-900">Home</a>
                            <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-900">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary px-4 py-2">Register</a>
                            @endif
                        </nav>
                    </div>
                </div>
            </header>

            <div class="flex flex-1 flex-col justify-center items-center px-4 py-8 sm:py-10">
                <div>
                    <a href="{{ url('/') }}">
                        <x-application-logo class="w-24 h-24 fill-current text-gray-500" />
                    </a>
                </div>

                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
