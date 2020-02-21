<?php

namespace App\Services\Security\User;

use App\Services\Security\Role\Role;

interface User
{

    /**
     * Is the user logged ?
     *
     * @return bool
     */
    public function logged(): bool;

    /**
     * Is this role part of the user's ones ?
     *
     * @param  Role $role
     * @return bool
     */
    public function hasRole(Role $role): bool;

    /**
     * Returns the user's informations
     *
     * @return array as follows: [ id, firstname, lastname, initials, roles ]
     */
    public function toArray(): array;

}
