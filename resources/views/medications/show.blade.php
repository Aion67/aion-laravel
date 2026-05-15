<x-app-layout>
    <x-slot name="header">
        <x-page-header :title="$medication->name" :subtitle="$medication->sku.' · Medication details'" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="grid gap-0 md:grid-cols-2">
                    <div class="bg-gray-50">
                        <x-medication-image :medication="$medication" variant="detail" />
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-semibold text-gray-900">{{ $medication->name }}</h2>
                                <p class="mt-1 text-sm text-gray-500">SKU {{ $medication->sku }}</p>
                            </div>
                            <x-status-badge :status="$medication->status" />
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Unit Price</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900"><x-money :amount="$medication->unit_price" /></p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Reorder Level</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $medication->reorder_level ?? '-' }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Unit Type</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $medication->unit_type }}</p>
                            </div>
                            <div class="rounded-xl bg-gray-50 p-4">
                                <p class="text-xs uppercase tracking-wide text-gray-500">Dosage Form</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $medication->dosage_form ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Inventory Snapshot</p>
                            <div class="mt-3 grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">On Hand</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $medication->inventory?->quantity_on_hand ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Reserved</p>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">{{ $medication->inventory?->reserved_quantity ?? 0 }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Status</p>
                                    <p class="mt-1"><x-status-badge :status="$medication->inventory && $medication->inventory->quantity_on_hand <= ($medication->reorder_level ?? -1) ? 'pending' : 'active'" /></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('medications.edit', $medication) }}">
                                <x-primary-button type="button">Edit Medication</x-primary-button>
                            </a>
                            <a href="{{ route('medications.index') }}">
                                <x-secondary-button type="button">Back</x-secondary-button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>