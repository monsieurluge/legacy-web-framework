<?php

namespace App\Services\Security\Role;

use App\Services\Security\Role\Role;

final class Admin implements Role
{

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'ADMIN';
    }

}
