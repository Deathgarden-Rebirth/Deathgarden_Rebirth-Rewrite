@props([
    'userList',
    'searchString' => null,
    'currentPage' => 1,

])

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $userList */
@endphp


<x-layouts.admin>
    <div class="w-full p-2 md:px-16">
        <form method="get">
        <div class="bg-slate-300 dark:bg-slate-800 my-4 p-4 rounded-xl">
            <x-inputs.text
                    id="search"
                    name="search"
                    placeholder="ID, Steam ID or Username"
                    value="{{ $searchString }}"
            />
            <x-inputs.button class="mt-2">
                Search
            </x-inputs.button>
        </div>
        </form>

        <table class="border-spacing-3">
            <thead>
            <th>User ID</th>
            <th>Steam ID</th>
            <th>Last Known Username</th>
            <th>Status</th>
            <th>Actions</th>
            </thead>
            <tbody>
            @foreach($userList as $user)
                <tr class="text-center">
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->steam_id }}</td>
                    <td class="text-left">{{ $user->last_known_username }}</td>
                    @if($user->ban()->exists())
                        <td class="text-red-600">BANNED</td>
                    @else
                        <td class="text-green-400">GOOD</td>
                    @endif
                    <td>
                        <x-inputs.button type="button" class="px-2 py-1 !text-sm" title="User Details">
                            <x-icons.user-details class="size-4"/>
                        </x-inputs.button>
                        <x-inputs.button type="button" class="px-2 py-1 !text-sm" title="Inbox">
                            <x-icons.mail class="size-4"/>
                        </x-inputs.button>
                        <x-inputs.button type="button" class="px-2 py-1 !text-sm delete" title="Bans">
                            <x-icons.hammer class="size-4"/>
                        </x-inputs.button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $userList->links() }}
        </div>
    </div>
</x-layouts.admin>