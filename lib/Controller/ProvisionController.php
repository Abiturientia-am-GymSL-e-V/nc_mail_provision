<?php

namespace OCA\MailProvision\Controller;

use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCA\MailProvision\Service\ProvisionService;
use OCP\IUserSession;

/**
 * @AdminRequired
 */
class ProvisionController extends Controller {

    private $provisionService;
    private $userSession;

    public function __construct($AppName, IRequest $request, ProvisionService $provisionService, IUserSession $userSession) {
        parent::__construct($AppName, $request);
        $this->provisionService = $provisionService;
        $this->userSession = $userSession;
    }

    /**
     * @NoCSRFRequired
     */
    public function index() {
        $accounts = $this->provisionService->getAllAccounts();
        return new JSONResponse($accounts);
    }

    /**
     * @NoCSRFRequired
     */
    public function show($id) {
        $account = $this->provisionService->getAccount($id);
        return new JSONResponse($account);
    }

    /**
     * @NoCSRFRequired
     */
    public function create($email, $username, $password, $imap_host, $smtp_host) {
        $user = $this->userSession->getUser();
        if (!$user) {
            return new JSONResponse(['error' => 'User not logged in'], 401);
        }
        $userId = $user->getUID();
        $account = $this->provisionService->createAccount($email, $username, $password, $imap_host, $smtp_host, $userId);
        return new JSONResponse($account);
    }

    /**
     * @NoCSRFRequired
     */
    public function update($id, $email, $username, $password, $imap_host, $smtp_host) {
        $account = $this->provisionService->updateAccount($id, $email, $username, $password, $imap_host, $smtp_host);
        return new JSONResponse($account);
    }

    /**
     * @NoCSRFRequired
     */
    public function destroy($id) {
        $this->provisionService->deleteAccount($id);
        return new JSONResponse(['status' => 'success']);
    }

    /**
     * @NoCSRFRequired
     */
    public function getSettings() {
        $settings = $this->provisionService->getSettings();
        return new JSONResponse($settings);
    }

    /**
     * @NoCSRFRequired
     */
    public function updateSettings($settings) {
        $this->provisionService->updateSettings($settings);
        return new JSONResponse(['status' => 'success']);
    }
    /**
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function adminSettings() {
    $settings = $this->provisionService->getSettings();
    return new TemplateResponse('mailprovision', 'admin', [
        'default_imap_host' => $settings['default_imap_host'] ?? '',
        'default_imap_port' => $settings['default_imap_port'] ?? '',
        'default_smtp_host' => $settings['default_smtp_host'] ?? '',
        'default_smtp_port' => $settings['default_smtp_port'] ?? '',
        'sync_interval' => $settings['sync_interval'] ?? '',
    ]);
    }
    /**
     * @NoCSRFRequired
     */
    public function testImapConnection($host, $port, $username, $password, $encryption) {
        return new JSONResponse([
            'success' => $this->provisionService->testImapConnection($host, $port, $username, $password, $encryption)
        ]);
    }
    /**
     * @NoCSRFRequired
     */  
    public function testSmtpConnection($host, $port, $username, $password, $encryption, $from, $to) {
        return new JSONResponse([
            'success' => $this->provisionService->sendTestEmail($host, $port, $username, $password, $encryption, $from, $to)
        ]);
    }
}