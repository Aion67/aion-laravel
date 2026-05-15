@props(['value'])

@php
    $raw = trim((string) $value);
    $digits = preg_replace('/\D+/', '', $raw) ?? '';

    if ($raw === '') {
        $display = '-';
    } elseif (str_starts_with($raw, '+256')) {
        $display = '+256 '.trim(chunk_split(substr($raw, 4), 3, ' '));
    } elseif (str_starts_with($digits, '256')) {
        $display = '+'.substr($digits, 0, 3).' '.trim(chunk_split(substr($digits, 3), 3, ' '));
    } elseif (str_starts_with($digits, '0')) {
        $display = '+256 '.trim(chunk_split(substr($digits, 1), 3, ' '));
    } else {
        $display = $raw;
    }
@endphp

<span>{{ $display }}</span>