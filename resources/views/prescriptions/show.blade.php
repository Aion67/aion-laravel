<x-app-layout>
    <x-slot name="header">
        <x-page-header :title="'Prescription '.$prescription->prescription_number" subtitle="Review details and update status" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Customer</p>
                        <p class="font-medium text-gray-800">{{ $prescription->customer?->first_name }} {{ $prescription->customer?->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p><x-status-badge :status="$prescription->status" /></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Created By</p>
                        <p class="font-medium text-gray-800">{{ $prescription->user?->name }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('prescriptions.status.update', $prescription) }}" class="mt-4 flex items-end gap-3">
                    @csrf
                    @method('PATCH')
                    <div>
                        <x-input-label for="status" value="Update Status" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            @foreach (['draft', 'confirmed', 'dispensed', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected($prescription->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                    <x-primary-button>Update</x-primary-button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Medication</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Dosage Instructions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($prescription->items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $item->medication?->name }} ({{ $item->medication?->sku }})</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700"><x-money :amount="$item->unit_price" /></td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $item->dosage_instructions ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
