<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "file not found" Error.
 */
final class FileNotFound implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-9';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'file not found';
    }
}
