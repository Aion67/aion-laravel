@props([
    'action',
    'method' => 'POST',
    'user' => null,
])

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user?->name)" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user?->email)" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="role" :value="__('Role')" />
        <select id="role" name="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @php
                $role = old('role', $user?->role ?? \App\Models\User::ROLE_PHARMACIST);
            @endphp
            <option value="{{ \App\Models\User::ROLE_ADMIN }}" @selected($role === \App\Models\User::ROLE_ADMIN)>Admin</option>
            <option value="{{ \App\Models\User::ROLE_PHARMACIST }}" @selected($role === \App\Models\User::ROLE_PHARMACIST)>Pharmacist</option>
        </select>
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" :required="$user === null" />
        @if ($user)
            <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password.</p>
        @endif
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" :required="$user === null" />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
    </div>

    <div class="flex items-center gap-3">
        <x-primary-button>{{ $user ? 'Update User' : 'Create User' }}</x-primary-button>
        <a href="{{ route('users.index') }}">
            <x-secondary-button type="button">Cancel</x-secondary-button>
        </a>
    </div>
</form>
