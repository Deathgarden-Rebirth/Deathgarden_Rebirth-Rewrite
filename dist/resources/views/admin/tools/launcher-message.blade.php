@props([
    'message' => null
])

@php
    use App\Models\Admin\LauncherMessage;

   /** @var ?LauncherMessage $message */
@endphp

<x-layouts.admin>
    <div class="container mx-auto border bg-slate-800 border-slate-500 rounded-xl  p-4 my-6">
        <form action="{{ route('launcherMessage.save') }}" method="post">
            @csrf
            <div class="flex flex-col gap-4">
                <label for="message" class="headline">
                    Message
                </label>

                <x-inputs.text
                        id="message"
                        name="message"
                        value="{{ $message?->message }}"
                        required
                />

                <label for="message" class="headline mt-4">
                    Url
                </label>

                <x-inputs.text
                        id="url"
                        name="url"
                        value="{{ $message?->url }}"
                />
            </div>

            <x-inputs.button class="save mt-8">
                Save
            </x-inputs.button>
        </form>
    </div>
</x-layouts.admin>