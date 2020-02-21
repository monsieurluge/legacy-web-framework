<?php

namespace App\Repository;

use monsieurluge\result\Result\Result;
use App\Application\Command\Action\CreateDeletedNoteHistoryEntry;
use App\Application\Command\Action\CreateNewNoteHistoryEntry;
use App\Application\Command\Action\CreateNoteHistoryEntry;
use App\Domain\ValueObject\ID;
use App\Repository\History as HistoryRepository;
use App\Repository\NotesRepository;
use App\Services\Result\CustomAction;
use App\Services\Security\User\User;

/**
 * Notes Repository decorator
 */
final class NotesHistorized implements NotesRepository
{
    /** @var HistoryRepository **/
    private $historyRepository;
    /** @var NotesRepository **/
    private $origin;
    /** @var User **/
    private $user;

    /**
     * @param NotesRepository   $origin              the Notes repository to decorate
     * @param HistoryRepository $historyRepository
     * @param User              $user              [description]
     */
    public function __construct(NotesRepository $origin, HistoryRepository $historyRepository, User $user)
    {
        $this->historyRepository = $historyRepository;
        $this->origin            = $origin;
        $this->user              = $user;
    }

    /**
    * @inheritDoc
    */
    public function create(ID $issueId, User $user, string $content): Result
    {
        return $this->origin
            ->create($issueId, $user, $content)
            ->then(function ($target) use ($user, $issueId) {
                return (new CreateNewNoteHistoryEntry($this->historyRepository, $user, $issueId))->handle($target);
            });
    }

    /**
     * @inheritDoc
     */
    public function delete(int $noteId): Result
    {
        return $this->origin
            ->findById($noteId)
            ->then(function ($target) use ($noteId) {
                return (new CustomAction(function() use ($noteId) {
                    return $this->origin->delete($noteId);
                }))->handle($target);
            })
            ->then(function ($target) {
                return (new CreateDeletedNoteHistoryEntry($this->historyRepository, $this->user))->handle($target);
            });
    }

    /**
     * @inheritDoc
     */
    public function findById(int $noteId): Result
    {
        return $this->origin->findById($noteId);
    }

    /**
     * @inheritDoc
     */
    public function findByIssue(int $issueId): Result
    {
        return $this->origin->findByIssue($issueId);
    }

    /**
     * @inheritDoc
     */
    public function updateContent(int $noteId, string $content): Result
    {
        $oldNote = $this->origin
            ->findById($noteId)
            ->getValueOrExecOnFailure(function() use ($noteId) {
                return (object)[ 'id' => $noteId, 'content' => '' ];
            });

        return $this->origin
            ->updateContent($noteId, $content)
            ->then(function ($target) use ($oldNote) {
                return (new CreateNoteHistoryEntry($this->historyRepository, $oldNote, $this->user))->handle($target);
            });
    }
}
