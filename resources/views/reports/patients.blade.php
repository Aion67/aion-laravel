<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Patients Report" subtitle="Patient growth, prescription activity, and repeat visitors" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-report.tabs active="patients" />

            <div class="flex justify-end">
                <x-report.export-link :href="route('reports.patients.export')" label="Export patients CSV" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-card label="Patients" :value="$patientCount" />
                <x-stat-card label="New This Month" :value="$newPatientsThisMonth" />
                <x-stat-card label="Prescriptions This Month" :value="$prescriptionsThisMonth" />
                <x-stat-card label="Repeat Patients" :value="$repeatPatientsCount" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-report.chart-card title="Patient growth" subtitle="New patients registered over the last six months" chart-id="patients-growth-chart" :chart-config="$patientGrowthChart" />
                <x-report.chart-card title="Sex distribution" subtitle="Patient mix by sex" chart-id="patient-sex-chart" :chart-config="$patientSexChart" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-report.chart-card title="Prescription activity" subtitle="Monthly prescription volume" chart-id="prescription-activity-chart" :chart-config="$prescriptionActivityChart" />

                <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800">Top Repeat Patients</h3>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Patient</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Prescriptions</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sales</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($repeatPatients as $patient)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-800">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $patient->prescriptions_count }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $patient->sales_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">No repeat patients yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>