@props([
    'action',
    'method' => 'POST',
    'medication' => null,
])

<form method="POST" action="{{ $action }}" class="space-y-6" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="sku" value="SKU" />
            <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full" :value="old('sku', $medication?->sku)" required />
            <x-input-error :messages="$errors->get('sku')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="name" value="Name" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $medication?->name)" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-input-label for="unit_type" value="Unit Type" />
            <x-text-input id="unit_type" name="unit_type" type="text" class="mt-1 block w-full" :value="old('unit_type', $medication?->unit_type)" placeholder="tablet, ml, bottle" required />
            <x-input-error :messages="$errors->get('unit_type')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="dosage_form" value="Dosage Form" />
            <x-text-input id="dosage_form" name="dosage_form" type="text" class="mt-1 block w-full" :value="old('dosage_form', $medication?->dosage_form)" />
            <x-input-error :messages="$errors->get('dosage_form')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="strength" value="Strength" />
            <x-text-input id="strength" name="strength" type="text" class="mt-1 block w-full" :value="old('strength', $medication?->strength)" />
            <x-input-error :messages="$errors->get('strength')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-input-label for="unit_price" value="Unit Price" />
            <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('unit_price', $medication?->unit_price)" required />
            <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="reorder_level" value="Reorder Level" />
            <x-text-input id="reorder_level" name="reorder_level" type="number" min="0" class="mt-1 block w-full" :value="old('reorder_level', $medication?->reorder_level)" />
            <x-input-error :messages="$errors->get('reorder_level')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="status" value="Status" />
            @php $selectedStatus = old('status', $medication?->status ?? 'active'); @endphp
            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="active" @selected($selectedStatus === 'active')>Active</option>
                <option value="inactive" @selected($selectedStatus === 'inactive')>Inactive</option>
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
        <div class="md:col-span-2">
            <x-input-label for="image" value="Medication Image" />
            <input id="image" name="image" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-indigo-700 hover:file:bg-indigo-100" />
            <p class="mt-2 text-xs text-gray-500">PNG, JPG, WebP up to 2MB. Leave empty to use the existing image or fallback placeholder.</p>
            <x-input-error :messages="$errors->get('image')" class="mt-2" />
        </div>

        <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Preview</p>
            <x-medication-image
                :medication="$medication ?? new \App\Models\Medication()"
                variant="preview"
                alt="Medication preview"
                class="mt-3 border border-gray-200 bg-white"
            />
        </div>
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $medication ? 'Update Medication' : 'Create Medication' }}</x-primary-button>
        <a href="{{ route('medications.index') }}">
            <x-secondary-button type="button">Cancel</x-secondary-button>
        </a>
    </div>
</form>
