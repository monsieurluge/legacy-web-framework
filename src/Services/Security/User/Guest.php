<?php

namespace App\Services\Security\User;

use App\Services\Security\User\AbstractUser;
use App\Services\Security\Role\Guest as GuestRole;
use App\Services\Security\Role\Role;

final class Guest extends AbstractUser
{

    /** @var Role[] **/
    private $roles;

    public function __construct()
    {
        $this->roles = [ new GuestRole() ];
    }

    /**
     * @inheritDoc
     */
    public function logged(): bool
    {
        return false;
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
            'id'        => 0,
            'firstname' => 'unknown',
            'lastname'  => 'unknown',
            'initials'  => 'N\\A',
            'roles'     => 'guest'
        ];
    }

}
