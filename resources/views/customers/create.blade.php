<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Create Customer" subtitle="Register a new customer profile" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @include('customers._form', [
                    'action' => route('customers.store'),
                    'method' => 'POST',
                    'customer' => null,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
