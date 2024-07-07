import $ from 'jquery';

class UserSelector {
    static getSelect2Options(element) {
        return {
            width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: $(element).attr('data-fetch-url'),
                dataType: 'json',
            },
            data: JSON.parse($(element).attr('data-prefill'))
        };
    }

    static initUserDropdowns() {
        let userDropdowns = $('.user-selector');

        userDropdowns.each((index, element) => {
            let elem = $(element);
            elem.select2(UserSelector.getSelect2Options(element));
        })
    }

    static {
        UserSelector.initUserDropdowns();
    }
}