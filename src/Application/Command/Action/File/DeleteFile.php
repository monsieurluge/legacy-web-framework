<?php

namespace App\Application\Command\Action\File;

use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Domain\DTO\FileDescription;
use App\Services\Error\CannotDeleteFile;

/**
 * Represents the action which deletes physically an issue's attachment, using
 *   its file description.
 */
final class DeleteFile implements Action
{
    /**
     * @param FileDescription $target
     *
     * @return Result a Result<FileDescription>
     */
    public function handle($target): Result
    {
        $deleted = $this->deletedFromGlobalFolder($target)
            || $this->deletedFromIssueFolder($target)
            || $this->deletedFromThumbnailFolder($target);

        return $deleted
            ? new Success($target)
            : new Failure(
                new CannotDeleteFile(),
                sprintf('cannot delete the file #%s', $target->identifier()->value())
            );
    }

    /**
     * [deletedFromGlobalFolder description]
     *
     * @param FileDescription $target
     *
     * @return bool
     */
    private function deletedFromGlobalFolder(FileDescription $target): bool
    {
        $path = sprintf(
            '%s/%s',
            DOSSIER_UPLOAD,
            $target->name()->value()
        );

        return unlink($path);
    }

    /**
     * [deletedFromIssueFolder description]
     *
     * @param FileDescription $target
     *
     * @return bool
     */
    private function deletedFromIssueFolder(FileDescription $target): bool
    {
        $path = sprintf(
            '%s/%s/%s',
            DOSSIER_UPLOAD,
            $target->issueId()->value(),
            $target->name()->value()
        );

        return unlink($path);
    }

    /**
     * [deletedFromThumbnailFolder description]
     *
     * @param FileDescription $target
     *
     * @return bool
     */
    private function deletedFromThumbnailFolder(FileDescription $target): bool
    {
        $path = sprintf(
            '%s/%s/%s',
            DOSSIER_UPLOAD,
            'thumbnail',
            $target->name()->value()
        );

        return unlink($path);
    }
}
