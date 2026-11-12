import $ from '../app.js';

$('#users').prop('disabled', $('#allUsers').is(':checked'));

$('#allUsers').on('change', (event) => {
    let elem = $(event.target);

    $('#users').prop('disabled', elem.is(':checked'));
});

$('#mailer-form').on('submit', (event) => {
    let users = $('#users').select2('data');
    if(users.length === 0 && !$('#allUsers').prop('checked')) {
        alert('You need to select at least one user to send this message to, or check the checkbox to send it to all.');
        event.preventDefault();
    }
});
