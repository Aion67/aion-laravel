<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Aion') }}</title>
        <meta name="description" content="Aion helps pharmacies track prescriptions, sales, inventory, and reporting in one clean workflow.">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <div class="flex min-h-screen flex-col">
            <header class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                    <x-application-logo class="h-20 w-auto sm:h-24" />
                </a>

                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-secondary px-4 py-2">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="hidden text-sm font-semibold text-slate-600 transition hover:text-slate-900 sm:inline-flex">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary px-4 py-2">Get started</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary px-4 py-2">Get started</a>
                        @endif
                    @endauth
                </nav>
            </header>

            <main class="flex flex-1 items-center justify-center px-6 py-12 text-center lg:px-8">
                <div class="mx-auto max-w-4xl">
                    <h1 class="text-5xl font-extrabold leading-[0.95] tracking-tight text-balance text-slate-950 sm:text-6xl lg:text-7xl">
                        Pharmacy management
                        <span class="text-primary-700">simplified.</span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-slate-600 sm:text-xl">
                        Manage prescriptions, sales, stock movement, and reports in one workflow built for the realities of a busy pharmacy counter.
                    </p>

                    <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary justify-center px-6 py-3 text-base">Open dashboard</a>
                        @else
                            <a href="{{ route('register') }}" class="btn-primary justify-center px-6 py-3 text-base">Start for free</a>
                            <a href="{{ route('login') }}" class="btn-secondary justify-center px-6 py-3 text-base">Log in</a>
                        @endauth
                    </div>
                </div>
            </main>

            <footer class="mx-auto max-w-7xl px-6 pb-10 text-sm text-slate-500 lg:px-8">
                <div class="flex flex-col justify-around gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center">
                    <p>Copyright {{ date('Y') }} {{ config('app.name', 'Aion') }}. Built for pharmacy operations.</p>
                </div>
            </footer>
        </div>
    </body>
</html>
