<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Repository\History as HistoryRepository;
use App\Services\Security\User\User;

/**
 * Creates a "deleted note" history entry. (immutable object)
 */
final class CreateDeletedNoteHistoryEntry implements Action
{
    /** @var HistoryRepository **/
    private $historyRepository;
    /** @var User **/
    private $user;

    /**
     * @param HistoryRepository $historyRepository
     * @param User              $user
     */
    public function __construct(HistoryRepository $historyRepository, User $user)
    {
        $this->historyRepository = $historyRepository;
        $this->user              = $user;
    }

    /**
     * @inheritDoc
     * @param [type] $target the deleted note
     */
    public function handle($target): Result
    {
        $this->historyRepository->add(
            $target->issueId,
            $this->user->toArray()['id'],
            sprintf(
                'note #%s supprimée (%s)',
                $target->id,
                substr($target->content, 0, 50)
            )
        );

        return new Success($target);
    }
}
