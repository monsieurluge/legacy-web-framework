<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Services\Security\User\User;
use Legacy\Incident;

/**
 * Creates a "mail history" entry.
 */
final class CreateMailHistoryEntry implements Action
{
    /** @var User **/
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @inheritDoc
     * @param Incident $target
     */
    public function handle($target): Result
    {
        $target->addHisto($this->user->toArray()['id'], 'envoi d\'un e-mail');

        return new Success($target);
    }
}
