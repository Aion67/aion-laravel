<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Edit Customer" subtitle="Update customer profile details" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @include('customers._form', [
                    'action' => route('customers.update', $customer),
                    'method' => 'PUT',
                    'customer' => $customer,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
