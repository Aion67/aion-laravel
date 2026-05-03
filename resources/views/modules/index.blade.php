<x-app-layout>
    <x-slot name="header">
        <x-page-header :title="$title" :subtitle="$description" />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-700">
                    <p class="text-sm text-gray-600">
                        This module is initialized in Phase 1. CRUD screens, validation flows,
                        and workflow actions will be delivered in subsequent phases.
                    </p>
                    <div class="mt-4">
                        <x-status-badge status="draft" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
