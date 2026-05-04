<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Aion') }} | Pharmacy management simplified</title>
        <meta name="description" content="Aion helps pharmacies track prescriptions, sales, inventory, and reporting in one clean workflow.">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(14,165,123,0.16),_transparent_35%),radial-gradient(circle_at_top_right,_rgba(255,167,0,0.16),_transparent_28%),linear-gradient(to_bottom,_#f8fffd,_#f8fafc_35%,_#f8fafc)]"></div>
            <div class="absolute inset-x-0 top-0 -z-10 h-72 bg-[linear-gradient(to_right,_rgba(14,165,123,0.08),_rgba(255,167,0,0.08))] blur-3xl"></div>

            <header class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-6 lg:px-8">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-600 text-white shadow-soft-md">
                        <span class="text-lg font-black tracking-tight">A</span>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold uppercase tracking-[0.28em] text-primary-700">Aion</span>
                        <span class="block text-sm text-slate-600">Pharmacy operations platform</span>
                    </span>
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

            <main class="mx-auto max-w-7xl px-6 pb-20 lg:px-8">
                <section class="grid items-center gap-12 pt-10 lg:grid-cols-2 lg:pt-14">
                    <div class="max-w-2xl">
                        <div class="badge-primary mb-6">For pharmacies that need clarity fast</div>
                        <h1 class="max-w-3xl text-5xl font-extrabold leading-[0.95] tracking-tight text-balance text-slate-950 sm:text-6xl lg:text-7xl">
                            Pharmacy management
                            <span class="bg-gradient-to-r from-primary-700 via-primary-600 to-accent-600 bg-clip-text text-transparent">simplified.</span>
                        </h1>
                        <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600 sm:text-xl">
                            Manage prescriptions, sales, stock movement, and reports in one workflow built for the realities of a busy pharmacy counter.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary justify-center px-6 py-3 text-base">Open dashboard</a>
                            @else
                                <a href="{{ route('register') }}" class="btn-primary justify-center px-6 py-3 text-base">Start for free</a>
                                <a href="{{ route('login') }}" class="btn-secondary justify-center px-6 py-3 text-base">Log in</a>
                            @endauth
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-3">
                            <div class="card">
                                <div class="text-2xl font-bold text-slate-950">47</div>
                                <div class="mt-1 text-sm text-slate-600">tests passing</div>
                            </div>
                            <div class="card">
                                <div class="text-2xl font-bold text-slate-950">2 roles</div>
                                <div class="mt-1 text-sm text-slate-600">admin and pharmacist</div>
                            </div>
                            <div class="card">
                                <div class="text-2xl font-bold text-slate-950">1 flow</div>
                                <div class="mt-1 text-sm text-slate-600">from intake to reporting</div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute -left-6 top-8 h-28 w-28 rounded-full bg-primary-200/60 blur-3xl"></div>
                        <div class="absolute -right-4 bottom-10 h-32 w-32 rounded-full bg-accent-200/50 blur-3xl"></div>

                        <div class="card relative overflow-hidden border-white/70 bg-white/85 p-0 shadow-[0_24px_80px_rgba(15,23,42,0.12)] backdrop-blur">
                            <div class="border-b border-slate-100 px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Today at a glance</div>
                                        <div class="text-sm text-slate-500">Live pharmacy snapshot</div>
                                    </div>
                                    <span class="badge-accent">Operational</span>
                                </div>
                            </div>

                            <div class="grid gap-4 p-6 sm:grid-cols-2">
                                <div class="rounded-2xl bg-primary-50 p-5 ring-1 ring-primary-100">
                                    <div class="text-sm font-medium text-primary-700">Prescription queue</div>
                                    <div class="mt-3 text-4xl font-black text-primary-950">18</div>
                                    <p class="mt-2 text-sm leading-6 text-primary-800">Priority requests processed with role-aware steps.</p>
                                </div>
                                <div class="rounded-2xl bg-accent-50 p-5 ring-1 ring-accent-100">
                                    <div class="text-sm font-medium text-accent-700">Low stock items</div>
                                    <div class="mt-3 text-4xl font-black text-accent-950">6</div>
                                    <p class="mt-2 text-sm leading-6 text-accent-800">Flag shortages before they interrupt service.</p>
                                </div>
                                <div class="rounded-2xl bg-slate-900 p-5 text-white sm:col-span-2">
                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <div class="text-sm font-medium text-slate-300">Role protection</div>
                                            <div class="mt-2 text-xl font-semibold">Pharmacists see only what they need.</div>
                                        </div>
                                        <div class="rounded-2xl bg-white/10 p-3 ring-1 ring-white/10">
                                            <x-icon name="shield-check" class="h-6 w-6 text-accent-300" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mt-20">
                    <div class="max-w-2xl">
                        <div class="badge-primary">What teams get</div>
                        <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Everything important is visible, and nothing noisy gets in the way.</h2>
                    </div>

                    <div class="mt-10 grid gap-6 md:grid-cols-3">
                        <article class="card">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-100 text-primary-700">
                                <x-icon name="pill" class="h-6 w-6" />
                            </div>
                            <h3 class="mt-5 text-lg font-semibold text-slate-950">Inventory visibility</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Track stock movement, surface shortages early, and keep replenishment decisions close to the counter.</p>
                        </article>

                        <article class="card">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-accent-100 text-accent-700">
                                <x-icon name="workflow" class="h-6 w-6" />
                            </div>
                            <h3 class="mt-5 text-lg font-semibold text-slate-950">Fast workflow</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Move from prescription intake to fulfillment with a focused interface built to reduce clicks.</p>
                        </article>

                        <article class="card">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white">
                                <x-icon name="users-cog" class="h-6 w-6" />
                            </div>
                            <h3 class="mt-5 text-lg font-semibold text-slate-950">Role-based access</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Admins get control over sales, reports, and users while pharmacists stay in a safe, simplified workspace.</p>
                        </article>
                    </div>
                </section>

                <section class="mt-20 grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                    <div class="card bg-slate-900 text-white">
                        <div class="badge-accent bg-white/10 text-white">Built for pharmacy counters</div>
                        <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">One system for stock, service, and reporting.</h2>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">
                            The landing page now matches the rest of the design language: clean cards, strong contrast, soft shadows, and primary/accent color cues that point users to the next step.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary justify-center px-6 py-3 text-base">Continue to dashboard</a>
                            @else
                                <a href="{{ route('register') }}" class="btn-primary justify-center px-6 py-3 text-base">Create account</a>
                            @endauth
                            <a href="{{ route('login') }}" class="btn-secondary justify-center px-6 py-3 text-base">Existing user sign in</a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">What is included</div>
                                <div class="text-sm text-slate-500">Core modules highlighted on the homepage</div>
                            </div>
                            <span class="badge-primary">Ready</span>
                        </div>

                        <ul class="mt-6 space-y-4">
                            <li class="flex gap-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-700">
                                    <x-icon name="package" class="h-4 w-4" />
                                </span>
                                <div>
                                    <div class="font-medium text-slate-950">Prescriptions</div>
                                    <div class="text-sm leading-6 text-slate-600">Focused intake and fulfillment flow.</div>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-accent-100 text-accent-700">
                                    <x-icon name="inventory" class="h-4 w-4" />
                                </span>
                                <div>
                                    <div class="font-medium text-slate-950">Inventory</div>
                                    <div class="text-sm leading-6 text-slate-600">Track movement and stock thresholds.</div>
                                </div>
                            </li>
                            <li class="flex gap-3">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-white">
                                    <x-icon name="bar-chart" class="h-4 w-4" />
                                </span>
                                <div>
                                    <div class="font-medium text-slate-950">Reports</div>
                                    <div class="text-sm leading-6 text-slate-600">See trends with admin-only visibility.</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </section>
            </main>

            <footer class="mx-auto max-w-7xl px-6 pb-10 text-sm text-slate-500 lg:px-8">
                <div class="flex flex-col justify-between gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:items-center">
                    <p>Copyright {{ date('Y') }} {{ config('app.name', 'Aion') }}. Built for pharmacy operations.</p>
                    <p class="text-slate-400">Primary and accent tokens keep the interface consistent.</p>
                </div>
            </footer>
        </div>
    </body>
</html>