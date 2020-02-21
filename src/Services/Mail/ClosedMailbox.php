<?php

namespace App\Services\Mail;

use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Mail\Action\Action;
use App\Services\Mail\DTO\EMail;
use App\Services\Mail\Mailbox;

/**
 * "Closed" Mailbox service
 * Immutable object
 */
final class ClosedMailbox implements Mailbox
{

    /** @var [type] **/
    private $eMailService;
    /** @var string **/
    private $folder;
    /** @var string **/
    private $imap;
    /** @var LoggerInterface **/
    private $logger;
    /** @var string **/
    private $password;
    /** @var int **/
    private $port;
    /** @var string **/
    private $user;

    /**
     * @param string          $imap
     * @param int             $port
     * @param string          $folder
     * @param string          $user
     * @param string          $password
     * @param [type]          $eMailService
     * @param LoggerInterface $logger
     */
    public function __construct(
        string $imap,
        int $port,
        string $folder,
        string $user,
        string $password,
        $eMailService,
        LoggerInterface $logger
    ) {
        $this->eMailService = $eMailService;
        $this->folder       = $folder;
        $this->imap         = $imap;
        $this->logger       = $logger;
        $this->password     = $password;
        $this->port         = $port;
        $this->user         = $user;
    }

    /**
     * @inheritDoc
     */
    public function applyOnTheEMails(string $criteria, Action $action): Mailbox
    {
        // do nothing, the mailbox is not open
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function clean(callable $onSuccess): Mailbox
    {
        // do nothing, the mailbox is not open
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function close(): Mailbox
    {
        // do nothing, the mailbox is already closed
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(string $criteria, callable $callback): Mailbox
    {
        $callback(0);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(EMail $email, callable $success): Mailbox
    {
        // do nothing, the mailbox is not open
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function imap()
    {
        throw new \Exception('la boîte mail est fermée, la resource IMAP est indisponible');
    }

    /**
     * @inheritDoc
     */
    public function move(EMail $email, string $target, callable $success, callable $failure): Mailbox
    {
        // do nothing, the mailbox is not open
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function open(callable $success, callable $failure): Mailbox
    {
        $mailbox = imap_open(
            sprintf('{%s:%s}%s', $this->imap, $this->port, $this->folder),
            $this->user,
            $this->password
        );

        if (false === $mailbox) {
            $failure(sprintf(
                'impossible d\'ouvrir la boîte mail "%s:%s" pour l\'utilisateur "%s"',
                $this->imap,
                $this->folder,
                $this->user
            ));

            return $this;
        }

        $success(''); // no success message

        return new OpenedMailbox(
            $this,
            $mailbox,
            $this->eMailService,
            $this->logger
        );
    }

}
