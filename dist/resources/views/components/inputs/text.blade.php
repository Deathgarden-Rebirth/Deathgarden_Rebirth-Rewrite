@props([
    'showWarning' => false
])

<div class="input-global-text">
    <input {{ $attributes }} data-show-error="{{ $showWarning }}" >
</div>
