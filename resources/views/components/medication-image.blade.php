@props([
    'medication',
    'variant' => 'card',
    'alt' => null,
])

@php
    $variantClasses = match ($variant) {
        'thumbnail' => 'h-16 w-16 rounded-lg',
        'preview' => 'h-32 w-full rounded-lg',
        'detail' => 'aspect-[16/9] w-full rounded-2xl md:aspect-auto md:h-full',
        default => 'h-28 w-full rounded-xl',
    };

    $imageClasses = match ($variant) {
        'thumbnail' => 'h-full w-full object-cover',
        'preview' => 'h-full w-full object-cover',
        'detail' => 'h-full w-full object-cover md:h-full',
        default => 'h-full w-full object-cover',
    };
@endphp

<div {{ $attributes->class(['overflow-hidden bg-gray-100', $variantClasses]) }}>
    <img
        src="{{ $medication->image_url }}"
        alt="{{ $alt ?? $medication->name }}"
        class="{{ $imageClasses }}"
    >
</div>