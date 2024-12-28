<x-layouts.web>
    <div class="container flex flex-col justify-center items-center mx-auto py-8 2xl:px-48">
        <x-web.headline class="w-full">
            Patch Notes
        </x-web.headline>
        <x-web.text>
            Last Updated: 09/14/2024
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
				<li >
                    <x-web.text>
                        [Monitoring - Very Rare] Matchmaking may crash and players receiving "Unknnown Matchmaking Errors"
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Note: When this happens, cancel the matchmaking, wait a minute and try again. We will monitor the Matchmaking and deploy fixes if needed.
                    </x-web.text>
                </li>
				<li >
                    <x-web.text>
                        [Not fixable] The required players' needle icon in the Ready Room disappear when the lobby is broken and got cleared by the backend.
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Solution: As the host, return to the Locker Room and queue for a new match.
                    </x-web.text>
                </li>
				<li >
                    <x-web.text>
                        [Not fixable] Hunter challenges might not be getting tracked.
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Solution: While inside the <span class="font-bold">Arena,</span> switch to a different Hunter and then switch back to the one you want to play.
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
                <li >
                    <x-web.text>
                        [Not fixable] The connection to a friend's Locker Room is failing.
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Note: The RWF lobby leader must wait until all friends have finished their match before leaving the Arena to avoid this issue. If the error occurs, all players should restart their game.
                    </x-web.text>
                </li>
				<li >
                    <x-web.text>
                        [Not fixable] Occasionally, the Hunter's weapon spawns at the center of the map, making it difficult to see where the bullets are coming from during a chase as a Scavenger.
                    </x-web.text>
                </li>
				<li >
                    <x-web.text>
                        [Not fixable] When loaded into the Arena, the audio is muffled.
                    </x-web.text>
                    <x-web.text class="font-semibold">
						[Solution] Press F1 or open the 'Change Character' menu within the Arena to resolve the issue.
                    </x-web.text>
                </li>
				<li >
                    <x-web.text>
                        [Not fixable] The Sawbones Perk 'Splash' assists may sometimes not be tracked.
                    </x-web.text>
                    <x-web.text class="font-semibold">
						[Solution] We recommend using Clones for the challenge, as supporting other Scavengers in chases only sometimes triggers challenge progress.
                    </x-web.text>
                </li>
				<li >
                    <x-web.text>
                        [Not fixable] Claimed inbox drop items are not appearing in the player's inventory.
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Note: The items appear either after restarting the game or after playing one match!
                    </x-web.text>
                </li>
				<li >
                    <x-web.text>
                        [In Investigation] The Matchmaking ETA is showing '0 seconds'.
                    </x-web.text>
                    <x-web.text class="font-semibold">
                        Note: If the ETA shows '0 seconds,' it means you are still in the queue. If there is no ETA displayed, your queue is likely broken. In that case, we recommend canceling the queue and trying again. The team is currently investigating how to display an accurate ETA in minutes.
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
                If you find any other issues, please share them in the #dg-rebirth-bug-reports channel of our <a href="https://discord.gg/7MqudBGyyp" target="_blank" class="weblink"> Discord </a> server or send us an email to
                <a href="mailto:contact@playdeathgarden.live" target="_blank" class="weblink">
                    contact@playdeathgarden.live
                </a> with more information.
            </x-web.text>
        </x-web.text>
    </div>
</x-layouts.web>
