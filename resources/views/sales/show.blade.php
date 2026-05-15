<x-app-layout>
    <x-slot name="header">
        <x-page-header :title="'Receipt '.$sale->sale_number" subtitle="Printable sale receipt" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Customer</p>
                        <p class="font-medium text-gray-800">{{ $sale->customer?->first_name }} {{ $sale->customer?->last_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Processed By</p>
                        <p class="font-medium text-gray-800">{{ $sale->user?->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p><x-status-badge :status="$sale->status" /></p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Medication</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($sale->items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $item->medication?->name }} ({{ $item->medication?->sku }})</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700"><x-money :amount="$item->unit_price" /></td>
                                <td class="px-4 py-3 text-sm text-gray-700"><x-money :amount="$item->line_total" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <p class="text-gray-600">Subtotal: <span class="font-medium text-gray-800"><x-money :amount="$sale->subtotal" /></span></p>
                    <p class="text-gray-600">Discount: <span class="font-medium text-gray-800"><x-money :amount="$sale->discount" /></span></p>
                    <p class="text-gray-600">Tax: <span class="font-medium text-gray-800"><x-money :amount="$sale->tax" /></span></p>
                    <p class="text-gray-600">Total: <span class="font-medium text-gray-800"><x-money :amount="$sale->total" /></span></p>
                    <p class="text-gray-600">Payment Method: <span class="font-medium text-gray-800">{{ ucfirst($sale->payment_method) }}</span></p>
                    <p class="text-gray-600">Sold At: <span class="font-medium text-gray-800">{{ $sale->sold_at?->format('Y-m-d H:i') }}</span></p>
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('sales.index') }}">
                    <x-secondary-button type="button">Back to Sales</x-secondary-button>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
