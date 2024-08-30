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

        <a href="{{ route('download.launcher') }}" target="_blank">
            <x-inputs.button type="button"
                             class="my-12 !px-8 !py-6 !bg-web-main hover:scale-110 !transition-all !border-web-main">
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
                            <span class="font-bold">Check if Deathgarden: Rebirth is already in your Steam Library.</span>
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


    </div>
</x-layouts.web>