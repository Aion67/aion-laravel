<x-app-layout>
    <x-slot name="header">
        <x-page-header
            title="Dashboard"
            subtitle="Operational overview for pharmacy staff"
        />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($cards as $card)
                    <x-stat-card :label="$card['label']" :value="$card['value']" />
                @endforeach
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-800">
                    <h3 class="text-lg font-semibold">Phase 1 Foundation Ready</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Core modules, role protection, and base UI components are in place.
                        Phase 2 will replace placeholders with live database-backed lists and forms.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
