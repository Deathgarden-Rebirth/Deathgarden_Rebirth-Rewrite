@props([
    'newsList'
])

<x-layouts.admin>
    <div class="h-full w-full p-1 md:p-16">
        @foreach($newsList as $news)
            <x-admin.tools.game-news-list-entry :$news />
        @endforeach
    </div>
</x-layouts.admin>