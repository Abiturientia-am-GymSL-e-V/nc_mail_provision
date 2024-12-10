<?php

namespace OCA\MailProvision;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCA\MailProvision\Cron\ProvisionSync;
use OCP\AppFramework\Utility\ITimeFactory;
use OCA\MailProvision\Service\ProvisionService;
use OCA\MailProvision\BackgroundJob\ProvisionSyncJob;


class Application extends App implements IBootstrap {

    public const APP_ID = 'mailprovision';

    public function __construct() {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void {
        // Registrieren Sie hier Ihre Dienste, Controller, etc.

        $context->registerService('OCA\MailProvision\Cron\ProvisionSync', function($c) {
            return new ProvisionSync(
                $c->get('OCP\AppFramework\Utility\ITimeFactory'),
                $c->get('OCA\MailProvision\Service\ProvisionService')
            );
        });

        // Registrieren Sie den Hintergrundjob
        $context->registerService('OCA\MailProvision\BackgroundJob\ProvisionSyncJob', function($c) {
            return new \OCA\MailProvision\BackgroundJob\ProvisionSyncJob(
                $c->get('OCA\MailProvision\Service\ProvisionService')
            );
        });
    }

    public function boot(IBootContext $context): void {
        // Hier können Sie Aktionen ausführen, die beim Starten der App erforderlich sind
    }
}