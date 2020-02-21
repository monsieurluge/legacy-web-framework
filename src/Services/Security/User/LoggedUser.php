<?php

namespace App\Services\Security\User;

use App\Services\Security\User\AbstractUser;

final class LoggedUser extends AbstractUser
{

    /** @var int **/
    private $identifier;
    /** @var string **/
    private $firstname;
    /** @var string **/
    private $lastname;
    /** @var string **/
    private $initials;
    /** @var Role[] **/
    private $roles;

    /**
     * @param int    $identifier
     * @param string $firstname
     * @param string $lastname
     * @param string $initials
     * @param Role[] $roles
     */
    public function __construct(
        int $identifier,
        string $firstname,
        string $lastname,
        string $initials,
        array $roles = []
    ) {
        $this->firstname  = $firstname;
        $this->identifier = $identifier;
        $this->initials   = $initials;
        $this->lastname   = $lastname;
        $this->roles      = $roles;
    }

    /**
     * @inheritDoc
     */
    public function logged(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function roles(): array
    {
        return $this->roles;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->identifier,
            'firstname' => $this->firstname,
            'lastname'  => $this->lastname,
            'initials'  => $this->initials,
            'roles'     => print_r($this->roles, true)
        ];
    }

}
