@pushonce('head')
    @vite(['resources/js/admin/user-selector.js'])
@endpushonce


<select {{ $attributes->merge(['class' => "input-global-dropdown user-selector"]) }}
        data-fetch-url="{{ route('users.dropdown') }}"
        multiple="multiple"
></select>
