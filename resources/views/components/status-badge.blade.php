@props(['status'])

@php
    $normalized = strtolower((string) $status);
    $classes = match ($normalized) {
        'active', 'paid', 'confirmed', 'dispensed' => 'bg-green-100 text-green-700',
        'pending', 'draft' => 'bg-yellow-100 text-yellow-800',
        'cancelled', 'void', 'inactive' => 'bg-red-100 text-red-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $classes }}">
    {{ ucfirst($normalized) }}
</span>
