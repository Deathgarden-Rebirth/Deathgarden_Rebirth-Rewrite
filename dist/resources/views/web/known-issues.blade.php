<x-layouts.web>
    <div class="container flex flex-col justify-center items-center mx-auto py-8 2xl:px-48">
        <x-web.headline class="w-full">
            Known Issues
        </x-web.headline>
        <x-web.text>
            Last Updated: 09/06/2024
        </x-web.text>

        <x-web.text>
            <ul class="list-disc ml-6 space-y-4">
                <li >
                    <x-web.text>
                        The game can’t connect to the First-Party Platform.
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Note: Look <a href="{{ route('download') }}#faq" class="weblink">here</a> for more information to fix this issue!
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        The Players loadout is not getting loaded and is getting “Unknown Matchmaking Error”.
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Note: Look at the <a href="{{ route('homepage') }}#faq" class="weblink">FAQ</a> to find out how to fix this issue.
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        Game may crash when swapping to fast Characters / Loadouts (not fixable)
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        The Veteran have different FoV’s when swapping weapons (not fixable)
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        After spending currency, the items widget not getting updated to show you that you don’t
                        have enough currency to buy the item. The player will get an “Purchase Error” (UI not fixable)
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        The Tally Screen shows no progression progress. (not fixable)
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Note: Progression is still getting granted!
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        Hexagons may not get loaded for the Scavengers. (not fixable)
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Note: This happens when you have a slow PC / poor connection to the Hunter.
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        Players may get stuck in certain UI elements (Very rare. Not fixable)
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        The Blood Needle UI-Element shows “100” charges needed and doesn't update the value. (not fixable)
                    </x-web.text>
                </li>
            </ul>

            <x-web.text class="mt-4 ">
                If you find any other issues, please share them on our
                <a href="https://discord.gg/7MqudBGyyp/" target="_blank" class="weblink"> Discord </a>
                in the dedicated bug report section or send us an email to
                <a href="mailto:contact@playdeathgarden.live" target="_blank" class="weblink">
                    contact@playdeathgarden.live
                </a> with more information.
            </x-web.text>
        </x-web.text>
    </div>
</x-layouts.web>