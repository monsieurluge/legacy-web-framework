<?php

namespace App\Services\Security\User;

use App\Services\Security\User\User;
use App\Services\Security\Role\Role;

abstract class AbstractUser implements User
{

    /**
     * @inheritDoc
     */
    public function hasRole(Role $role): bool
    {
        return in_array($role, $this->roles());
    }

    /**
     * Returns the user's roles
     *
     * @return Role[]
     */
    abstract protected function roles(): array;

}
