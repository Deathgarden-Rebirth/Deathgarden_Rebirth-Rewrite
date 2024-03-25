@props([
    'current',
    'needed',
    'showEdit' => false,
])

<div {{ $attributes->merge(['class' => 'flex gap-2']) }}>
    @for($i = 0;$i < $needed;++$i)
        <div class="h-2 w-full {{ $i < $current ? 'bg-sky-400' : 'bg-gray-500 ' }}">

        </div>
    @endfor
</div>