<?php

namespace OCA\MailProvision\Controller;

use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCA\MailProvision\Service\ProvisionService;
/**
 * @AdminRequired
 */
class ProvisionController extends Controller {

    private $provisionService;

    public function __construct($AppName, IRequest $request, ProvisionService $provisionService) {
        parent::__construct($AppName, $request);
        $this->provisionService = $provisionService;
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
        $account = $this->provisionService->createAccount($email, $username, $password, $imap_host, $smtp_host);
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
     */
    public function testConnection($imap_host, $smtp_host, $username, $password) {
        $result = $this->provisionService->testConnection($imap_host, $smtp_host, $username, $password);
        return new JSONResponse($result);
    }
}