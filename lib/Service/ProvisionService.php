<?php

namespace OCA\MailProvision\Service;

use OCP\IDBConnection;
use OCP\IUserManager;
use OCP\IGroupManager;
use OCP\Mail\IMailer;
use OCP\Security\ICrypto;
use OCP\IConfig;
use OCA\Mail\Service\AccountService;
use OCA\Mail\Db\MailAccount;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use Ddeboer\Imap\Server as ImapServer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class ProvisionService {
    private $db;
    private $userManager;
    private $groupManager;
    private $mailer;
    private $crypto;
    private $config;
    private $accountService;
    private $logger;

    public function __construct(
        IDBConnection $db,
        IUserManager $userManager,
        IGroupManager $groupManager,
        IMailer $mailer,
        ICrypto $crypto,
        IConfig $config,
        AccountService $accountService,
        LoggerInterface $logger
    ) {
        $this->db = $db;
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
        $this->mailer = $mailer;
        $this->crypto = $crypto;
        $this->config = $config;
        $this->accountService = $accountService;
        $this->logger = $logger;
    }

    public function getAllAccounts() {
        $qb = $this->db->getQueryBuilder();
        $result = $qb->select('*')
            ->from('mailprovision_accounts')
            ->execute();

        return $result->fetchAll();
    }

    public function getAccount($id) {
        $qb = $this->db->getQueryBuilder();
        $result = $qb->select('*')
            ->from('mailprovision_accounts')
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)))
            ->execute();

        return $result->fetch();
    }

    public function createAccount($email, $username, $password, $imapHost, $smtpHost) {
        $qb = $this->db->getQueryBuilder();
        $qb->insert('mailprovision_accounts')
            ->values([
                'email' => $qb->createNamedParameter($email),
                'username' => $qb->createNamedParameter($username),
                'password' => $qb->createNamedParameter($this->crypto->encrypt($password)),
                'imap_host' => $qb->createNamedParameter($imapHost),
                'smtp_host' => $qb->createNamedParameter($smtpHost),
            ])
            ->execute();

        return $this->getAccount($qb->getLastInsertId());
    }

    public function updateAccount($id, $email, $username, $password, $imapHost, $smtpHost) {
        $qb = $this->db->getQueryBuilder();
        $qb->update('mailprovision_accounts')
            ->set('email', $qb->createNamedParameter($email))
            ->set('username', $qb->createNamedParameter($username))
            ->set('imap_host', $qb->createNamedParameter($imapHost))
            ->set('smtp_host', $qb->createNamedParameter($smtpHost))
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)))
            ->execute();

        if (!empty($password)) {
            $qb->update('mailprovision_accounts')
                ->set('password', $qb->createNamedParameter($this->crypto->encrypt($password)))
                ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)))
                ->execute();
        }

        return $this->getAccount($id);
    }

    public function deleteAccount($id) {
        $qb = $this->db->getQueryBuilder();
        $qb->delete('mailprovision_accounts')
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id)))
            ->execute();
    }

    public function testImapConnection($host, $port, $username, $password, $encryption = 'ssl') {
        try {
            $server = new ImapServer(
                $host,
                $port,
                $encryption === 'ssl' ? '/ssl' : ''
            );
            $connection = $server->authenticate($username, $password);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendTestEmail($smtpHost, $smtpPort, $username, $password, $encryption, $from, $to) {
        try {
            $dsn = sprintf('%s://%s:%s@%s:%d', $encryption, $username, $password, $smtpHost, $smtpPort);
            $transport = Transport::fromDsn($dsn);
            $mailer = new Mailer($transport);

            $email = (new Email())
                ->from($from)
                ->to($to)
                ->subject('Test Email')
                ->text('This is a test email from MailProvision.');

            $mailer->send($email);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function kontenSynchronisieren() {
        $accounts = $this->getAllAccounts();
        foreach ($accounts as $account) {
            $user = $this->userManager->get($account['user_id']);
            if ($user) {
                $this->updateOrCreateMailAccount($user, $account);
            } else {
                $this->deleteAccount($account['id']);
                $this->logger->info('Deleted orphaned account: ' . $account['email'], ['app' => 'mailprovision']);
            }
        }
    }

    private function updateOrCreateMailAccount($user, $account) {
        try {
            $mailAccount = $this->accountService->findByEmail($user->getUID(), $account['email']);
            $this->updateMailAccount($mailAccount, $account);
            $this->logger->info('Updated mail account: ' . $account['email'], ['app' => 'mailprovision']);
        } catch (DoesNotExistException $e) {
            $this->createMailAccount($user, $account);
            $this->logger->info('Created new mail account: ' . $account['email'], ['app' => 'mailprovision']);
        } catch (MultipleObjectsReturnedException $e) {
            $this->logger->error('Multiple accounts found for email: ' . $account['email'], ['app' => 'mailprovision']);
        }
    }

    private function updateMailAccount(MailAccount $mailAccount, $account) {
        $mailAccount->setInboundHost($account['imap_host']);
        $mailAccount->setInboundPort($account['imap_port'] ?? 993);
        $mailAccount->setInboundSslMode('ssl');
        $mailAccount->setOutboundHost($account['smtp_host']);
        $mailAccount->setOutboundPort($account['smtp_port'] ?? 587);
        $mailAccount->setOutboundSslMode('tls');
        $mailAccount->setName($account['username']);
        $mailAccount->setPassword($this->crypto->decrypt($account['password']));

        $this->accountService->update($mailAccount);
    }

    private function createMailAccount($user, $account) {
        $mailAccount = new MailAccount();
        $mailAccount->setUserId($user->getUID());
        $mailAccount->setEmail($account['email']);
        $mailAccount->setName($account['username']);
        $mailAccount->setInboundHost($account['imap_host']);
        $mailAccount->setInboundPort($account['imap_port'] ?? 993);
        $mailAccount->setInboundSslMode('ssl');
        $mailAccount->setOutboundHost($account['smtp_host']);
        $mailAccount->setOutboundPort($account['smtp_port'] ?? 587);
        $mailAccount->setOutboundSslMode('tls');
        $mailAccount->setPassword($this->crypto->decrypt($account['password']));

        $this->accountService->save($mailAccount);
    }

    public function getSettings() {
        return [
            'default_imap_host' => $this->config->getAppValue('mailprovision', 'default_imap_host', ''),
            'default_imap_port' => $this->config->getAppValue('mailprovision', 'default_imap_port', '993'),
            'default_smtp_host' => $this->config->getAppValue('mailprovision', 'default_smtp_host', ''),
            'default_smtp_port' => $this->config->getAppValue('mailprovision', 'default_smtp_port', '587'),
            'sync_interval' => $this->config->getAppValue('mailprovision', 'sync_interval', '3600'),
        ];
    }

    public function saveSettings($settings) {
        foreach ($settings as $key => $value) {
            $this->config->setAppValue('mailprovision', $key, $value);
        }
    }

    public function assignAccountToUser($accountId, $userId) {
        $qb = $this->db->getQueryBuilder();
        $qb->update('mailprovision_accounts')
            ->set('user_id', $qb->createNamedParameter($userId))
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($accountId)))
            ->execute();

        $this->logger->info('Assigned account ' . $accountId . ' to user ' . $userId, ['app' => 'mailprovision']);
    }

    public function assignAccountToGroup($accountId, $groupId) {
        $group = $this->groupManager->get($groupId);
        if ($group) {
            foreach ($group->getUsers() as $user) {
                $this->assignAccountToUser($accountId, $user->getUID());
            }
            $this->logger->info('Assigned account ' . $accountId . ' to group ' . $groupId, ['app' => 'mailprovision']);
        } else {
            $this->logger->error('Group not found: ' . $groupId, ['app' => 'mailprovision']);
        }
    }
}