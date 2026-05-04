<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="Dashboard"
            subtitle="Operational overview for pharmacy staff"
        />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach ($cards as $card)
                    <x-stat-card :label="$card['label']" :value="$card['value']" />
                @endforeach
            </div>

            @if ($canManageSales)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Low Stock Alerts</h3>
                                <p class="text-sm text-gray-500">Items that need restocking soon</p>
                            </div>
                            @if ($canViewReports)
                                <a href="{{ route('reports.stock') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Stock report</a>
                            @endif
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Medication</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">On Hand</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reorder</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($lowStockItems as $row)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-800">
                                                <div>{{ $row->medication?->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $row->medication?->sku }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $row->quantity_on_hand }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $row->medication?->reorder_level ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">No low stock alerts.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Recent Sales</h3>
                                <p class="text-sm text-gray-500">Latest completed transactions</p>
                            </div>
                            @if ($canViewReports)
                                <a href="{{ route('reports.sales') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Sales report</a>
                            @endif
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Number</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($recentSales as $sale)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-800">{{ $sale->sale_number }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ number_format((float) $sale->total, 2) }}</td>
                                            <td class="px-4 py-3 text-sm"><x-status-badge :status="$sale->status" /></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">No recent sales yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 {{ $canViewStockMovements ? 'lg:grid-cols-2' : '' }} gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Prescriptions</h3>
                        <p class="text-sm text-gray-500">New prescription activity</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Number</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($recentPrescriptions as $prescription)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $prescription->prescription_number }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $prescription->customer?->first_name }} {{ $prescription->customer?->last_name }}</td>
                                        <td class="px-4 py-3 text-sm"><x-status-badge :status="$prescription->status" /></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">No recent prescriptions yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($canViewStockMovements)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Stock Movements</h3>
                            <p class="text-sm text-gray-500">Audit trail of inventory changes</p>
                        </div>
                        <div class="overflow-x-auto">
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
                                            <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">No recent stock movements yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-800 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">Operational Dashboard</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            @if ($canViewReports)
                                Live operational aggregates and reporting views are available for sales and stock monitoring.
                            @else
                                This dashboard is tailored to your role with customer, medication, prescription, and inventory visibility.
                            @endif
                        </p>
                    </div>
                    @if ($canViewReports)
                        <div class="flex gap-3">
                            <a href="{{ route('reports.sales') }}">
                                <x-secondary-button type="button">Sales Report</x-secondary-button>
                            </a>
                            <a href="{{ route('reports.stock') }}">
                                <x-secondary-button type="button">Stock Report</x-secondary-button>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
