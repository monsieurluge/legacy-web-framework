<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "wrong login informations" Error.
 */
final class WrongLoginInformations implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-17';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'cannot log the user using the provided login informations';
    }
}
