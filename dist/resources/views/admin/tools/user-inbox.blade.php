@php
    use App\Http\Controllers\Web\Admin\Tools\InboxMailerController;use App\Models\Game\Inbox\InboxMessage;
@endphp
@props([
    'messages' => collect(),
    'allowEdit' => false,
    'user'
])

@pushonce('head')
    @vite('resources/css/components/admin/tools/inbox-message.scss')
@endpushonce

@php
    /** @var \App\Models\Game\Inbox\InboxMessage[]|\Illuminate\Support\Collection $messages */
    /** @var \App\Models\Game\Inbox\InboxMessage[]|\Illuminate\Support\Collection $deletedMessages */
    $messageCount = $messages->count();

    [$deletedMessages, $messages] = $messages->partition(function (InboxMessage $message) {
        return $message->trashed();
    });

@endphp

<x-layouts.admin>
    @if($messageCount <= 0)
        <div class="flex justify-center items-center gap-6 m-12 text-xl font-bold flex-col">
            <span>The User currently has no inbox messages.</span>
        </div>
    @else
        <div class="w-full p-2 md:px-16 bg-inherit container mx-auto">
            <span class="text-3xl font-bold headline">
                Inbox
            </span>
            @foreach($messages as $message)
                <x-admin.tools.inbox-message :$message :$allowEdit/>
            @endforeach

            <a href="{{ route(InboxMailerController::class, ['users' => [$user->id]]) }}" target="_blank">
                <x-inputs.button class="create w-full">
                    Send Message to {{ $user->last_known_username ?? $user->id }}.
                </x-inputs.button>
            </a>
        </div>
    @endif
</x-layouts.admin>