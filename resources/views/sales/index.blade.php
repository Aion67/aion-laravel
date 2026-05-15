<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Sales" subtitle="Completed transactions and receipts" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('sales.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-2">
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All statuses</option>
                            <option value="paid" @selected($status === 'paid')>Paid</option>
                            <option value="pending" @selected($status === 'pending')>Pending</option>
                            <option value="void" @selected($status === 'void')>Void</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('sales.index') }}">
                            <x-secondary-button type="button">Reset</x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('sales.create') }}">
                    <x-primary-button type="button">New Sale</x-primary-button>
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sale Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sold At</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($sales as $sale)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $sale->sale_number }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $sale->customer?->first_name }} {{ $sale->customer?->last_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700"><x-money :amount="$sale->total" /></td>
                                <td class="px-4 py-3 text-sm"><x-status-badge :status="$sale->status" /></td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $sale->sold_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex justify-end">
                                        <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-800">Receipt</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No sales found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
