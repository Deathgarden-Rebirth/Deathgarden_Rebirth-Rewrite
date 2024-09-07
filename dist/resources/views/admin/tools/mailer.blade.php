@props([
    'preSelectedUsers' => [],
])

@pushonce('head')
    @vite(['resources/js/admin/tools/mailer.js'])
@endpushonce

<x-layouts.admin>
    <div class="w-full p-2 md:px-16 bg-inherit">
        <div class="container mx-auto bg-slate-300 dark:bg-slate-800 border dark:border-slate-500 rounded-xl my-6 py-2 px-4 shadow-xl dark:shadow-glow dark:shadow-gray-400/30">
            <form action="{{ route('mailer.send') }}" method="post" onsubmit="return validateForm()">
                @csrf
                <div class="flex flex-col gap-4">
                    <label for="users" class="headline">
                        Recipients
                    </label>
                    <x-admin.user-selector id="users" name="users[]" :prefillUsers="$preSelectedUsers" />
                    <div class="flex gap-4 items-center">
                        <x-inputs.checkbox
                                id="allUsers"
                                class="size-6"
                                name="allUsers"
                        />
                        <label for="allUsers">Send to All</label>
                    </div>
                    <label for="title" class="headline">Title</label>
                    <x-inputs.text
                            required
                            id="title"
                            name="title"
                    />
                    <label for="body" class="headline">
                        Body
                    </label>
                    <x-inputs.text-area id="body" name="body" required/>
                    <label class="headline">
                        Metadata
                    </label>
                    <div class="columns-1 md:columns-2">
                        <div class="flex gap-8 items-center">
                            <label for="tag" class="align-middle w-50">Tag</label>
                            <x-inputs.text id="tag" name="tag" required/>
                        </div>
                        <div class="flex gap-8 mt-4 md:mt-0 items-center">
                            <label for="expireAt" class="align-middle">Expire At</label>
                            <x-inputs.date
                                    type="datetime-local"
                                    id="expireAt"
                                    name="expireAt"
                            />
                        </div>
                    </div>
                    <h1 class="headline">Rewards</h1>
                    <x-admin.tools.item-selector />
                    <x-inputs.button class="create">Send</x-inputs.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
