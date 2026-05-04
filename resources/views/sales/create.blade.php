<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Create Sale" subtitle="Complete point-of-sale checkout" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="customer_id" value="Customer" />
                        <select id="customer_id" name="customer_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Walk-in / Not selected</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected((string) old('customer_id') === (string) $customer->id)>
                                    {{ $customer->last_name }}, {{ $customer->first_name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label value="Items" />
                        <div id="sale-items" class="space-y-3 mt-2"></div>
                        <x-input-error :messages="$errors->get('items')" class="mt-2" />
                        <button type="button" id="add-sale-item" class="mt-3 text-sm text-indigo-600 hover:text-indigo-800">+ Add item</button>
                    </div>

                    <template id="sale-medication-options">
                        <option value="">Select medication</option>
                        @foreach ($medications as $medication)
                            <option value="{{ $medication->id }}">{{ $medication->name }} ({{ $medication->sku }})</option>
                        @endforeach
                    </template>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="payment_method" value="Payment Method" />
                            @php $paymentMethod = old('payment_method', 'cash'); @endphp
                            <select id="payment_method" name="payment_method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="cash" @selected($paymentMethod === 'cash')>Cash</option>
                                <option value="card" @selected($paymentMethod === 'card')>Card</option>
                                <option value="mobile" @selected($paymentMethod === 'mobile')>Mobile</option>
                            </select>
                            <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="discount" value="Discount" />
                            <x-text-input id="discount" name="discount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('discount', 0)" />
                            <x-input-error :messages="$errors->get('discount')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="tax" value="Tax" />
                            <x-text-input id="tax" name="tax" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('tax', 0)" />
                            <x-input-error :messages="$errors->get('tax')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Complete Sale</x-primary-button>
                        <a href="{{ route('sales.index') }}">
                            <x-secondary-button type="button">Cancel</x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('sale-items');
        const addBtn = document.getElementById('add-sale-item');
        const medicationOptions = document.getElementById('sale-medication-options').innerHTML;

        function renderItemRow(index) {
            const wrap = document.createElement('div');
            wrap.className = 'grid grid-cols-1 md:grid-cols-4 gap-3 border border-gray-200 rounded-md p-3';

            wrap.innerHTML = `
                <div>
                    <label class="block font-medium text-sm text-gray-700">Medication</label>
                    <select name="items[${index}][medication_id]" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        ${medicationOptions}
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-sm text-gray-700">Quantity</label>
                    <input name="items[${index}][quantity]" type="number" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block font-medium text-sm text-gray-700">Line Note</label>
                    <input name="items[${index}][note]" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Optional note for the sale line">
                </div>
            `;

            container.appendChild(wrap);
        }

        let count = 0;
        addBtn.addEventListener('click', () => renderItemRow(count++));
        renderItemRow(count++);
    </script>
</x-app-layout>
