(function(OCA) {
    OCA.MailProvision = OCA.MailProvision || {};

    OCA.MailProvision.Admin = {
        init: function() {
            $('#mailprovision-settings-form').on('submit', this.saveSettings);
            $('#mailprovision-add-account').on('click', this.showAddAccountForm);
            $('#mailprovision-account-form-element').on('submit', this.saveAccount);
            $('#mailprovision-test-connection').on('click', this.testConnection);
            this.loadAccounts();
            this.loadSettings();
        },

        saveSettings: function(e) {
            e.preventDefault();
            var data = $(this).serialize();

            $.ajax({
                url: OC.generateUrl('/apps/mailprovision/settings'),
                type: 'POST',
                data: data,
                success: function(response) {
                    OC.msg.finishedSuccess('#mailprovision-msg', t('mailprovision', 'Settings saved successfully'));
                },
                error: function(xhr) {
                    OC.msg.finishedError('#mailprovision-msg', t('mailprovision', 'Error saving settings'));
                }
            });
        },

        loadSettings: function() {
            $.ajax({
                url: OC.generateUrl('/apps/mailprovision/settings'),
                type: 'GET',
                success: function(response) {
                    $('#default_imap_host').val(response.default_imap_host);
                    $('#default_imap_port').val(response.default_imap_port);
                    $('#default_smtp_host').val(response.default_smtp_host);
                    $('#default_smtp_port').val(response.default_smtp_port);
                    $('#sync_interval').val(response.sync_interval);
                },
                error: function(xhr) {
                    OC.msg.finishedError('#mailprovision-msg', t('mailprovision', 'Error loading settings'));
                }
            });
        },

        testConnection: function(e) {
            e.preventDefault();
            var data = $('#mailprovision-form').serialize();

            $.ajax({
                url: OC.generateUrl('/apps/mailprovision/test-connection'),
                type: 'POST',
                data: data,
                success: function(response) {
                    OC.msg.finishedSuccess('#mailprovision-test-msg', t('mailprovision', 'Connection successful'));
                },
                error: function(xhr) {
                    OC.msg.finishedError('#mailprovision-test-msg', t('mailprovision', 'Connection failed'));
                }
            });
        },

        loadAccounts: function() {
            $.ajax({
                url: OC.generateUrl('/apps/mailprovision/provision'),
                type: 'GET',
                success: function(response) {
                    OCA.MailProvision.Admin.renderAccounts(response);
                },
                error: function(xhr) {
                    OC.msg.finishedError('#mailprovision-accounts-msg', t('mailprovision', 'Error loading accounts'));
                }
            });
        },

        renderAccounts: function(accounts) {
            var $list = $('#mailprovision-accounts-list tbody');
            $list.empty();

            accounts.forEach(function(account) {
                var $row = $('<tr>')
                    .data('id', account.id)
                    .append($('<td>').text(account.email))
                    .append($('<td>').text(account.username))
                    .append($('<td>').text(account.imap_host))
                    .append($('<td>').text(account.smtp_host))
                    .append($('<td>').html(
                        '<button class="icon-edit" title="' + t('mailprovision', 'Edit') + '"></button>' +
                        '<button class="icon-delete" title="' + t('mailprovision', 'Delete') + '"></button>'
                    ));

                $list.append($row);
            });

            this.bindAccountActions();
        },

        bindAccountActions: function() {
            $('#mailprovision-accounts-list .icon-edit').on('click', this.editAccount);
            $('#mailprovision-accounts-list .icon-delete').on('click', this.deleteAccount);
        },

        showAddAccountForm: function() {
            $('#mailprovision-account-form').removeClass('hidden');
            $('#mailprovision-account-form-title').text(t('mailprovision', 'Add New Account'));
            $('#mailprovision-account-form-element')[0].reset();
            $('#account-id').val('');
        },

        saveAccount: function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            var accountId = $('#account-id').val();
            var url = accountId ? 
                OC.generateUrl('/apps/mailprovision/provision/' + accountId) :
                OC.generateUrl('/apps/mailprovision/provision');
            var method = accountId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: data,
                success: function(response) {
                    OC.msg.finishedSuccess('#mailprovision-msg', t('mailprovision', 'Account saved successfully'));
                    OCA.MailProvision.Admin.loadAccounts();
                    $('#mailprovision-account-form').addClass('hidden');
                },
                error: function(xhr) {
                    OC.msg.finishedError('#mailprovision-msg', t('mailprovision', 'Error saving account'));
                }
            });
        },

        editAccount: function(e) {
            var $row = $(e.target).closest('tr');
            var accountId = $row.data('id');

            $.ajax({
                url: OC.generateUrl('/apps/mailprovision/provision/' + accountId),
                type: 'GET',
                success: function(account) {
                    $('#mailprovision-account-form').removeClass('hidden');
                    $('#mailprovision-account-form-title').text(t('mailprovision', 'Edit Account'));
                    $('#account-id').val(account.id);
                    $('#email').val(account.email);
                    $('#username').val(account.username);
                    $('#imap_host').val(account.imap_host);
                    $('#smtp_host').val(account.smtp_host);
                    // Passwort-Feld leer lassen, da es normalerweise nicht zurückgegeben wird
                },
                error: function() {
                    OC.msg.finishedError('#mailprovision-accounts-msg', t('mailprovision', 'Error loading account details'));
                }
            });
        },

        deleteAccount: function(e) {
            var $row = $(e.target).closest('tr');
            var accountId = $row.data('id');

            OC.dialogs.confirm(
                t('mailprovision', 'Are you sure you want to delete this account?'),
                t('mailprovision', 'Delete Account'),
                function(confirm) {
                    if (confirm) {
                        $.ajax({
                            url: OC.generateUrl('/apps/mailprovision/provision/' + accountId),
                            type: 'DELETE',
                            success: function() {
                                $row.remove();
                                OC.msg.finishedSuccess('#mailprovision-accounts-msg', t('mailprovision', 'Account deleted successfully'));
                            },
                            error: function() {
                                OC.msg.finishedError('#mailprovision-accounts-msg', t('mailprovision', 'Error deleting account'));
                            }
                        });
                    }
                },
                true
            );
        }
    };
})(OCA);

$(document).ready(function() {
    OCA.MailProvision.Admin.init();
});