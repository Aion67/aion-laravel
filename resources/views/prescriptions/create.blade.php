<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Create Prescription" subtitle="Capture medications and dosage instructions" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('prescriptions.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="customer_id" value="Customer" />
                        <select id="customer_id" name="customer_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">Select customer</option>
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
                        <div id="prescription-items" class="space-y-3 mt-2"></div>
                        <x-input-error :messages="$errors->get('items')" class="mt-2" />
                        <button type="button" id="add-prescription-item" class="mt-3 text-sm text-indigo-600 hover:text-indigo-800">+ Add item</button>
                    </div>

                    <template id="prescription-medication-options">
                        <option value="">Select medication</option>
                        @foreach ($medications as $medication)
                            <option value="{{ $medication->id }}">{{ $medication->name }} ({{ $medication->sku }})</option>
                        @endforeach
                    </template>

                    <div>
                        <x-input-label for="notes" value="Notes" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Create Prescription</x-primary-button>
                        <a href="{{ route('prescriptions.index') }}">
                            <x-secondary-button type="button">Cancel</x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const container = document.getElementById('prescription-items');
        const addBtn = document.getElementById('add-prescription-item');
        const medicationOptions = document.getElementById('prescription-medication-options').innerHTML;

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
                    <label class="block font-medium text-sm text-gray-700">Dosage Instructions</label>
                    <input name="items[${index}][dosage_instructions]" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g. Take twice daily after meals">
                </div>
            `;

            container.appendChild(wrap);
        }

        let count = 0;
        addBtn.addEventListener('click', () => renderItemRow(count++));
        renderItemRow(count++);
    </script>
</x-app-layout>
