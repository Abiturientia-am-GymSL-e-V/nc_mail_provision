<?php
script('mailprovision', 'admin');
style('mailprovision', 'admin');
?>

<div id="mailprovision" class="section">
    <h2><?php p($l->t('Mail Account Provisioning')); ?></h2>

    <div id="mailprovision-settings">
        <h3><?php p($l->t('Global Settings')); ?></h3>
        <form id="mailprovision-settings-form" class="mailprovision-form">
            <label for="default_imap_host"><?php p($l->t('Default IMAP Host:')); ?></label>
            <input type="text" id="default_imap_host" name="default_imap_host" 
                   value="<?php p($_['default_imap_host']); ?>">

            <label for="default_imap_port"><?php p($l->t('Default IMAP Port:')); ?></label>
            <input type="number" id="default_imap_port" name="default_imap_port" 
                   value="<?php p($_['default_imap_port']); ?>">

            <label for="default_smtp_host"><?php p($l->t('Default SMTP Host:')); ?></label>
            <input type="text" id="default_smtp_host" name="default_smtp_host" 
                   value="<?php p($_['default_smtp_host']); ?>">

            <label for="default_smtp_port"><?php p($l->t('Default SMTP Port:')); ?></label>
            <input type="number" id="default_smtp_port" name="default_smtp_port" 
                   value="<?php p($_['default_smtp_port']); ?>">

            <label for="sync_interval"><?php p($l->t('Sync Interval (seconds):')); ?></label>
            <input type="number" id="sync_interval" name="sync_interval" 
                   value="<?php p($_['sync_interval']); ?>">

            <button type="submit"><?php p($l->t('Save Settings')); ?></button>
        </form>
    </div>

    <div id="mailprovision-accounts">
        <h3><?php p($l->t('Provisioned Accounts')); ?></h3>
        <button id="mailprovision-add-account"><?php p($l->t('Add New Account')); ?></button>
        <table id="mailprovision-accounts-list">
            <thead>
                <tr>
                    <th><?php p($l->t('Email')); ?></th>
                    <th><?php p($l->t('Username')); ?></th>
                    <th><?php p($l->t('IMAP Host')); ?></th>
                    <th><?php p($l->t('SMTP Host')); ?></th>
                    <th><?php p($l->t('Actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <!-- Accounts will be loaded here via JavaScript -->
            </tbody>
        </table>
    </div>

    <div id="mailprovision-account-form" class="hidden">
        <h3 id="mailprovision-account-form-title"><?php p($l->t('Add/Edit Account')); ?></h3>
        <form id="mailprovision-account-form-element" class="mailprovision-form">
            <input type="hidden" id="account-id" name="id">
            
            <label for="email"><?php p($l->t('Email:')); ?></label>
            <input type="email" id="email" name="email" required>

            <label for="username"><?php p($l->t('Username:')); ?></label>
            <input type="text" id="username" name="username" required>

            <label for="password"><?php p($l->t('Password:')); ?></label>
            <input type="password" id="password" name="password" required>

            <label for="imap_host"><?php p($l->t('IMAP Host:')); ?></label>
            <input type="text" id="imap_host" name="imap_host" required>

            <label for="imap_port"><?php p($l->t('IMAP Port:')); ?></label>
            <input type="number" id="imap_port" name="imap_port" value="993" required>

            <label for="smtp_host"><?php p($l->t('SMTP Host:')); ?></label>
            <input type="text" id="smtp_host" name="smtp_host" required>

            <label for="smtp_port"><?php p($l->t('SMTP Port:')); ?></label>
            <input type="number" id="smtp_port" name="smtp_port" value="587" required>

            <button type="submit"><?php p($l->t('Save Account')); ?></button>
            <button type="button" id="mailprovision-cancel-account"><?php p($l->t('Cancel')); ?></button>
        </form>
    </div>

    <div id="mailprovision-assign-account" class="hidden">
        <h3><?php p($l->t('Assign Account')); ?></h3>
        <form id="mailprovision-assign-form" class="mailprovision-form">
            <input type="hidden" id="assign-account-id" name="account_id">
            
            <label for="assign-type"><?php p($l->t('Assign to:')); ?></label>
            <select id="assign-type" name="assign_type">
                <option value="user"><?php p($l->t('User')); ?></option>
                <option value="group"><?php p($l->t('Group')); ?></option>
            </select>

            <label for="assign-target"><?php p($l->t('Select User/Group:')); ?></label>
            <select id="assign-target" name="assign_target">
                <!-- Options will be populated via JavaScript -->
            </select>

            <button type="submit"><?php p($l->t('Assign')); ?></button>
            <button type="button" id="mailprovision-cancel-assign"><?php p($l->t('Cancel')); ?></button>
        </form>
    </div>

    <div id="mailprovision-test-connection" class="hidden">
        <h3><?php p($l->t('Test Connection')); ?></h3>
        <form id="mailprovision-test-form" class="mailprovision-form">
            <label for="test-imap-host"><?php p($l->t('IMAP Host:')); ?></label>
            <input type="text" id="test-imap-host" name="imap_host" required>

            <label for="test-imap-port"><?php p($l->t('IMAP Port:')); ?></label>
            <input type="number" id="test-imap-port" name="imap_port" value="993" required>

            <label for="test-smtp-host"><?php p($l->t('SMTP Host:')); ?></label>
            <input type="text" id="test-smtp-host" name="smtp_host" required>

            <label for="test-smtp-port"><?php p($l->t('SMTP Port:')); ?></label>
            <input type="number" id="test-smtp-port" name="smtp_port" value="587" required>

            <label for="test-username"><?php p($l->t('Username:')); ?></label>
            <input type="text" id="test-username" name="username" required>

            <label for="test-password"><?php p($l->t('Password:')); ?></label>
            <input type="password" id="test-password" name="password" required>

            <button type="submit"><?php p($l->t('Test Connection')); ?></button>
            <button type="button" id="mailprovision-cancel-test"><?php p($l->t('Cancel')); ?></button>
        </form>
        <div id="mailprovision-test-result"></div>
    </div>

    <div id="mailprovision-msg" class="msg"></div>
</div>