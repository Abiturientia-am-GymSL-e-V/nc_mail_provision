$(document).ready(function() {
    var baseUrl = OC.generateUrl('/apps/mailprovision');

    function loadAccounts() {
        $.get(baseUrl + '/accounts', function(response) {
            var accountsList = $('#mailprovision-accounts-list tbody');
            accountsList.empty();
            response.forEach(function(account) {
                accountsList.append(
                    '<tr>' +
                    '<td>' + account.email + '</td>' +
                    '<td>' + account.username + '</td>' +
                    '<td>' + account.imap_host + '</td>' +
                    '<td>' + account.smtp_host + '</td>' +
                    '<td>' +
                    '<button class="edit-account" data-id="' + account.id + '">Edit</button>' +
                    '<button class="delete-account" data-id="' + account.id + '">Delete</button>' +
                    '</td>' +
                    '</tr>'
                );
            });
        });
    }

    $('#mailprovision-add-account').click(function() {
        $('#mailprovision-account-form-title').text('Add New Account');
        $('#mailprovision-account-form-element')[0].reset();
        $('#mailprovision-account-form').removeClass('hidden');
    });

    $('#mailprovision-account-form-element').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var url = baseUrl + '/accounts';
        var method = $('#account-id').val() ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                loadAccounts();
                $('#mailprovision-account-form').addClass('hidden');
                showMessage('Account saved successfully', 'success');
            },
            error: function() {
                showMessage('Error saving account', 'error');
            }
        });
    });

    $(document).on('click', '.edit-account', function() {
        var accountId = $(this).data('id');
        $.get(baseUrl + '/accounts/' + accountId, function(account) {
            $('#account-id').val(account.id);
            $('#email').val(account.email);
            $('#username').val(account.username);
            $('#imap_host').val(account.imap_host);
            $('#smtp_host').val(account.smtp_host);
            $('#mailprovision-account-form-title').text('Edit Account');
            $('#mailprovision-account-form').removeClass('hidden');
        });
    });

    $(document).on('click', '.delete-account', function() {
        if (confirm('Are you sure you want to delete this account?')) {
            var accountId = $(this).data('id');
            $.ajax({
                url: baseUrl + '/accounts/' + accountId,
                method: 'DELETE',
                success: function() {
                    loadAccounts();
                    showMessage('Account deleted successfully', 'success');
                },
                error: function() {
                    showMessage('Error deleting account', 'error');
                }
            });
        }
    });

    $('#mailprovision-settings-form').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.post(baseUrl + '/settings', formData, function() {
            showMessage('Settings saved successfully', 'success');
        }).fail(function() {
            showMessage('Error saving settings', 'error');
        });
    });

    function showMessage(message, type) {
        var msgElement = $('#mailprovision-msg');
        msgElement.text(message).removeClass('success error').addClass(type).show();
        setTimeout(function() {
            msgElement.hide();
        }, 3000);
    }

    loadAccounts();
});