<?php
namespace OCA\MailProvision\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;

class Admin implements ISettings {

    public function getForm() {
        return new TemplateResponse('mailprovision', 'admin');
    }

    public function getSection() {
        return 'mailprovision';
    }

    public function getPriority() {
        return 10;
    }
}