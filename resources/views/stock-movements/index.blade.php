<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Stock Movements" subtitle="Inventory audit trail" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
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
                                    <x-status-badge :status="$movement->movement_type === 'in' ? 'active' : ($movement->movement_type === 'out' ? 'inactive' : 'pending')" />
                                    <span class="ml-2 text-gray-600">{{ ucfirst($movement->movement_type) }}</span>
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
