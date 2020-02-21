<?php

namespace App\Services\Mail\Action;

use App\Services\Mail\Action\AbstractAction;
use App\Services\Mail\Action\Action;
use App\Services\Mail\DTO\EMail;
use App\Services\Mail\Mailbox;

/**
 * Successful Action (immutable object)
 */
final class Success extends AbstractAction implements Action
{

    /** @var Action **/
    private $origin;
    /** @var mixed **/
    private $result;

    /**
     * @param Action $origin
     * @param mixed  $result
     */
    public function __construct(Action $origin, $result = null)
    {
        $this->origin = $origin;
        $this->result = $result;
    }

    /**
     * @inheritDoc
     */
    public function handle(Mailbox $mailbox, EMail $email): Action
    {
        return $this->origin->handle($mailbox, $email);
    }

    /**
     * @inheritDoc
     */
    public function then(callable $callback): Action
    {
        $callback($this->result);

        return $this;
    }

}
