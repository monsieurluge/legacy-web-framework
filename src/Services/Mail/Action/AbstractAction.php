<?php

namespace App\Services\Mail\Action;

use App\Services\Mail\Action\Action;
use App\Services\Mail\DTO\EMail;
use App\Services\Mail\Mailbox;

/**
 * Abstract Action
 */
abstract class AbstractAction implements Action
{
    /**
     * @inheritDoc
     */
    abstract public function handle(Mailbox $mailbox, EMail $email): Action;

    /**
     * @inheritDoc
     */
    public function else(callable $callback): Action
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function then(callable $callback): Action
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withParameter($parameter): Action
    {
        return $this;
    }
}
