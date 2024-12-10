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
        return 'mail_provision';
    }

    public function getName() {
        return $this->l->t('Mail Provision');
    }

    public function getPriority() {
        return 50;
    }

    public function getIcon() {
        return $this->url->imagePath('mail_provision', 'app.svg');
    }
}