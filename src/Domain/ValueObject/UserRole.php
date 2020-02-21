<?php

namespace App\Domain\ValueObject;

use Exception;

final class UserRole implements ValueObject
{

    /** @var string **/
    private $value;

    public function __construct(string $role)
    {
        $this->checkRole($role);

        $this->value = $role;
    }

    /**
     * Checks if the given role is valid
     *
     * @param  string $role
     * @throws Exception if the role is invalid
     */
    private function checkRole(string $role)
    {
        $validRoles = [ 'ADMIN', 'DEV', 'USER', 'SUPPORT' ];

        if (false === in_array($role, $validRoles)) {
            throw new Exception(sprintf(
                'the user\'s role must be one of these : [ %s ]',
                implode(', ', $validRoles)
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->value;
    }

}
