<?php

namespace App\Services\Mail;

use App\Services\Mail\Action\Action;
use App\Services\Mail\DTO\EMail;

/**
 * Mailbox interface
 */
interface Mailbox
{

    /**
     * Apply the action to the e-mails that matches the given criteria
     * @param string $criteria
     * @param Action $action
     * @return Mailbox
     */
    public function applyOnTheEMails(string $criteria, Action $action): Mailbox;

    /**
     * Removes all the "to delete" flagged messages
     * @param callable $onSuccess
     * @return Mailbox
     */
    public function clean(callable $onSuccess): Mailbox;

    /**
     * Closes the mailbox
     * @return Mailbox
     */
    public function close(): Mailbox;

    /**
     * Returns the messages total count
     * @param string   $criteria
     * @param callable $callback
     * @return Mailbox
     */
    public function count(string $criteria, callable $callback): Mailbox;

    /**
     * Mark the e-mail with the "DELETE" flag
     * @param EMail    $email
     * @param callable $success
     * @return Mailbox
     */
    public function delete(EMail $email, callable $success): Mailbox;

    /**
     * Returns the imap resource
     * TODO fonction à supprimer
     * @return resource
     */
    public function imap();

    /**
     * Moves the e-mail to the target folder
     * @param  EMail    $email
     * @param  string   $target
     * @param  callable $success
     * @param  callable $failure
     * @return Mailbox
     */
    public function move(EMail $email, string $target, callable $success, callable $failure): Mailbox;

    /**
     * Open the mailbox
     * @param callable $success
     * @param callable $failure
     * @return Mailbox
     */
    public function open(callable $success, callable $failure): Mailbox;

}
