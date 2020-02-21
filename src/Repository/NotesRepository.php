<?php

namespace App\Repository;

use monsieurluge\result\Result\Result;
use App\Domain\ValueObject\ID;
use App\Services\Security\User\User;

interface NotesRepository
{
    /**
     * Creates a note
     *
     * @param ID     $issueId
     * @param User   $user
     * @param string $content
     *
     * @return Result a Result<ID> where ID is the new note's ID
     */
    public function create(ID $issueId, User $user, string $content): Result;

    /**
     * Deletes the note
     *
     * @param int $noteId
     *
     * @return Result
     */
    public function delete(int $noteId): Result;

    /**
     * Returns a Result<Note>
     *
     * @param int $noteId
     *
     * @return Result
     */
    public function findById(int $noteId): Result;

    /**
     * Returns a Result<Note[]>
     *
     * @param int $issueId
     *
     * @return Result
     */
    public function findByIssue(int $issueId): Result;

    /**
     * Updates the note's content
     *
     * @param int    $noteId
     * @param string $content
     *
     * @return Result the updated note (Result<Note>)
     */
    public function updateContent(int $noteId, string $content): Result;
}
