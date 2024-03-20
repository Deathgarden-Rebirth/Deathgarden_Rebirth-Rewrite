@props([
    'newsList'
])

<x-layouts.admin>
    <div class="w-full p-2 md:px-16">
        @foreach($newsList as $index => $news)
            <x-admin.tools.game-news-list-entry :$news :idPrefix="$index"/>
        @endforeach
        <form action="{{ route('gamenews.create') }}" method="post">
            @csrf
            <x-inputs.button
                    type="submit"
                    class="create container mx-auto mb-8 block shadow-glow shadow-green-400/10 hover:shadow-green-400/20 sticky bottom-8"
            >
                Add New
            </x-inputs.button>
        </form>
    </div>
</x-layouts.admin>