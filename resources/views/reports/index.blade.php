<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Reports Overview" subtitle="High-level pharmacy performance, inventory health, and patient activity" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-report.tabs active="overview" />

            <div class="flex justify-end">
                <x-report.export-link :href="route('reports.overview.export')" label="Export overview CSV" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card label="Monthly Revenue" :value="$monthlySalesTotal" money />
                <x-stat-card label="Active Patients" :value="$patientCount" />
                <x-stat-card label="Low Stock Medications" :value="$lowStockCount" />
                <x-stat-card label="Prescriptions This Month" :value="$monthlyPrescriptionCount" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-report.chart-card title="Sales trend" subtitle="Revenue across the last six months" chart-id="overview-sales-trend-chart" :chart-config="$salesTrendChart" />
                <x-report.chart-card title="Inventory health" subtitle="Healthy versus low-stock medications" chart-id="overview-stock-health-chart" :chart-config="$stockHealthChart" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-report.chart-card title="Patient growth" subtitle="New patients registered over the last six months" chart-id="overview-patient-growth-chart" :chart-config="$patientGrowthChart" />
                <x-report.chart-card title="Medication performance" subtitle="Revenue by medication sold this month" chart-id="overview-medication-performance-chart" :chart-config="$medicationPerformanceChart" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Sales</h3>
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
                            @forelse ($recentSales as $sale)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $sale->sale_number }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><x-money :amount="$sale->total" /></td>
                                    <td class="px-4 py-3 text-sm"><x-status-badge :status="$sale->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">No sales yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Patients</h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Patient</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Phone</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentPatients as $patient)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><x-phone :value="$patient->phone" /></td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $patient->created_at?->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">No patients yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>