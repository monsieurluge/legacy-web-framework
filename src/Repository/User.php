<?php

namespace App\Repository;

use PDO;
use monsieurluge\result\Result\Failure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Application\Query\User as UserDTO;
use App\Domain\ValueObject\EmailAddress;
use App\Domain\ValueObject\Firstname;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Lastname;
use App\Domain\ValueObject\Login;
use App\Domain\ValueObject\UserRole as Role;
use App\Services\Database\Database;
use App\Services\Error\UserNotFound;
use App\Services\Error\WrongLoginInformations;

/**
 * User repository.
 */
final class User
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
     * Returns the user identified by the given login and password
     *
     * @param string $login
     * @param string $password
     *
     * @return Result a Result<UserDTO>
     */
    public function findByLoginPassword(string $login, string $password): Result
    {
        $result = $this->dataSource
            ->query(
                'SELECT * FROM utilisateur WHERE login=:login AND password=:password',
                PDO::FETCH_ASSOC,
                [ 'login' => $login, 'password' => $password ]
            );

        return empty($result)
            ? new Failure(
                new WrongLoginInformations()
            )
            : new Success(
                new UserDTO(
                    new ID($result[0]['id']),
                    new Firstname($result[0]['prenom']),
                    new Lastname($result[0]['nom']),
                    new EmailAddress($result[0]['email']),
                    new Login($result[0]['login']),
                    new Role($result[0]['type'])
                )
            );
    }

    /**
     * Returns the user's data
     *
     * @param int $identifier
     *
     * @return Result a Result<UserDTO>
     */
    public function findById(int $identifier): Result
    {
        $result = $this->dataSource
            ->query(
                'SELECT * FROM utilisateur WHERE id = :identifier',
                PDO::FETCH_ASSOC,
                [ 'identifier' => $identifier ]
            );

        return empty($result)
            ? new Failure(
                new UserNotFound($identifier)
            )
            : new Success(
                new UserDTO(
                    new ID($result[0]['id']),
                    new Firstname($result[0]['prenom']),
                    new Lastname($result[0]['nom']),
                    new EmailAddress($result[0]['email']),
                    new Login($result[0]['login']),
                    new Role($result[0]['type'])
                )
            );
    }
}
