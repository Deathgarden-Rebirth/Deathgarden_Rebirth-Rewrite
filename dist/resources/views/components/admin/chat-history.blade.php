@php
    /** @var \App\Classes\Frontend\ChatMessage[] $messages */
@endphp

<table>
    <thead>
    <tr>
        <th>
            Timestamp
        </th>
        <th>
            User
        </th>
        <th>
            Message
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($messages as $message)
        <tr>
            <td class="py-0 text-nowrap">
                {{ $message->messageTime }}
            </td>
            <td class="py-0 px-8">
                <x-auth.user :user="$message->getUser()" class="size-20"/>
            </td>
            <td class="py-0 px-4 min-w-24">
                {{ $message->message }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>