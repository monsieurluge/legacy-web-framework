<?php

namespace App\Application\Command\Action\File;

use monsieurluge\result\Result\Result;
use App\Application\Command\Action\Action;
use App\Domain\DTO\FileDescription;
use App\Repository\Files as FilesRepository;

/**
 * Represents the action which deletes from the database an issue's attachment,
 *   using its file description.
 */
final class DeleteFileFromDB implements Action
{
    /** @var FilesRepository **/
    private $repository;

    /**
     * @param FilesRepository $repository
     */
    public function __construct(FilesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param FileDescription $target
     *
     * @return Result a Result<FileDescription>
     */
    public function handle($target): Result
    {
        return $this->repository->delete($target->identifier());
    }
}
