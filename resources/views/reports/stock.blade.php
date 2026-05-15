<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Stock Report" subtitle="Inventory levels, low-stock alerts, and movement trends" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-report.tabs active="stock" />

            <div class="flex justify-end">
                <x-report.export-link :href="route('reports.stock.export')" label="Export stock CSV" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card label="Tracked Medications" :value="$inventoryRows->count()" />
                <x-stat-card label="Low Stock Items" :value="$lowStockItems->count()" />
                <x-stat-card label="Stock In This Month" :value="number_format((float) ($movementSummary->firstWhere('movement_type', 'in')?->quantity_total ?? 0), 0)" />
                <x-stat-card label="Stock Out This Month" :value="number_format((float) ($movementSummary->firstWhere('movement_type', 'out')?->quantity_total ?? 0), 0)" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-report.chart-card title="Movement trend" subtitle="Stock in and out across the last six months" chart-id="movement-trend-chart" :chart-config="$movementTrendChart" />
                <x-report.chart-card title="Inventory health" subtitle="Healthy versus low-stock items" chart-id="stock-health-chart" :chart-config="$stockHealthChart" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-report.chart-card title="Lowest stock items" subtitle="Fastest way to spot restock priorities" chart-id="inventory-levels-chart" :chart-config="$inventoryLevelsChart" />

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Low Stock Alerts</h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Medication</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">On Hand</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reorder Level</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($lowStockItems as $row)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $row['inventory']->medication?->name }} ({{ $row['inventory']->medication?->sku }})</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $row['inventory']->quantity_on_hand }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $row['inventory']->medication?->reorder_level ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">No low stock alerts.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Stock Movements</h3>
                        <a href="{{ route('stock-movements.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Open Audit Trail</a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Medication</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentMovements as $movement)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->created_at?->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $movement->medication?->name }} ({{ $movement->medication?->sku }})</td>
                                    <td class="px-4 py-3 text-sm"><x-status-badge :status="$movement->movement_type === 'in' ? 'active' : ($movement->movement_type === 'out' ? 'inactive' : 'pending')" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">No stock movement data available yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800">Movement Summary This Month</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($movementSummary as $row)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ ucfirst($row->movement_type) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $row->quantity_total }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-6 text-center text-sm text-gray-500">No movement summary available yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>