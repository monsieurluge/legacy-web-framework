<?php

namespace App\Repository;

use PDO;
use monsieurluge\result\Result\Result;
use App\Services\Database\Database;
use App\Services\Error\CannotFetchEmail;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Success;

/**
 * Emails repository.
 */
final class Emails
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
     * Returns an e-mail
     *
     * @param int $emailId
     *
     * @return Result
     */
    public function findById(int $emailId): Result
    {
        $query = 'SELECT * FROM email WHERE id = :id';

        $email = $this->dataSource
            ->query($query, PDO::FETCH_ASSOC, [ 'id' => $emailId ]);

        return false === $email
            ? new Failure(
                new CannotFetchEmail(),
                'récupération impossible - raison inconnue'
            )
            : new Success((object) $email[0]);
    }

}
