<?php

namespace App\Application\Command\Action\File;

use Exception;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Services\Error\CannotUpdateIssue;
use App\Services\File\File;
use App\Services\Security\User\User;
use Legacy\Incident;

/**
 * Add a file to the issue. (immutable object)
 */
final class AddFileToIssue implements Action
{
    /** @var File **/
    private $file;
    /** @var User **/
    private $user;

    /**
     * @param File $file
     * @param User $user
     */
    public function __construct(File $file, User $user)
    {
        $this->file = $file;
        $this->user = $user;
    }

    /**
     * @inheritDoc
     * @param Incident $target
     *
     * @return Result the file ID as a Result<int>
     */
    public function handle($target): Result
    {
        try {
            $target->addPJ(
                $this->user->toArray()['id'],
                $this->file->name()
            );

            $target->enregistrer();

            $this->file->write();
        } catch (Exception $exception) {
            $this->logger->error(sprintf(
                'failed to add a file to the issue #%s: %s',
                $target->id,
                $exception->getMessage()
            ));

            return new Failure(new CannotUpdateIssue());
        }

        return new Success($target);
    }
}
