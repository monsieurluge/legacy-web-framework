<?php

namespace App\Application\Query;

use App\Domain\ValueObject\EmailAddress;
use App\Domain\ValueObject\Firstname;
use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Lastname;
use App\Domain\ValueObject\Login;
use App\Domain\ValueObject\UserRole as Role;

final class User
{

    /** @var EmailAddress **/
    private $email;
    /** @var Firstname **/
    private $firstname;
    /** @var ID **/
    private $identifier;
    /** @var Lastname **/
    private $lastname;
    /** @var Login **/
    private $login;
    /** @var Role **/
    private $role;

    /**
     * @param ID        $identifier
     * @param Firstname $firstname
     * @param Lastname  $lastname
     * @param EmailAddress     $email
     * @param Login     $login
     * @param Role      $role
     */
    public function __construct(
        ID $identifier,
        Firstname $firstname,
        Lastname $lastname,
        EmailAddress $email,
        Login $login,
        Role $role
    ) {
        $this->email      = $email;
        $this->firstname  = $firstname;
        $this->identifier = $identifier;
        $this->lastname   = $lastname;
        $this->login      = $login;
        $this->role       = $role;
    }

    /**
     * Returns the EmailAddress
     *
     * @return EmailAddress
     */
    public function email(): EmailAddress
    {
        return $this->email;
    }

    /**
     * Returns the Firstname
     *
     * @return Firstname
     */
    public function firstname(): Firstname
    {
        return $this->firstname;
    }

    /**
     * Returns the ID
     *
     * @return ID
     */
    public function identifier(): ID
    {
        return $this->identifier;
    }

    /**
     * Returns the initials
     *
     * @return string
     */
    public function initials(): string
    {
        return strtoupper(
            sprintf(
                '%s%s',
                mb_substr($this->lastname->value(), 0, 1, 'UTF-8'),
                mb_substr($this->firstname->value(), 0, 1, 'UTF-8')
            )
        );
    }

    /**
     * Returns the Lastname
     *
     * @return Lastname
     */
    public function lastname(): Lastname
    {
        return $this->lastname;
    }

    /**
     * Returns the Login name
     *
     * @return Login
     */
    public function login(): Login
    {
        return $this->login;
    }

    /**
     * Returns the Role
     *
     * @return Role
     */
    public function role(): Role
    {
        return $this->role;
    }

}
