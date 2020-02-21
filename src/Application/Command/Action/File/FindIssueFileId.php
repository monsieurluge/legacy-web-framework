<?php

namespace App\Application\Command\Action\File;

use PDO;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Command\Action\Action;
use App\Services\Database\Database;
use App\Services\File\File;
use App\Services\Error\FileNotFound;

/**
 * Finds a file by its ID.
 */
final class FindIssueFileId implements Action
{
    /** @var Database **/
    private $database;
    /** @var File **/
    private $file;

    /**
     * @param Database $database
     * @param File     $file
     */
    public function __construct(Database $database, File $file)
    {
        $this->database = $database;
        $this->file     = $file;
    }

    /**
     * @inheritDoc
     */
    public function handle($target): Result
    {
        $result = $this->database
            ->query(
                'select id from incident_pj where incident_id = :issue and pj = :name',
                PDO::FETCH_ASSOC,
                [
                    'issue' => $target->id,
                    'name'  => $this->file->name()
                ]
            );

        return false === $result
            ? new Failure(
                new FileNotFound(),
                sprintf(
                    'the file "%s" is not attached to the issue #%s',
                    $this->file->name(),
                    $target->id
                )
            )
            : new Success($result[0]['id']);
    }
}
