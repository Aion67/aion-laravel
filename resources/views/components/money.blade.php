@props([
    'amount',
    'precision' => 0,
    'currency' => 'UGX',
    'exchangeRate' => config('currency.usd_to_ugx_rate', 3800),
])

@php
    $normalizedAmount = (float) $amount * (float) $exchangeRate;
    $formatted = number_format($normalizedAmount, $precision);
@endphp

<span>{{ $currency }} {{ $formatted }}</span>