{{--
    Logo image asset: public/aion_pharmacy_logo.svg
    Size is controlled by Tailwind classes on <x-application-logo> in each parent view, e.g. class="h-16 w-auto".
    Main locations:
    - resources/views/layouts/navigation.blade.php (app shell)
    - resources/views/layouts/guest.blade.php (auth: header + mark above form)
    - resources/views/welcome.blade.php (marketing header)
--}}
<img src="{{ asset('aion_pharmacy_logo.svg') }}" alt="Aion Pharmacy" {{ $attributes }} />
