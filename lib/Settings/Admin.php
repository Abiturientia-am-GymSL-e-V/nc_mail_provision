<?php
namespace OCA\MailProvision\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;

class Admin implements ISettings {

    public function getForm() {
        return new TemplateResponse('mail_provision', 'admin');
    }

    public function getSection() {
        return 'additional';
    }

    public function getPriority() {
        return 50;
    }
}