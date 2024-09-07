@php
    $type = $attributes->has('type') ? $attributes->get('type') : 'date';
@endphp

<input type="{{ $type }}" {{ $attributes->merge(['class' => 'input-global-date']) }}>