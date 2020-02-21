<?php

namespace App\Services\Mail\Action;

use App\Services\Mail\DTO\EMail;
use App\Services\Mail\Mailbox;

/**
 * E-Mail Action interface
 */
interface Action
{

    /**
     * Apply the action to the given e-mail
     *
     * @param Mailbox $mailbox
     * @param EMail   $email
     *
     * @return Action
     */
    public function handle(Mailbox $mailbox, EMail $email): Action;

    /**
     * Executes the callback using the "do" action if this one has failed
     * @param callable $callback
     * @return Action
     */
    public function else(callable $callback): Action;

    /**
     * Executes the callback using the "do" action if this one has succeeded
     * @param callable $callback
     * @return Action
     */
    public function then(callable $callback): Action;

    /**
     * Adds the parameter to the action
     * @param mixed $parameter
     * @return Action
     */
    public function withParameter($parameter): Action;

}
