<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "cannot fetch the e-mail" Error.
 */
final class CannotFetchEmail implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-5';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'cannot fetch the e-mail';
    }
}
