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
                        Note: Look <a href="{{ route('download') }}#faq" class="weblink">here</a> for more information on how to fix this issue!
                    </x-web.text>
                </li>
				<li>
                    <x-web.text>
                        The character loadouts are not loading / I am receiving an “Unknown Matchmaking Error”.
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Note: Look at the <a href="{{ route('homepage') }}#faq" class="weblink">FAQ</a> for more information on how to fix this issue!
                    </x-web.text>
                </li>
				<li>
                    <x-web.text>
                        [Not fixable] The Tally Screen displays incorrect/zero progression progress.
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Note: Progression is still granted as intended!
                    </x-web.text>
                </li>
				<li>
                    <x-web.text>
                        [Not fixable] Hexagons/Tiles are not loading once in-game.
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Note: This happens when your PC is suffering from poor performance or your latency to the Hunter is too high.
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        [Not Fixable] Certain Blood Needles may display the incorrect amount of blood needed to complete the objective.
                    </x-web.text>
					<x-web.text class="font-semibold">
                        Note: Note: The correct amount of blood is still displayed in the top-left corner of the heads-up display (HUD).
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        [Not Fixable] Game may crash when swapping too quickly between characters or loadouts.
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        [Not Fixable] The Veteran has different field of views (FoV) when swapping between weapons.
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        [Not Fixable] After spending currency, the item icons may not update and display that you do not have enough currency to purchase any remaining items. Instead the player will receive a “Purchase Error”.
                    </x-web.text>
                </li>
                <li>
                    <x-web.text>
                        [Not Fixable] Players may occasionally get stuck in certain UI elements.
                    </x-web.text>
                </li>
            </ul>

            <x-web.text class="mt-4 ">
                If you find any other issues, please share them on our
                <a href="https://discord.gg/7MqudBGyyp" target="_blank" class="weblink"> Discord </a>
                in the dedicated bug report section or send us an email to
                <a href="mailto:contact@playdeathgarden.live" target="_blank" class="weblink">
                    contact@playdeathgarden.live
                </a> with more information.
            </x-web.text>
        </x-web.text>
    </div>
</x-layouts.web>