<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" x-data="{ showPassword: false }">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pe-10"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    name="password"
                    required autocomplete="current-password" />
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 mt-1 flex items-center px-3 text-gray-500 hover:text-gray-700" aria-label="Toggle password visibility">
                    <x-icon name="eye" class="h-5 w-5" x-show="!showPassword" />
                    <x-icon name="eye-off" class="h-5 w-5" x-show="showPassword" x-cloak />
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
