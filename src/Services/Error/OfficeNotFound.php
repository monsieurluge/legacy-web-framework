<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "office not found" Error.
 */
final class OfficeNotFound implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-13';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'office not found';
    }
}
