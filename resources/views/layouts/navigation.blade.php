<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-6">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-12 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                        {{ __('Customers') }}
                    </x-nav-link>
                    <x-nav-link :href="route('medications.index')" :active="request()->routeIs('medications.*')">
                        {{ __('Medications') }}
                    </x-nav-link>
                    <x-nav-link :href="route('inventory.index')" :active="request()->routeIs('inventory.*')">
                        {{ __('Inventory') }}
                    </x-nav-link>
                    <x-nav-link :href="route('prescriptions.index')" :active="request()->routeIs('prescriptions.*')">
                        {{ __('Prescriptions') }}
                    </x-nav-link>
                    @if (Auth::user()->isAdmin())
                        <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">
                            {{ __('Sales') }}
                        </x-nav-link>
                        <x-nav-link :href="route('reports.sales')" :active="request()->routeIs('reports.*')">
                            {{ __('Reports') }}
                        </x-nav-link>
                        <x-nav-link :href="route('stock-movements.index')" :active="request()->routeIs('stock-movements.*')">
                            {{ __('Stock') }}
                        </x-nav-link>
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            {{ __('Users') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <x-icon name="chevron-down" class="h-4 w-4" />
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = true" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out" aria-label="Open menu">
                    <x-icon name="menu" class="h-6 w-6" />
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/45 sm:hidden" @click="open = false" x-cloak></div>

    <aside
        x-show="open"
        x-transition:enter="transform transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 z-50 h-full w-80 max-w-[86vw] bg-white shadow-2xl sm:hidden"
        x-cloak
    >
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-4">
            <div>
                <p class="text-sm font-semibold text-gray-800">Navigation</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->name }}</p>
            </div>
            <button @click="open = false" class="rounded-md p-2 text-gray-500 hover:bg-gray-100" aria-label="Close menu">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>

        <div class="h-[calc(100%-64px)] overflow-y-auto px-4 py-4 space-y-6">
            <div>
                <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Main</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('dashboard') }}" @click="open = false" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary-100 text-primary-800' : 'bg-gray-50 text-gray-700' }}">Dashboard</a>
                    <a href="{{ route('customers.index') }}" @click="open = false" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('customers.*') ? 'bg-primary-100 text-primary-800' : 'bg-gray-50 text-gray-700' }}">Customers</a>
                    <a href="{{ route('medications.index') }}" @click="open = false" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('medications.*') ? 'bg-primary-100 text-primary-800' : 'bg-gray-50 text-gray-700' }}">Medications</a>
                    <a href="{{ route('inventory.index') }}" @click="open = false" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('inventory.*') ? 'bg-primary-100 text-primary-800' : 'bg-gray-50 text-gray-700' }}">Inventory</a>
                    <a href="{{ route('prescriptions.index') }}" @click="open = false" class="rounded-lg px-3 py-2 text-sm font-medium col-span-2 {{ request()->routeIs('prescriptions.*') ? 'bg-primary-100 text-primary-800' : 'bg-gray-50 text-gray-700' }}">Prescriptions</a>
                </div>
            </div>

            @if (Auth::user()->isAdmin())
                <div>
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Admin</p>
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('sales.index') }}" @click="open = false" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('sales.*') ? 'bg-accent-100 text-accent-800' : 'bg-gray-50 text-gray-700' }}">Sales</a>
                        <a href="{{ route('reports.sales') }}" @click="open = false" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('reports.*') ? 'bg-accent-100 text-accent-800' : 'bg-gray-50 text-gray-700' }}">Reports</a>
                        <a href="{{ route('stock-movements.index') }}" @click="open = false" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('stock-movements.*') ? 'bg-accent-100 text-accent-800' : 'bg-gray-50 text-gray-700' }}">Stock</a>
                        <a href="{{ route('users.index') }}" @click="open = false" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('users.*') ? 'bg-accent-100 text-accent-800' : 'bg-gray-50 text-gray-700' }}">Users</a>
                    </div>
                </div>
            @endif

            <div>
                <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Account</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('profile.edit') }}" @click="open = false" class="rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.edit') ? 'bg-primary-100 text-primary-800' : 'bg-gray-50 text-gray-700' }}">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-sm font-medium bg-gray-50 text-gray-700 text-left">Log Out</button>
                    </form>
                </div>
                <p class="mt-4 px-1 text-xs text-gray-500">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </aside>
</nav>