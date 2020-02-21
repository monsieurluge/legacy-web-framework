<?php

namespace App\Services\Security;

use monsieurluge\result\Result\Result;
use App\Application\Command\Action\UpdateUserSession;
use App\Application\Query\User as UserDTO;
use App\Repository\User as UserRepository;
use App\Services\Security\RoleFactory;
use App\Services\Security\Session;
use App\Services\Security\User\User;
use App\Services\Security\User\Guest;
use App\Services\Security\User\LoggedUser;

/**
 * User Factory
 */
final class UserFactory
{
    /** @var UserRepository **/
    private $repository;
    /** @var RoleFactory **/
    private $roleFactory;
    /** @var Session **/
    private $session;

    /**
     * @param UserRepository $repository
     * @param Session        $session
     * @param RoleFactory    $roleFactory
     */
    public function __construct(UserRepository $repository, Session $session, RoleFactory $roleFactory)
    {
        $this->repository  = $repository;
        $this->roleFactory = $roleFactory;
        $this->session     = $session;
    }

    /**
     * Creates the user from the HTTP session informations
     *
     * @return User
     */
    public function createFromSession(): User
    {
        return $this->session
            ->retrieve('user')
            ->then(function($identifier) {
                return $this->repository->findById($identifier->value);
            })
            ->map(function(UserDTO $user) {
                return new LoggedUser(
                    $user->identifier()->value(),
                    $user->lastname()->value(),
                    $user->firstname()->value(),
                    $user->initials(),
                    [ $this->roleFactory->createFromValueObject($user->role()) ]
                );
            })
            ->getValueOrExecOnFailure(function() {
                return new Guest();
            });
    }

    /**
     * Creates the user from its ID
     *
     * @param string $login
     * @param string $password
     *
     * @return Result Result<User>
     */
    public function createFromLogin(string $login, string $password): Result
    {
        return $this->repository
            ->findByLoginPassword($login, $password) // returns a Result<UserDTO>
            ->then(function ($target) {
                return (new UpdateUserSession($this->session))
                    ->handle($target) // returns a Result<UserDTO>
                    ->map(function(UserDTO $user) {
                        return new LoggedUser(
                            $user->identifier()->value(),
                            $user->lastname()->value(),
                            $user->firstname()->value(),
                            $user->initials(),
                            [ $this->roleFactory->createFromValueObject($user->role()) ]
                        );
                    });
            });
    }
}
