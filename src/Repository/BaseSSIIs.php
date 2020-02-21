<?php

namespace App\Repository;

use PDO;
use monsieurluge\result\Result\Result;
use App\Domain\ValueObject\ID;
use App\Services\Error\SSIINotFound;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Success;
use App\Repository\SSIIs as SSIIsRepository;
use App\Services\Database\Database;

/**
 * SII repository.
 */
final class BaseSSIIs implements SSIIsRepository
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
        $query = 'SELECT id_ssii AS id, raison_sociale AS name, adresse1 AS addressLine1, adresse2 AS addressLine2, adresse3 AS addressLine3, code_postal AS postalCode, ville AS city, telephone AS phone, e_mail1 AS email1, e_mail2 AS email2, ref_legacy AS office'
            . ' FROM ssii'
            . ' WHERE id_ssii = :identifier';

        $result = $this->dataSource->query($query, PDO::FETCH_ASSOC, [ 'identifier' => $identifier->value() ]);

        return true === empty($result)
            ? new Failure(
                new SSIINotFound(),
                sprintf('the SSII #%s was not found', $identifier->value())
            )
            : new Success($result[0]);
    }

    /**
     * @inheritDoc
     */
    public function findByOffices(array $offices): array
    {
        if (empty($offices)) {
            return [];
        }

        $officesId = array_map(function($office) { return $office['code_unique_hj']; }, $offices);

        $query = 'SELECT * FROM lien_hj_ssii WHERE id_hj IN (' . implode($officesId, ',') . ') ORDER BY id_hj';

        $results = $this->dataSource->query($query, PDO::FETCH_ASSOC);

        return array_reduce(
            $results,
            function($accumulator, $result) {
                return $this->addFormattedSsiiData($accumulator, $result);
            },
            []
        );
    }

    /**
     * @inheritDoc
     */
    public function findByOfficeId(ID $identifier): Result
    {
        $query = 'SELECT s.id_ssii as id, raison_sociale AS name, adresse1 AS addressLine1, adresse2 AS addressLine2, adresse3 AS addressLine3, code_postal AS postalCode, ville AS city, telephone AS phone, e_mail1 AS email1, e_mail2 AS email2'
            . ' FROM ssii s'
            . ' LEFT JOIN lien_hj_ssii l'
            . ' ON s.id_ssii = l.id_ssii'
            . ' WHERE l.id_hj = :officeId';

        $result = $this->dataSource->query($query, PDO::FETCH_ASSOC, [ 'officeId' => $identifier->value() ]);

        return true === empty($result)
            ? new Failure(
                new SSIINotFound(),
                sprintf('there is no SSII for the office #%s', $identifier->value())
            )
            : new Success($result);
    }

    /**
     * Add formatted SSII information to the given list.
     * Example:and the
     *  $list  = [ 12 => 987 ]
     *  $item  = [ 'id_hj' => 1234, 'id_ssii' => 666 ]
     *  result = [ 12 => 987, 1234 => 666 ]
     *
     * @param  array $list
     * @param  array $item SSII data as follows: [ 'id_hj' => int, 'id_ssii' => int ]
     * @return array
     */
    private function addFormattedSsiiData(array $list, array $item): array
    {
        $list[$item['id_hj']] = $item['id_ssii'];

        return $list;
    }
}
