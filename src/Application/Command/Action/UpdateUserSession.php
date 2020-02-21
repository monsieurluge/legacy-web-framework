<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Application\Query\User as UserDTO;
use App\Services\Security\Session;

/**
 * Updates an user session.
 */
final class UpdateUserSession implements Action
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
     * @param UserDTO $target
     *
     * @return Result a Result<UserDTO>
     */
    public function handle($target): Result
    {
        $this->session
            ->update([ 'user' => $target->identifier()->value() ]);

        return new Success($target);
    }
}
