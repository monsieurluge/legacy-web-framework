<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "SSII not found" Error.
 */
final class SSIINotFound implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-14';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'SSII not found';
    }
}
