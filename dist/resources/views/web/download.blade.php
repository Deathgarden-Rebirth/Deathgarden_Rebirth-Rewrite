@props([
    'files' => [],
])

@php
    /** @var \App\Models\GameFile[] $files */
@endphp


<x-layouts.web>
    <div class="container flex flex-col justify-center items-center mx-auto py-8 2xl:px-48">
        <x-web.headline class="w-full">
            Deathgarden: Rebirth Launcher
        </x-web.headline>

        <x-web.text class="">
            The game launcher, available only for Windows, is essential for updating and playing
            Deathgarden: Rebirth.
        </x-web.text>
        <x-web.text>
            By downloading the launcher, you'll also be able to see how many players
            are online and in the queue, keeping you informed and ready to jump into the action.
            Please note that you'll still need to have Steam open to log in.
        </x-web.text>

        <img class="max-h-[600px] my-8 border rounded-md border-web-main" src="{{ asset('img/launcher_img.png') }}" alt="Deathgarden: Rebirth Launcher">

        <a href="{{ route('download.launcher') }}" target="_blank">
            <x-inputs.button type="button"
                             class="my-12 !px-8 !py-6 !bg-web-main hover:scale-110 !transition-transform !border-web-main">
            <span class="text-3xl font-semibold">
                Download for Windows
            </span>
            </x-inputs.button>
        </a>

        <x-web.accordeon
                headline="How to Set Up Deathgarden: Rebirth on Linux"
                class="w-full"
        >
            @if(count($files) > 0)
                <x-web.text>
                    Follow these instructions to correctly modify and launch Deathgarden: Rebirth on your Linux system
                    using Steam.
                </x-web.text>

                <table class="mt-4">
                    <thead class="!bg-slate-950">
                    <tr>
                        <th>File</th>
                        <th>Game Path</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($files as $file)
                        <tr>
                            <td>{{ $file->name }}</td>
                            <td>{{ $file->game_path }}</td>
                            <td>
                                <a class="weblink" href="{{ route('patch.file', ['hash' => $file->hash]) }}"
                                   target="_blank">
                                    Download
                                </a>
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>

                <x-web.headline class="-mb-0.5">
                    Step 1: Add Deathgarden to Your Steam Library
                </x-web.headline>

                <x-web.text>
                    <ol class="list-decimal list-inside [li]:my-2">
                        <li>
                            <span class="font-bold">Check if Deathgarden: BLOODHARVEST is already in your Steam Library.</span>
                        </li>
                        <li>
                            If not, press <span class="font-bold">Win + R</span> on your keyboard (or use the Linux equivalent such as Alt+F2 to open a command runner).
                        </li>
                        <li>
                            Type in the following command and press <span class="font-bold">Enter:</span>
                            <span class="bg-slate-600 px-2 rounded-md font-semibold cursor-pointer inline-flex items-center justify-center gap-2"
                                  x-on:click="navigator.clipboard.writeText('steam://run/555440'); window.alert('Copied to Clipboard.');"
                            >
                                <x-icons.clipboard class="size-5 inline" />
                                <span class="text-nowrap">steam://run/555440</span>
                            </span>
                        </li>
                    </ol>
                </x-web.text>

                <x-web.headline class="mt-6 -mb-0.5">
                    Step 2: Delete the BattlEye Folder
                </x-web.headline>

                <x-web.text>
                    <ol class="list-decimal list-inside [li]:my-2">
                        <li>
                            Open your file explorer and navigate to the following path:<br>
                            <span class="bg-slate-600 px-2 rounded-md font-semibold">
                                /YourDrive/SteamLibrary/steamapps/common/DEATHGARDEN/TheExit/Binaries/Win64
                            </span>
                        </li>
                        <li>
                            Locate and <span class="font-bold">delete</span> the <span class="font-bold">BattlEye</span> folder
                        </li>
                    </ol>
                </x-web.text>

                <x-web.headline class="mt-6 -mb-0.5">
                    Step 3: Rename Files
                </x-web.headline>

                <x-web.text>
                    <ol class="list-decimal list-inside [li]:my-2">
                        <li>
                            In the same directory:<br>
                            <span class="bg-slate-600 px-2 rounded-md font-semibold">
                                /YourDrive/SteamLibrary/steamapps/common/DEATHGARDEN/TheExit/Binaries/Win64
                            </span>
                        </li>
                        <li>
                            Find the file named <span class="font-bold">TheExit_BE.exe</span> and rename it to <span class="font-bold">Old.exe</span>
                        </li>
                        <li>
                            Next, find the file named <span class="font-bold">TheExit.exe</span> and rename it to <span class="font-bold">TheExit_BE.exe.</span>
                        </li>
                    </ol>
                </x-web.text>

                <x-web.headline class="mt-6 -mb-0.5">
                    Step 4: Launch Deathgarden with Specific Arguments
                </x-web.headline>

                <x-web.text>
                    <ol class="list-decimal list-inside [li]:my-2">
                        <li>
                            <span class="font-bold">Open Steam</span> and go to your <span class="font-bold">Library</span>.
                        </li>
                        <li>
                            Right-click on <span class="font-bold">Deathgarden: BLOODHARVEST</span> and select <span class="font-bold">Properties</span>.
                        </li>
                        <li>
                            In the <span class="font-bold">Launch Options</span> field, enter the following launch argument:
                            <ul class="ml-4">
                                <li class="bg-slate-600 px-2 rounded-md font-semibold w-min text-nowrap cursor-pointer flex justify-center items-center gap-2"
                                    x-on:click="navigator.clipboard.writeText('-battleye'); window.alert('Copied to Clipboard.');"
                                >
                                    <x-icons.clipboard class="size-5 inline" />
                                    -battleye
                                </li>
                            </ul>
                        </li>
                        <li>
                            If you have a 10th generation or newer Intel Core Processor, you need to add additional arguments:
                            <ul class="ml-4">
                                <li class="bg-slate-600 px-2 rounded-md font-semibold w-min text-nowrap cursor-pointer flex justify-center items-center gap-2"
                                    x-on:click="navigator.clipboard.writeText('{{ 'cmd /c "set OPENSSL_ia32cap=:~0x20000000 && %command%" , -battleye' }}'); window.alert('Copied to Clipboard.');"
                                >
                                    <x-icons.clipboard class="size-5 inline" />
                                    cmd /c "set OPENSSL_ia32cap=:~0x20000000 && %command%" , -battleye
                                </li>
                            </ul>
                        </li>
                    </ol>
                </x-web.text>

                <x-web.headline class="mt-6 -mb-0.5">
                    Step 5: Launch the Game
                </x-web.headline>

                <x-web.text>
                    <ol class="list-decimal list-inside [li]:my-2">
                        <li>
                            After setting the launch arguments, close the properties window.
                        </li>
                        <li>
                            Click <span class="font-bold">Play</span> in Steam to launch <span class="font-bold">Deathgarden: Rebirth</span>.
                        </li>
                    </ol>
                </x-web.text>

                <hr class="my-4 border-web-main">

                <x-web.text>
                    <span class="font-bold">Note: </span>
                    These steps help in bypassing issues related to the BattlEye anti-cheat system, especially for newer Intel Core processors.
                </x-web.text>

                <x-web.text>
                    You should now be able to play <span class="font-bold">Deathgarden: Rebirth</span> on your Linux system with the modified
                    settings.
                </x-web.text>
            @else
                There are no Files uploaded yet.
            @endif
        </x-web.accordeon>

        <x-web.headline class="w-full mt-12 !text-4xl" id="faq">
            Frequently Asked Questions
        </x-web.headline>

        <x-web.accordeon headline="Steam installed Deathgarden. Why can’t the Launcher find the installation?" class="w-full my-2">
            <x-web.text>
                Restart the Launcher. If the launcher still can’t detect the installation, you can click on “Browse”
                and go manually to the installation Path of Deathgarden.
            </x-web.text>
            <x-web.text>
                The launcher will then verify the installation.
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="Why does verifying take so long?" class="w-full my-2">
            <x-web.text>
                It can take up to 30-60 seconds to verify the installation.
                If the button turns red again, you can click on “Patch” and then on “Update” to install the latest mod updates.
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="I start the game through the Launcher and getting a “Can’t connect to the First-Party Platform Error”?" class="w-full my-2">
            <x-web.text>
                If this happens, there are some ways to fix the issue:
                <ul class="list-disc list-inside ml-4 my-2">
                    <li>
                        Check if Steam is open
                    </li>
                    <li>
                        Restart Steam
                    </li>
                    <li>
                        Start the Deathgarden: Rebirth Launcher as Administrator
                    </li>
                    <li>
                        Verify the Integrity of the Game Files through Steam + perform the Launcher setup steps again
                    </li>
                    <li>
                        Re-Install the game through Steam + perform the Launcher setup steps again.
                    </li>
                </ul>
                If nothing else works, please contact someone with the “Deathgarden: Rebirth Devs” role in
                <a href="https://discord.gg/7MqudBGyyp" target="_blank" class="weblink">
                    Discord
                </a> or send us an email to: <a href="mailto:contact@playdeathgarden.live" target="_blank" class="weblink">
                    contact@playdeathgarden.live
                </a>
            </x-web.text>
        </x-web.accordeon>

        <x-web.accordeon headline="Why aren't my characters loading, and why am I getting an &quot;Unknown Matchmaking Error&quot;?" class="w-full my-2">
            <x-web.text>
                If your game is not in sync with the latest update, certain features, like the catalog (including
                characters, prices, and other content), will not load correctly.
            </x-web.text>
            <x-web.text>
                Additionally, you may encounter matchmaking errors that prevent you from joining or playing games until your mod is updated through the Launcher.
            </x-web.text>
        </x-web.accordeon>



    </div>
</x-layouts.web>