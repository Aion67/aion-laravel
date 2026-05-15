<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Stock Movements" subtitle="Inventory audit trail and manual adjustments" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-lg p-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="font-semibold">This page is read-only.</p>
                    <p class="text-sm text-amber-800">Sales and inventory adjustments create stock movements automatically. Use Inventory Adjustment to record a new manual movement.</p>
                </div>
                @can('adjust-inventory')
                    <a href="{{ route('inventory.adjust.create') }}">
                        <x-primary-button type="button">New Adjustment</x-primary-button>
                    </a>
                @endcan
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('stock-movements.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-2">
                        <x-input-label for="movement_type" value="Movement Type" />
                        <select id="movement_type" name="movement_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All types</option>
                            <option value="in" @selected($movementType === 'in')>In</option>
                            <option value="out" @selected($movementType === 'out')>Out</option>
                            <option value="adjustment" @selected($movementType === 'adjustment')>Adjustment</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('stock-movements.index') }}">
                            <x-secondary-button type="button">Reset</x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Medication</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Recorded By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($movements as $movement)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $movement->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $movement->medication?->name }} ({{ $movement->medication?->sku }})</td>
                                <td class="px-4 py-3 text-sm">
                                    @php
                                        $movementLabel = match ($movement->movement_type) {
                                            'in' => 'Stock In',
                                            'out' => 'Stock Out',
                                            'adjustment' => 'Adjustment',
                                            default => ucfirst($movement->movement_type),
                                        };

                                        $movementClasses = match ($movement->movement_type) {
                                            'in' => 'bg-green-100 text-green-700',
                                            'out' => 'bg-red-100 text-red-700',
                                            'adjustment' => 'bg-yellow-100 text-yellow-800',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $movementClasses }}">
                                        {{ $movementLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->user?->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $movement->notes ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No stock movements found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
