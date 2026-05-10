<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" x-data="{ showPassword: false, showPasswordConfirm: false }">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role (temporary: allow creating admin) -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role" class="block mt-1 w-full rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="{{ App\\Models\\User::ROLE_PHARMACIST }}" {{ old('role', App\\Models\\User::ROLE_PHARMACIST) === App\\Models\\User::ROLE_PHARMACIST ? 'selected' : '' }}>Pharmacist</option>
                <option value="{{ App\\Models\\User::ROLE_ADMIN }}" {{ old('role') === App\\Models\\User::ROLE_ADMIN ? 'selected' : '' }}>Admin</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pe-10"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    name="password"
                    required autocomplete="new-password" />
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 mt-1 flex items-center px-3 text-gray-500 hover:text-gray-700" aria-label="Toggle password visibility">
                    <x-icon name="eye" class="h-5 w-5" x-show="!showPassword" />
                    <x-icon name="eye-off" class="h-5 w-5" x-show="showPassword" x-cloak />
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="relative">
                <x-text-input id="password_confirmation" class="block mt-1 w-full pe-10"
                    x-bind:type="showPasswordConfirm ? 'text' : 'password'"
                    name="password_confirmation" required autocomplete="new-password" />
                <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" class="absolute inset-y-0 right-0 mt-1 flex items-center px-3 text-gray-500 hover:text-gray-700" aria-label="Toggle password confirmation visibility">
                    <x-icon name="eye" class="h-5 w-5" x-show="!showPasswordConfirm" />
                    <x-icon name="eye-off" class="h-5 w-5" x-show="showPasswordConfirm" x-cloak />
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
