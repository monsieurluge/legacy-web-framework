<?php

namespace App\Services\Security\User;

use Closure;
use App\Services\Security\User\User;
use App\Services\Security\Role\Role;

final class UserFromFactory implements User
{

    /** @var null/User **/
    private $user;
    /** @var Closure **/
    private $factory;

    public function __construct(Closure $factory)
    {
        $this->user    = null;
        $this->factory = $factory;
    }

    /**
     * @inheritDoc
     */
    public function logged(): bool
    {
        return $this->cachedUser()->logged();
    }

    /**
     * @inheritDoc
     */
    public function hasRole(Role $role): bool
    {
        return $this->cachedUser()->hasRole($role);
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->cachedUser()->toArray();
    }

    /**
     * Calls the factory if necessary and returns the User
     *
     * @return User
     */
    private function cachedUser(): User
    {
        if (false === is_null($this->user)) {
            return $this->user;
        }

        $this->user = ($this->factory)();

        return $this->cachedUser();
    }

}
