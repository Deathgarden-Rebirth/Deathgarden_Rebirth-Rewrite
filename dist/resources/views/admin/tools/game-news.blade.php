@props([
    'newsList'
])

<x-layouts.admin>
    <div class="w-full p-2 md:px-16">
        @foreach($newsList as $index => $news)
            <x-admin.tools.game-news-list-entry :$news :idPrefix="$index"/>
        @endforeach
        <form id="create-news" action="{{ route('gamenews.create') }}" method="post">
            @csrf
        </form>
        <x-inputs.button
                form="create-news"
                type="submit"
                class="create container mx-auto mb-8 block shadow-glow shadow-green-400/10 hover:shadow-green-400/20 sticky bottom-10"
        >
            Add New
        </x-inputs.button>
    </div>
</x-layouts.admin>