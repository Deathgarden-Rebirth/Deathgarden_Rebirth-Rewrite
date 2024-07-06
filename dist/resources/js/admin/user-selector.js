import $ from 'jquery';

class UserSelector {
    static getSelect2Options(element) {
        return {
            width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: $(element).attr('data-fetch-url'),
                dataType: 'json',
            }
        };
    }

    static initUserDropdowns() {
        let userDropdowns = $('.user-selector');

        userDropdowns.each((index, element) => {
            $(element).select2(UserSelector.getSelect2Options(element));
        })
    }

    static {
        UserSelector.initUserDropdowns();
    }
}