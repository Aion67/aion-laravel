<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Sales Report" subtitle="Monthly trends, top medications, and recent sales" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card label="Monthly Sales" :value="number_format((float) $monthlySalesTotal, 2)" />
                <x-stat-card label="Monthly Transactions" :value="$monthlySalesCount" />
                <x-stat-card label="Today's Sales" :value="number_format((float) $dailySalesTotal, 2)" />
                <x-stat-card label="Top Seller" :value="$topMedications->first()->name ?? 'None yet'" />
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <x-report.export-link :href="route('reports.sales.export')" label="Export sales CSV" />
                <a href="{{ route('reports.stock') }}">
                    <x-secondary-button type="button">Stock Report</x-secondary-button>
                </a>
                <a href="{{ route('dashboard') }}">
                    <x-secondary-button type="button">Back to Dashboard</x-secondary-button>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-report.chart-card
                    title="Sales trend"
                    subtitle="Total revenue across the last six months"
                    chart-id="sales-trend-chart"
                    :chart-config="$salesTrendChart"
                />

                <x-report.chart-card
                    title="Payment mix"
                    subtitle="Revenue split by payment method"
                    chart-id="payment-method-chart"
                    :chart-config="$paymentMethodChart"
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-report.chart-card
                    title="Sales by cashier"
                    subtitle="Revenue contribution by staff member"
                    chart-id="sales-by-user-chart"
                    :chart-config="$salesByUserChart"
                />

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Top Medications This Month</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Medication</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Qty Sold</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($topMedications as $row)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $row->name }} ({{ $row->sku }})</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $row->quantity_sold }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ number_format((float) $row->revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">No sales data available yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Sales</h3>
                        <a href="{{ route('sales.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Open Sales</a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Number</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($sales as $sale)
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
    </div>
</x-app-layout>
