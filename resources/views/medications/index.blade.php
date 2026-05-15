<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Medications" subtitle="Medication catalog and status management" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('medications.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <input type="hidden" name="view" value="{{ $viewMode }}">
                    <div class="md:col-span-3">
                        <x-input-label for="search" value="Search" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="$search" placeholder="Name or SKU" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All statuses</option>
                            <option value="active" @selected($status === 'active')>Active</option>
                            <option value="inactive" @selected($status === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('medications.index') }}">
                            <x-secondary-button type="button">Reset</x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>

            <div class="flex justify-end">
                <div class="flex items-center gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}">
                        <x-secondary-button type="button" :class="$viewMode === 'table' ? 'bg-gray-200' : ''">Table</x-secondary-button>
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'cards']) }}">
                        <x-secondary-button type="button" :class="$viewMode === 'cards' ? 'bg-gray-200' : ''">Cards</x-secondary-button>
                    </a>
                    <a href="{{ route('medications.create') }}">
                        <x-primary-button type="button">New Medication</x-primary-button>
                    </a>
                </div>
            </div>

            @if ($viewMode === 'cards')
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
                    @forelse ($medications as $medication)
                        <article class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
                            <x-medication-image :medication="$medication" variant="card" />
                            <div class="space-y-2 p-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">{{ $medication->name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $medication->sku }}</p>
                                    </div>
                                    <x-status-badge :status="$medication->status" />
                                </div>

                                <p class="text-xs text-gray-600">{{ $medication->strength ?: 'No strength' }} • {{ $medication->unit_type }}</p>

                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div class="rounded-lg bg-gray-50 p-2">
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Unit Price</p>
                                        <p class="mt-1 text-sm font-semibold text-gray-900"><x-money :amount="$medication->unit_price" /></p>
                                    </div>
                                    <div class="rounded-lg bg-gray-50 p-2">
                                        <p class="text-xs uppercase tracking-wide text-gray-500">Stock</p>
                                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $medication->inventory?->quantity_on_hand ?? 0 }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <div class="flex gap-2 text-xs">
                                        <a href="{{ route('medications.show', $medication) }}" class="text-gray-700 hover:text-gray-900">View</a>
                                        <a href="{{ route('medications.edit', $medication) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form method="POST" action="{{ route('medications.destroy', $medication) }}" onsubmit="return confirm('Delete this medication?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </div>
                                    <span class="text-[11px] text-gray-500">SKU: {{ $medication->sku }}</span>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-xl bg-white p-6 text-center text-sm text-gray-500 shadow-sm ring-1 ring-gray-200 sm:col-span-2 xl:col-span-3">No medications found.</div>
                    @endforelse
                </div>
            @else
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">SKU</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($medications as $medication)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $medication->sku }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    <div>{{ $medication->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $medication->strength ?: 'No strength' }} • {{ $medication->unit_type }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700"><x-money :amount="$medication->unit_price" /></td>
                                <td class="px-4 py-3 text-sm">
                                    <x-status-badge :status="$medication->status" />
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('medications.show', $medication) }}" class="text-gray-700 hover:text-gray-900">View</a>
                                        <a href="{{ route('medications.edit', $medication) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form method="POST" action="{{ route('medications.destroy', $medication) }}" onsubmit="return confirm('Delete this medication?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No medications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif

            <div>
                {{ $medications->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
