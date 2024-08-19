<x-layouts.web>
    <div class="container flex flex-col justify-center items-center mx-auto py-8 2xl:px-48">
        <img src="{{ asset('img/logos/DG_Rebirth_Logo.png') }}"
             alt="Deathgarden Rebirth Logo"
             class="max-h-96"
        >
        <span class="font-extrabold text-4xl my-10">
            EVEN DEATH CAN'T STOP US
        </span>

        <a href="{{ route('download') }}">
            <x-inputs.button type="button"
                             class="my-4 !px-10 !py-8 !bg-web-main hover:scale-110 !transition-all !border-web-main">
            <span class="text-3xl font-bold">
                PLAY NOW
            </span>
            </x-inputs.button>
        </a>

        <x-web.headline class="w-full mt-16">
            What is Deathgarden Rebirth?
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
            To learn more how to survive or kill Scavengers inside the Deathgarden, visit our „How To“ page!
        </x-web.text>

        <div class="h-56 mt-8 w-full bg-cover bg-[center_40%]"
             style="background-image: url('{{ asset('img/backgrounds/background_blur.png') }}')">
        </div>

        <x-web.headline class="w-full mt-12">
            Frequently Asked Questions
        </x-web.headline>

        <x-web.accordeon headline="How can I play Deathgarden: Rebirth?" class="w-full">
            <x-web.text>
                To play Deathgarden: Rebirth, you'll need to download the official launcher, which ensures you
                have the latest updates. For more information, click <a href="{{ route('download') }}" class="weblink">
                    here
                </a>.
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="Does it cost money to play Deathgarden: Rebirth?" class="w-full">
            <x-web.text>
                No, the game and the Rebirth mod are completely free!
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="What personal information is saved?" class="w-full">
            <x-web.text>
                To launch Deathgarden: Rebirth, we need to create a user account in our database using some
                public information from your Steam account.
            </x-web.text>
            <x-web.text class="my-2">
                We only save your SteamID64, Steam profile
                picture, and current username. This information is stored on a secure server with restricted
                access. If you'd like to delete your data and player account, please email us at
                contact@playdeathgarden.live.
            </x-web.text>
            <x-web.text>
                If you'd like to delete your data and player account, please email us at
                <a href="mailto:contact@playdeathgarden.live" class="weblink">
                    contact@playdeathgarden.live
                </a>.
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="This is cool! Can we donate money to the project?" class="w-full">
            <x-web.text>
                Due to legal restrictions, we cannot accept any donations. However, you can support us by
                sharing the project with your friends and others!
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="I’m a programmer. Can I view and submit updates to the backend?" class="w-full">
            <x-web.text>
                The backend has a public repository on GitHub. If you find any bugs or want to contribute to
                certain features, you can submit a pull request.
            </x-web.text>
            <x-web.text>
                Our programming team will review it, and if your
                contribution is added, you’ll be credited on our credits page!
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="I’m an Unreal Engine mod creator. Can I create mods and use them in-game?" class="w-full">
            <x-web.text>
                Using self-created mods is against our Terms of Service. However, if you have cool ideas, you
                can submit a detailed description of your mod and its functionality.
            </x-web.text>
            <x-web.text>
                Once our mod creation
                team reviews it, we may add it to the official supported mod list, with you credited as the
                creator!
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="I’m a content creator and want to support the game." class="w-full">
            <x-web.text>
                That’s awesome! We’re always looking for content creators to promote the game through their
                social media or streaming channels.
            </x-web.text>
            <x-web.text>
                Please send a brief introduction and your social/streaming
                links to <a href="mailto:contact@playdeathgarden.live" class="weblink">
                    contact@playdeathgarden.live
                </a>.
                Our content creator partners receive in-game goodies
                and occasional promotion in the in-game news section!
            </x-web.text>
        </x-web.accordeon>
    </div>
</x-layouts.web>