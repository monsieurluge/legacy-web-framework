<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "method not implemented" Error.
 */
final class NotImplemented implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-11';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'method not implemented';
    }
}
