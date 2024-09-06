<x-layouts.web>
    <div class="container flex flex-col justify-center items-center mx-auto py-8 2xl:px-48">
        <img src="{{ asset('img/logos/DG_Rebirth_Logo.png') }}"
             alt="Deathgarden: Rebirth Logo"
             class="max-h-96"
        >
        <span class="font-extrabold text-4xl my-10">
            EVEN DEATH CAN'T STOP US
        </span>

        <a href="{{ route('download') }}">
            <x-inputs.button type="button"
                             class="my-4 !px-10 !py-8 !bg-web-main hover:scale-110 !transition-transform !border-web-main">
            <span class="text-3xl font-bold">
                PLAY NOW
            </span>
            </x-inputs.button>
        </a>

        <x-web.headline class="w-full mt-16">
            What is Deathgarden: Rebirth?
        </x-web.headline>

        <x-web.text>
            Deathgarden: Rebirth is a multiplayer survival action game where a relentless hunter tracks and
            eliminates scavengers.
        </x-web.text>

        <x-web.text>
            After five years, the game has been brought back to life by the
            community and is now playable again, featuring new balance changes for an improved
            experience.
        </x-web.text>

        <x-web.text class="mt-8 font-semibold">
            To learn more how to survive or kill Scavengers inside the Deathgarden, visit our
            <a href="{{ route('how-to-play') }}" class="weblink">
                How To
            </a> page!
        </x-web.text>

        <x-web.headline class="w-full mt-12 !text-5xl" id="faq">
            Frequently Asked Questions
        </x-web.headline>

        <x-web.accordeon headline="How can I play Deathgarden: Rebirth?" class="w-full my-2">
            <x-web.text>
                To play Deathgarden: Rebirth, you will need to download the official launcher. 
				The launcher will ensure that you have the latest patches required to connect to the servers. For more information, click <a href="{{ route('download') }}" class="weblink">here.
                </a>
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="Does it cost any money to play Deathgarden: Rebirth?" class="w-full my-2">
            <x-web.text>
                Deathgarden: Rebirth is a completely *free-to-play* experience.
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="Why aren’t my characters loading, and why am I getting an “Unknown Matchmaking Error”?" class="w-full my-2">
            <x-web.text>
                If you do not have the latest patch installed, certain features like the item catalog (which includes characters, prices, and other content) will not load or display correctly.
            </x-web.text>
            <x-web.text>
                Additionally, you will be unable to join or create any matches until your version is updated through the official Deathgarden: Rebirth launcher.
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="Will my progress be restored from the original game?" class="w-full my-2">
            <x-web.text>
                Due to the immense amount of changes made to the character kits and currency gains, all player accounts have been reset. We do not have access to the old playerdata from when the original game was live.
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="Do I have to use the Deathgarden: Rebirth launcher, or can I start the game through Steam?" class="w-full my-2">
            <x-web.text>
                Players are unable to launch Deathgarden directly through Steam. In order to play the game, players must use the launcher to gain access to the latest updates, features, and content in-game.

            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="Is any of my personal information saved?" class="w-full my-2">
            <x-web.text>
                To launch Deathgarden: Rebirth, we need to create a user account in our database using some
                public information from your Steam account.
            </x-web.text>
            <x-web.text class="my-2">
                To launch Deathgarden: Rebirth, players are required to make a user account in our database using public information tied to their Steam account. 
				Only the SteamID64, Steam profile picture, and current username are saved. This information is stored on a secure server with restricted access.
            </x-web.text>
            <x-web.text>
                If you'd like to delete your data and player account, please email us at
                <a href="mailto:contact@playdeathgarden.live" class="weblink">
                    contact@playdeathgarden.live
                </a>.
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="Can we donate money to the project?" class="w-full my-2">
            <x-web.text>
                Due to legal restrictions, we cannot accept any donations at this time. 
				You can show your support by sharing the project with your friends or on social media!
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="I’m a programmer, can I have access or submit updates to the backend code?" class="w-full my-2">
            <x-web.text>
                The backend has a public repository on GitHub. 
				If you find any bugs or want to contribute to certain features, you can submit a pull request.

            </x-web.text>
            <x-web.text>
                Once the request is submitted, our team will review and contact you if the contribution is fit to be added into the game.
				Successful contributions will be credited on our website's credits pages!
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="I’m an Unreal Engine mod creator, can I create mods and use them in-game?" class="w-full my-2">
            <x-web.text>
                Using self-created mods is against our Terms of Service and may result in temporary/permanent account suspension. 
				However, if you would like to submit your mod for review, our team may add it to our official supported mod list.
            </x-web.text>
            <x-web.text>
                Successful mods will be credited to their respective creators!
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="I’m a content creator, how can I support the game?" class="w-full my-2">
            <x-web.text>
                Thank you for your interest! 
				We are always looking for content creators to promote the game through their social media or streaming platforms. <br>
            </x-web.text>
            <x-web.text>
               If you would like to be a Partnered Creator, please send a brief introduction along with your social/streaming links to <a href="mailto:contact@playdeathgarden.live" class="weblink">contact@playdeathgarden.live
                </a>
               Our team will review and contact you if your application has been approved. 
			   Partnered Creators will receive in-game goodies and occasional promotions in the in-game news section!
            </x-web.text>
        </x-web.accordeon>
    </div>
</x-layouts.web>