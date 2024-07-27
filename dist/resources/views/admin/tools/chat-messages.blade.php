@props(['messages'])

@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Admin\BadChatMessage[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator $messages */

    $handledMessages = $unhandledMessages = [];

    foreach ($messages as $message) {
        if($message->handled)
            $handledMessages[] = $message;
        else
            $unhandledMessages[] = $message;
    }

@endphp

<x-layouts.admin>
    <div class="w-full p-2 md:px-16 bg-inherit">
        @if($messages->count() <= 0)
            <h1 class="text-2xl font-bold text-center">No Messages</h1>
        @endif

        @if(count($unhandledMessages) > 0)
            <h1 class="text-2xl font-bold my-6">Unhandled</h1>

            <table class="border-spacing-3">
                <thead>
                <th>Time</th>
                <th>Lobby Host</th>
                <th>User</th>
                <th>Message</th>
                <th>Handled</th>
                <th>Actions</th>
                </thead>
                <tbody>
                @foreach($unhandledMessages as $message)
                    <x-admin.tools.chat-message-entry :$message/>
                @endforeach
                </tbody>
            </table>
        @endif
        @if(count($handledMessages) > 0)
            <h1 class="text-2xl font-bold my-6">Handled</h1>

            <table class="border-spacing-3">
                <thead>
                <th>Time</th>
                <th>Lobby Host</th>
                <th>User</th>
                <th>Message</th>
                <th>Handled</th>
                <th>Consequences</th>
                <th>Handled By</th>
                </thead>
                <tbody>
                @foreach($handledMessages as $message)
                    <x-admin.tools.chat-message-entry :$message/>
                @endforeach
                </tbody>
            </table>
        @endif
        <div class="mt-4">
            {{ $messages->links() }}
        </div>
    </div>
</x-layouts.admin>
