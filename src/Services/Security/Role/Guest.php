<?php

namespace App\Services\Security\Role;

use App\Services\Security\Role\Role;

final class Guest implements Role
{

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'GUEST';
    }

}
