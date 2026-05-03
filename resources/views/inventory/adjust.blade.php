<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Adjust Inventory" subtitle="Record stock movement with reason" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('inventory.adjust.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="medication_id" value="Medication" />
                        @php $selectedMedication = old('medication_id', $selectedMedicationId); @endphp
                        <select id="medication_id" name="medication_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">Select medication</option>
                            @foreach ($medications as $medication)
                                <option value="{{ $medication->id }}" @selected((string) $selectedMedication === (string) $medication->id)>
                                    {{ $medication->name }} ({{ $medication->sku }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('medication_id')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="movement_type" value="Movement Type" />
                            @php $movementType = old('movement_type', 'in'); @endphp
                            <select id="movement_type" name="movement_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="in" @selected($movementType === 'in')>Stock In</option>
                                <option value="out" @selected($movementType === 'out')>Stock Out</option>
                                <option value="adjustment" @selected($movementType === 'adjustment')>Adjustment</option>
                            </select>
                            <x-input-error :messages="$errors->get('movement_type')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="adjustment_direction" value="Adjustment Direction" />
                            @php $direction = old('adjustment_direction', 'increase'); @endphp
                            <select id="adjustment_direction" name="adjustment_direction" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="increase" @selected($direction === 'increase')>Increase</option>
                                <option value="decrease" @selected($direction === 'decrease')>Decrease</option>
                            </select>
                            <x-input-error :messages="$errors->get('adjustment_direction')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="quantity" value="Quantity" />
                        <x-text-input id="quantity" name="quantity" type="number" min="1" class="mt-1 block w-full" :value="old('quantity')" required />
                        <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="notes" value="Notes" />
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Save Adjustment</x-primary-button>
                        <a href="{{ route('inventory.index') }}">
                            <x-secondary-button type="button">Cancel</x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
