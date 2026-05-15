<x-app-layout>
    <x-slot name="header">
        <x-page-header :title="$medication->name" :subtitle="$medication->sku.' · Inventory details'" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="grid gap-0 md:grid-cols-2">
                    <div class="bg-gray-50">
                        <x-medication-image :medication="$medication" variant="detail" />
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-semibold text-gray-900">{{ $medication->name }}</h2>
                                <p class="mt-1 text-sm text-gray-500">{{ $medication->sku }} · {{ $medication->strength ?: 'No strength' }} · {{ $medication->unit_type }}</p>
                            </div>
                            <x-status-badge :status="$isLowStock ? 'pending' : 'active'" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">On Hand</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $quantityOnHand }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Reserved</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $reservedQuantity }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Reorder Level</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $medication->reorder_level ?? '-' }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Unit Price</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900"><x-money :amount="$medication->unit_price" /></p>
                            </div>
                        </div>

                        @if ($isLowStock)
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                                <p class="font-semibold">Low stock alert</p>
                                <p class="mt-1 text-sm">This item is at or below its reorder threshold.</p>
                            </div>
                        @endif

                        <div class="flex items-center gap-3">
                            @can('adjust-inventory')
                                <a href="{{ route('inventory.adjust.create', ['medication_id' => $medication->id]) }}">
                                    <x-primary-button type="button">Adjust Stock</x-primary-button>
                                </a>
                            @endcan
                            <a href="{{ route('inventory.index') }}">
                                <x-secondary-button type="button">Back</x-secondary-button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Stock Movements</h3>
                        <p class="text-sm text-gray-500">Latest audit trail entries for this medication</p>
                    </div>
                </div>

                <div class="mt-4 overflow-hidden rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">By</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($medication->stockMovements as $movement)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $movement->created_at?->format('Y-m-d H:i') }}</td>
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
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $movementClasses }}">{{ $movementLabel }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->user?->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $movement->notes ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No stock movements recorded for this item.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>