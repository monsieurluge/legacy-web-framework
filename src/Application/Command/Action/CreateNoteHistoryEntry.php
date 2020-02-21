<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Repository\History as HistoryRepository;
use App\Services\Security\User\User;

/**
 * Creates a "note" history entry.
 */
final class CreateNoteHistoryEntry implements Action
{
    /** @var HistoryRepository **/
    private $historyRepository;
    /** @var [type] **/
    private $oldNote;
    /** @var User **/
    private $user;

    /**
     * @param [type]              $oldNote
     * @param HistoryRepository   $historyRepository
     * @param User                $user
     */
    public function __construct(
        HistoryRepository $historyRepository,
        $oldNote,
        User $user
    ) {
        $this->historyRepository   = $historyRepository;
        $this->oldNote             = $oldNote;
        $this->user                = $user;
    }

    /**
     * @inheritDoc
     * @param [type] $target the updated Note
     */
    public function handle($target): Result
    {
        $this->historyRepository->add(
            $target->issueId,
            $this->user->toArray()['id'],
            sprintf('note %s modifiée', $target->id)
        );

        return new Success($target);
    }
}
