import $ from "jquery";

class ItemSelector {
    static rewardItemContainer = $('.current-items-selection');

    static getSelect2Options(element) {
        return {
            width: '80%',
            minimumInputLength: 3,
            ajax: {
                url: $(element).attr('data-fetch-url'),
                dataType: 'json',
            }
        };
    }

    static createRewardItemElement(type, id, name, amount) {
        return $(`<div class="reward-item">
                        <input type="hidden" name="rewards[type][]" value="${type}">
                        <input type="hidden" name="rewards[id][]" value="${id}">
                        <input type="hidden" name="rewards[amount][]" value="${amount}">
                        <div>
                            <label class="!w-16">Item</label>
                            <span class="!overflow-auto text-nowrap">${name}</span>
                        </div>
                        <div class="mt-2">
                            <label class="!w-16">Amount</label>
                            <span class="mr-auto">${amount}</span>
                            <button type="button" class="border rounded-md bg-red-800 border-red-600 px-2 hover:bg-red-600">Delete</button>
                        </div>
                  </div>`);
    }

    static initCatalogDropdowns(){
        let catalogDropdowns = $('.catalog-item-selector');

        catalogDropdowns.each((index, element) => {
            let elem = $(element);
            elem.select2(ItemSelector.getSelect2Options(element));
        })
    }

    static initItemDeleteButtons() {
        let rewardItemElements = $('.reward-item');

        rewardItemElements.each((index, element) => {
            //let button = $(element).find('button');
            $(element).on('click', 'button', () => {
                $(element).remove();
            })
        })
    }

    static addItem() {

    }

    static {
        ItemSelector.initCatalogDropdowns();
        ItemSelector.initItemDeleteButtons()

        let addItemButton = $('button[id="add-item-button"]');
        addItemButton.on('click', () => {
            let data = $('.item-selector #add-item-id').select2('data')[0];

            // Check if we have selected something and if it exists already
            if(typeof data === 'undefined' || data.disabled || ItemSelector.rewardItemContainer.find('input[value="' + data.id + '"]').length > 0)
                return;

            let newRewardItem = ItemSelector.createRewardItemElement(
                'Inventory',
                data.id,
                data.text,
                1
            );
            ItemSelector.rewardItemContainer.append(newRewardItem);
            newRewardItem.on('click', 'button', () => {
                $(newRewardItem).remove();
            })
        })

        let addCurrencyButton = $('button[id="add-currency-button"]');
        addCurrencyButton.on('click', () => {
            let type = $('.item-selector #add-currency-type').val();
            let amount = $('.item-selector #add-currency-amount').val();
            console.log(amount);

            // Check if we have selected something and if it exists already
            if(amount === '' || ItemSelector.rewardItemContainer.find('input[value="' + type + '"]').length > 0)
                return;

            let newRewardItem = ItemSelector.createRewardItemElement(
                'Currency',
                type,
                type,
                amount
            );
            ItemSelector.rewardItemContainer.prepend(newRewardItem);
            newRewardItem.on('click', 'button', () => {
                $(newRewardItem).remove();
            })
        });
    }
}



