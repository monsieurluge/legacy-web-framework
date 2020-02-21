<?php

namespace App\Application\Command\Action\History;

use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Domain\DTO\FileDescription;
use App\Repository\History as HistoryRepository;
use App\Services\Security\User\User;

/**
 * Creates a "file added" history entry.
 */
final class CreateFileAddedHistoryEntry implements Action
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
     * @param FileDescription $target
     *
     * @return Result a Result<FileDescription>
     */
    public function handle($target): Result
    {
        $this->historyRepository->add(
            $target->issueId()->value(),
            $this->user->toArray()['id'],
            sprintf(
                'Document #%s ajouté : %s',
                $target->identifier()->value(),
                $target->name()->value()
            )
        );

        return new Success($target);
    }
}
