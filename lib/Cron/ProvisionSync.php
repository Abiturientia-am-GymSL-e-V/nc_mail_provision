<?php

namespace OCA\MailProvision\Cron;

use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;
use OCA\MailProvision\Service\ProvisionService;

class ProvisionSync extends TimedJob {

    /** @var ProvisionService */
    private $provisionService;

    public function __construct(ITimeFactory $time, ProvisionService $provisionService) {
        parent::__construct($time);
        $this->provisionService = $provisionService;

        // Einmal pro Stunde ausführen
        $this->setInterval(3600);
    }

    protected function run($argument) {
        \OCP\Util::writeLog('mailprovision', 'Starte Mail-Provisionierungs-Synchronisationsjob', \OCP\Util::INFO);

        try {
            $this->provisionService->kontenSynchronisieren();
            \OCP\Util::writeLog('mailprovision', 'Mail-Provisionierungs-Synchronisation erfolgreich abgeschlossen', \OCP\Util::INFO);
        } catch (\Exception $e) {
            \OCP\Util::writeLog('mailprovision', 'Fehler während der Mail-Provisionierungs-Synchronisation: ' . $e->getMessage(), \OCP\Util::ERROR);
        }
    }
}