<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Inventory" subtitle="Stock snapshot and low-stock monitoring" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('inventory.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div class="md:col-span-3">
                        <x-input-label for="search" value="Search" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="$search" placeholder="Medication name or SKU" />
                    </div>
                    <div class="flex items-center gap-2 mt-6">
                        <input id="low_stock" name="low_stock" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked($onlyLowStock)>
                        <label for="low_stock" class="text-sm text-gray-700">Low stock only</label>
                    </div>
                    <div class="flex items-end gap-2">
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('inventory.index') }}">
                            <x-secondary-button type="button">Reset</x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('inventory.adjust.create') }}">
                    <x-primary-button type="button">Adjust Stock</x-primary-button>
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Medication</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">On Hand</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reserved</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reorder Level</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($inventoryRows as $row)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    <div>{{ $row['medication']->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $row['medication']->sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $row['quantity_on_hand'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $row['reserved_quantity'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $row['medication']->reorder_level ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <x-status-badge :status="$row['is_low_stock'] ? 'pending' : 'active'" />
                                    <span class="ml-2 text-gray-600">{{ $row['is_low_stock'] ? 'Low stock' : 'Healthy' }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex justify-end">
                                        <a href="{{ route('inventory.adjust.create', ['medication_id' => $row['medication']->id]) }}" class="text-indigo-600 hover:text-indigo-800">Adjust</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No inventory records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
