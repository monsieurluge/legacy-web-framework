<?php

namespace App\Services\Mail;

use \Exception;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\ServiceInterfaces\Log\LogLevel;
use App\Services\Mail\Action\Action;
use App\Services\Mail\DTO\EMail;
use App\Services\Mail\LegacyMailService;
use App\Services\Mail\Mailbox;

/**
 * "Opened" Mailbox service
 * Immutable object
 */
final class OpenedMailbox implements Mailbox
{

    /** @var LegacyMailService **/
    private $eMailService;
    /** @var LoggerInterface **/
    private $logger;
    /** @var Mailbox **/
    private $origin;
    /** @var [type] **/
    private $mailbox;

    /**
     * @param Mailbox           $origin
     * @param resource          $mailbox IMAP resource
     * @param LegacyMailService $eMailService
     * @param LoggerInterface   $logger
     */
    public function __construct(Mailbox $origin, $mailbox, LegacyMailService $eMailService, LoggerInterface $logger)
    {
        // the mailbox must be available
        if (FALSE === imap_check($mailbox)) {
            throw new Exception('Impossible de faire le "check IMAP"');
        }

        $this->eMailService = $eMailService;
        $this->logger       = $logger;
        $this->mailbox      = $mailbox;
        $this->origin       = $origin;
    }

    /**
     * @inheritDoc
     */
    public function applyOnTheEMails(string $criteria, Action $action): Mailbox
    {
        $IDs = $this->filter($criteria);

        foreach ($IDs as $ID) {
            $action->handle($this, $this->eMailService->getEmail($this->mailbox, $ID));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function clean(callable $onSuccess): Mailbox
    {
        imap_expunge($this->mailbox);

        $onSuccess();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function close(): Mailbox
    {
        imap_close($this->mailbox);

        return $this->origin;
    }

    /**
     * @inheritDoc
     */
    public function count(string $criteria, callable $callback): Mailbox
    {
        $callback(count($this->filter($criteria)));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(EMail $email, callable $success): Mailbox
    {
        imap_delete($this->mailbox, $email->uid(), FT_UID);

        $success();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function imap()
    {
        return $this->mailbox;
    }

    /**
     * @inheritDoc
     */
    public function move(EMail $email, string $target, callable $success, callable $failure): Mailbox
    {
        $moved = imap_mail_move($this->mailbox, strval($email->uid()), $target, CP_UID);

        if ($moved) {
            $success(sprintf(
                'l\'e-mail #%s ("%s") a été déplacé vers "%s" et marqué pour suppression ; la boîte aux lettes doit être purgée pour que la suppression soit effective',
                $email->uid(),
                $email->subject(),
                $target
            ));
        } else {
            $failure(sprintf(
                'erreur lors du déplacement de l\'e-mail #%s ("%s") vers "%s" : %s',
                $email->uid(),
                $email->subject(),
                $target,
                imap_last_error()
            ));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function open(callable $success, callable $failure): Mailbox
    {
        // do nothing, the mailbox is already opened
        return $this;
    }

    /**
     * Returns the e-mails (IDs only) that matches the criteria
     * @param  string $criteria
     * @return array
     */
    private function filter(string $criteria): array
    {
        $IDs = imap_search($this->mailbox, $criteria);

        if (false === $IDs) {
            return [];
        }

        return $IDs;
    }

}
