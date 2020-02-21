<?php

namespace App\Services\Security;

use App\Domain\ValueObject\UserRole;
use App\Services\Security\Role\Admin;
use App\Services\Security\Role\Developer;
use App\Services\Security\Role\Guest;
use App\Services\Security\Role\Role;
use App\Services\Security\Role\Support;

final class RoleFactory
{

    /**
     * Returns the role. Guest by default.
     *
     * @param  UserRole $role
     * @return Role
     */
    public function createFromValueObject(UserRole $role): Role
    {
        switch ($role->value()) {
            case 'ADMIN':
                return new Admin();
            case 'DEV':
                return new Developer();
            case 'SUPPORT':
            case 'USER':
                return new Support();
            default:
                return new Guest();
        }
    }

}
