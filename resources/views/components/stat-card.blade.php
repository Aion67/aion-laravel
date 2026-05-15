@props([
    'label',
    'value',
    'icon' => 'bar-chart',
    'money' => false,
])

<div class="bg-white shadow-soft p-5 border border-gray-100 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">{{ $label }}</p>
            <p class="mt-2 text-2xl font-semibold text-gray-800">
                @if ($money)
                    <x-money :amount="$value" />
                @else
                    {{ $value }}
                @endif
            </p>
        </div>
        <div class="ms-4 flex-shrink-0">
            <div class="w-12 h-12 rounded-lg bg-primary-50 flex items-center justify-center">
                <x-icon :name="$icon" class="w-6 h-6 text-primary-600" />
            </div>
        </div>
    </div>
</div>
