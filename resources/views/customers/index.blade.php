<x-app-layout>
    <x-slot name="header">
        <x-page-header title="Customers" subtitle="Patient and customer records" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('customers.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-3">
                        <x-input-label for="search" value="Search" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" :value="$search" placeholder="Name, phone, or email" />
                    </div>
                    <div class="flex items-end gap-2">
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('customers.index') }}">
                            <x-secondary-button type="button">Reset</x-secondary-button>
                        </a>
                    </div>
                </form>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('customers.create') }}">
                    <x-primary-button type="button">New Customer</x-primary-button>
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">DOB</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sex</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Contact</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $customer->first_name }} {{ $customer->last_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $customer->date_of_birth?->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($customer->sex) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if ($customer->phone)
                                        <x-phone :value="$customer->phone" />
                                    @else
                                        {{ $customer->email ?: '-' }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('customers.edit', $customer) }}" class="text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Delete this customer?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No customers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
