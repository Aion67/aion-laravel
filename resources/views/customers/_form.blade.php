@props([
    'action',
    'method' => 'POST',
    'customer' => null,
])

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="first_name" value="First Name" />
            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $customer?->first_name)" required />
            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="last_name" value="Last Name" />
            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $customer?->last_name)" required />
            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-input-label for="date_of_birth" value="Date of Birth" />
            <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', optional($customer?->date_of_birth)->format('Y-m-d'))" required />
            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="sex" value="Sex" />
            @php $sex = old('sex', $customer?->sex ?? 'male'); @endphp
            <select id="sex" name="sex" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="male" @selected($sex === 'male')>Male</option>
                <option value="female" @selected($sex === 'female')>Female</option>
                <option value="other" @selected($sex === 'other')>Other</option>
            </select>
            <x-input-error :messages="$errors->get('sex')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="phone" value="Phone" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $customer?->phone)" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="email" value="Email" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $customer?->email)" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="address" value="Address" />
        <textarea id="address" name="address" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $customer?->address) }}</textarea>
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="medical_history" value="Medical History" />
        <textarea id="medical_history" name="medical_history" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('medical_history', $customer?->medical_history) }}</textarea>
        <x-input-error :messages="$errors->get('medical_history')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-input-label for="allergies" value="Allergies" />
            <textarea id="allergies" name="allergies" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('allergies', $customer?->allergies) }}</textarea>
            <x-input-error :messages="$errors->get('allergies')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="conditions" value="Conditions" />
            <textarea id="conditions" name="conditions" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('conditions', $customer?->conditions) }}</textarea>
            <x-input-error :messages="$errors->get('conditions')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $customer ? 'Update Customer' : 'Create Customer' }}</x-primary-button>
        <a href="{{ route('customers.index') }}">
            <x-secondary-button type="button">Cancel</x-secondary-button>
        </a>
    </div>
</form>
