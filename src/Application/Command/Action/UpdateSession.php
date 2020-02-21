<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Services\Security\Session;

/**
 * Updates a session.
 */
final class UpdateSession implements Action
{
    /** @var Session **/
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritDoc
     * @param [type] $target
     *
     * @return Result a Result<[type]>
     */
    public function handle($target): Result
    {
        $this->session->update([
            'user' => $target->value
        ]);

        return new Success($target);
    }
}
