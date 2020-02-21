<?php

namespace App\Repository;

use PDO;
use monsieurluge\result\Result\Result;
use App\Domain\ValueObject\ID;
use App\Repository\Offices;
use App\Services\Error\OfficeNotFound;
use App\Services\Database\Database;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Success;

/**
 * Offices repository.
 */
final class BaseOffices implements Offices
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
     * @inheritDoc
     */
    public function findById(ID $identifier): Result
    {
        $result = $this->dataSource->query(
            'SELECT * FROM etudes WHERE code_unique_hj = :officeId',
            PDO::FETCH_ASSOC,
            [ 'officeId' => $identifier->value() ]
        );

        return false === isset($result[0])
            ? new Failure(
                new OfficeNotFound(),
                sprintf('the office #%s does not exist', $identifier->value())
            )
            : new Success($result[0]);
    }

    /**
     * @inheritDoc
     */
    public function findByTerm(string $term): array
    {
        $dbQuery = 'select * from etudes where code_unique_hj like :valeur1 or raison_sociale like :valeur2 order by code_unique_hj limit 20';

        $params  = [
            'valeur1' => ltrim($term, '0') . '%',
            'valeur2' => '%' . trim($term) . '%'
        ];

        return $this->dataSource
            ->query($dbQuery, PDO::FETCH_ASSOC, $params);
    }
}
