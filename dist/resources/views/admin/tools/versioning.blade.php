@props([
    'message' => null
])

@php
    use App\Models\Admin\LauncherMessage;
    use App\Models\Admin\Versioning\CurrentCatalogVersion;
    use App\Models\Admin\Versioning\CurrentContentVersion;
    use App\Models\Admin\Versioning\CurrentGameVersion;
    use App\Models\Admin\Versioning\LauncherVersion;

   /** @var ?LauncherMessage $message */
@endphp

<x-layouts.admin>
    <div class="container mx-auto border bg-slate-800 border-slate-500 rounded-xl  p-4 my-6">
        <form action="{{ route('versioning.save') }}" method="post">
            @csrf
            <div class="flex flex-col gap-4">
                <label for="message" class="headline">
                    Launcher Version
                </label>

                <x-inputs.text
                        id="launcherVersion"
                        name="launcherVersion"
                        value="{{ LauncherVersion::get()?->launcherVersion }}"
                        required
                />

                <label for="message" class="headline mt-4">
                    Game Version
                </label>

                <x-inputs.text
                        id="gameVersion"
                        name="gameVersion"
                        value="{{ CurrentGameVersion::get()?->gameVersion }}"
                        required
                />

                <label for="message" class="headline mt-4">
                    Content Version
                </label>

                <x-inputs.text
                        id="contentVersion"
                        name="contentVersion"
                        value="{{ CurrentContentVersion::get()?->contentVersion }}"
                        required
                />

                <label for="message" class="headline mt-4">
                    Catalog Version
                </label>

                <x-inputs.text
                        id="catalogVersion"
                        name="catalogVersion"
                        value="{{ CurrentCatalogVersion::get()?->catalogVersion }}"
                        required
                />
            </div>

            <x-inputs.button class="save mt-8">
                Save
            </x-inputs.button>
        </form>
    </div>
</x-layouts.admin>