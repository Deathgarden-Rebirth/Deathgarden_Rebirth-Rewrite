import $ from "../app.js";

class ItemSelector {
    rewardItemContainer;

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
        let element = $(`<div class="reward-item">
                        <input type="hidden" name="rewards[type][]">
                        <input type="hidden" name="rewards[id][]">
                        <input type="hidden" name="rewards[amount][]">
                        <div>
                            <label class="w-16!">Item</label>
                            <span class="reward-name overflow-auto! text-nowrap"></span>
                        </div>
                        <div class="mt-2">
                            <label class="w-16!">Amount</label>
                            <span class="reward-amount mr-auto"></span>
                            <button type="button" class="border rounded-md bg-red-800 border-red-600 px-2 hover:bg-red-600">Delete</button>
                        </div>
                  </div>`);

        // Values are set through jQuery so they are escaped. attr() instead of val() keeps the
        // value attribute in sync, which the duplicate checks below select on.
        element.find('input[name="rewards[type][]"]').attr('value', type);
        element.find('input[name="rewards[id][]"]').attr('value', id);
        element.find('input[name="rewards[amount][]"]').attr('value', amount);
        element.find('.reward-name').text(name);
        element.find('.reward-amount').text(amount);

        return element;
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

    static {
        ItemSelector.initCatalogDropdowns();
        ItemSelector.initItemDeleteButtons();

        let itemSelectors = $('.item-selector');
        itemSelectors.each((index, element) => {
            let selector = new ItemSelector();
            selector.rewardItemContainer = $(element).find('.current-items-selection');

            let addItemButton = $(element).find('.add-item-button');
            addItemButton.on('click', () => {
                let data = $(element).find('.catalog-item-selector').select2('data')[0];

                // Check if we have selected something and if it exists already
                if(typeof data === 'undefined' || data.disabled || selector.rewardItemContainer.find('input[value="' + data.id + '"]').length > 0)
                    return;

                let newRewardItem = ItemSelector.createRewardItemElement(
                    'Inventory',
                    data.id,
                    data.text,
                    1
                );
                selector.rewardItemContainer.append(newRewardItem);
                newRewardItem.on('click', 'button', () => {
                    $(newRewardItem).remove();
                })
            })

            let addCurrencyButton =  $(element).find('.add-currency-button');
            addCurrencyButton.on('click', () => {
                let type = $(element).find('.add-currency-type').val();
                let amount = $(element).find('.add-currency-amount').val();

                // Check if we have selected something and if it exists already
                if(amount === '' || selector.rewardItemContainer.find('input[value="' + type + '"]').length > 0)
                    return;

                let newRewardItem = ItemSelector.createRewardItemElement(
                    'Currency',
                    type,
                    type,
                    amount
                );
                selector.rewardItemContainer.prepend(newRewardItem);
                newRewardItem.on('click', 'button', () => {
                    $(newRewardItem).remove();
                })
            });
        });




    }
}



