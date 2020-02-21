<?php

namespace App\Repository;

use DateTime;
use PDO;
use monsieurluge\result\Result\Result;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;
use App\Domain\DTO\FileDescription;
use App\Services\Error\CannotDeleteFile;
use App\Services\Error\FileNotFound;
use App\Services\Database\Database;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Success;

/**
 * Files repository.
 */
final class Files
{
    /** @var Database **/
    private $dataSource;

    /**
     * @param Database $dataSource
     */
    public function __construct(Database $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    /**
     * Deletes a file (only from the DB) identified by its ID
     *
     * @param ID $identifier
     *
     * @return Result a Result<ID>
     */
    public function delete(ID $identifier): Result
    {
        $result = $this->dataSource->exec(
            'DELETE FROM `incident_pj` WHERE `id` = :identifier',
            [ 'identifier' => $identifier->value() ]
        );

        return is_null($result)
            ? new Failure(
                new CannotDeleteFile(),
                sprintf('cannot delete the file #%s', $identifier->value())
            )
            : new Success($identifier);
    }

    /**
     * Returns the file informations
     *
     * @param ID $identifier
     *
     * @return Result
     */
    public function fileById(ID $identifier): Result
    {
        $query = 'SELECT `id`, `incident_id` AS issueId, `pj` AS name, `date` AS createdAt, `utilisateur` AS createdBy'
            . ' FROM `incident_pj`'
            . ' WHERE id = :identifier';

        $file = $this->dataSource->query($query, PDO::FETCH_ASSOC, [ 'identifier' => $identifier->value() ]);

        return empty($file)
            ? new Failure(
                new FileNotFound(),
                sprintf('le fichier #%s n\'a pas été trouvé', $identifier->value())
            )
            : new Success(
                new FileDescription(
                    new ID($file[0]['id']),
                    new Label($this->valueOrDefault($file, 'name', 'fichier sans nom')),
                    new DateTime($file[0]['createdAt']),
                    new ID($this->valueOrDefault($file, 'createdBy', 0)),
                    new ID($file[0]['issueId'])
                )
            );
    }

    /**
     * Returns the file's hash value or the default one
     *
     * @param  array  $file
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    private function valueOrDefault(array $file, string $name, $default)
    {
        return isset($file[0][$name])
            ? $file[0][$name]
            : $default;
    }
}
