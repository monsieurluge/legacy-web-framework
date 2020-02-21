<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Domain\ValueObject\ID;
use App\Repository\History as HistoryRepository;
use App\Services\Security\User\User;

/**
 * Creates a "new note" history entry.
 */
final class CreateNewNoteHistoryEntry implements Action
{
    /** @var HistoryRepository **/
    private $historyRepository;
    /** @var ID **/
    private $issueId;
    /** @var User **/
    private $user;

    /**
     * @param HistoryRepository   $historyRepository
     * @param User                $user
     * @param ID                  $issueId
     */
    public function __construct(HistoryRepository $historyRepository, User $user, ID $issueId)
    {
        $this->historyRepository = $historyRepository;
        $this->issueId           = $issueId;
        $this->user              = $user;
    }

    /**
     * @inheritDoc
     * @param ID $target the new note's ID
     */
    public function handle($target): Result
    {
        $this->historyRepository->add(
            $this->issueId->value(),
            $this->user->toArray()['id'],
            sprintf('note créée : #%s', $target->value())
        );

        return new Success($target);
    }
}
