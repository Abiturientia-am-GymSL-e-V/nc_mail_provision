<?php
namespace OCA\MailProvision\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

class AdminSection implements IIconSection {
    private $l;
    private $url;

    public function __construct(IL10N $l, IURLGenerator $url) {
        $this->l = $l;
        $this->url = $url;
    }

    public function getID() {
        return 'mailprovision';
    }

    public function getName() {
        return $this->l->t('Mail Account Provisioning');
    }

    public function getPriority() {
        return 50;
    }

    public function getIcon() {
        return $this->url->imagePath('mailprovision', 'app-dark.svg');
    }
}