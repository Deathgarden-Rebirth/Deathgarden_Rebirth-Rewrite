@php
     use App\Models\User\User;

    /** @var \App\Models\Admin\Archive\ArchivedGame $game */
/** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Admin\Archive\ArchivedPlayerProgression[] $players */
@endphp

<div>
    <h3 class="text-2xl my-4 headline">
        MATCH ID: {{ $game->id }}
    </h3>
    <table>
        <thead>
            <tr>
                <th>
                    User
                </th>
                <th>
                    Has Quit
                </th>
                <th>
                    Played Character
                </th>
                <th>
                    Character State
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($players as $user)
                <tr>
                    <td>
                        <x-auth.user :user="User::find($user->user_id)" class="size-20"/>
                    </td>
                    <td>
                        @if($user->has_quit)
                            <div class="flex justify-center items-center">
                                <x-icons.checkmark class="size-6 bg-green-500 rounded-md"/>
                            </div>
                        @else
                            <div class="flex justify-center items-center">
                                <div class="bg-red-500 rounded-md">
                                    <x-icons.plus class="size-6 rotate-45"/>
                                </div>
                            </div>
                        @endif
                    </td>
                    <td>
                        {{ $user->played_character }}
                    </td>
                    <td>
                        {{ $user->character_state }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3 class="text-2xl my-4 headline">
        CHAT HISTORY
    </h3>
    <x-admin.chat-history :messages="$game->getChatMessages()" />
</div>