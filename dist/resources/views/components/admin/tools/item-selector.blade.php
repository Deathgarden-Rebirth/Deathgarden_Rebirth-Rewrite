@pushonce('head')
    @vite(['resources/js/admin/tools/item-selector.js','resources/css/components/admin/tools/item-selector.scss'])
@endpushonce

@php
    /** @var \App\Http\Responses\Api\Player\Inbox\InboxMessageReward[] $rewards */
@endphp

<div class="item-selector">
    <div class="flex flex-col w-[90%] mx-auto">
        <div>
            <div class="current-items-selection p-4 grid gap-2 grid-cols-1 md:grid-cols-2 xl:grid-cols-3">
                @foreach($rewards as $key => $reward)
                    <div class="reward-item">
                        <input type="hidden" name="rewards[type][]" value="{{ $reward->type }}">
                        <input type="hidden" name="rewards[id][]" value="{{ $reward->id }}">
                        <input type="hidden" name="rewards[amount][]" value="{{ $reward->amount }}">
                        <div>
                            <label class="!w-16">Item</label>
                            <span class="!overflow-auto text-nowrap">{{ $reward->getRewardName() }}</span>
                        </div>
                        <div class="mt-2">
                            <label class="!w-16">Amount</label>
                            <span class="mr-auto">{{ $reward->amount }}</span>
                            @if($allowEdit)
                                <button type="button"
                                        class="border rounded-md bg-red-800 border-red-600 px-2 hover:bg-red-600">Delete
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @if($allowEdit)
            <div class="grid grid-cols-2 mx-auto w-full">
                <div class="col-start-1 col-end-1 w-full">
                    <h1 class="headline after:mb-4">Add item</h1>
                    <select class="catalog-item-selector input-global-dropdown w-full"
                            data-fetch-url="{{ route('catalog.dropdown') }}"
                    >
                        <option value="" disabled selected>Select an Item</option>
                    </select><br>
                    <x-inputs.button type="button" class="create mt-2 add-item-button">
                        Add
                    </x-inputs.button>
                </div>
                <div class="flex flex-col col-start-2">
                    <h1 class="headline after:mb-4">Add Currency</h1>
                    <div class="flex gap-4 items-center">
                        <x-inputs.dropdown
                                class="add-currency-type"
                                :cases="['CurrencyA', 'CurrencyB', 'CurrencyC']"
                                :selected="'CurrencyA'"
                        />
                        <span class="ml-auto">Amount</span>
                        <x-inputs.number class="mr-auto add-currency-amount"/>
                    </div>
                    <x-inputs.button type="button" class="create mt-2 w-min add-currency-button">
                        Add
                    </x-inputs.button>
                </div>
            </div>
        @endif
    </div>
</div>
