<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "cannot delete the file" Error.
 */
final class CannotDeleteFile implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-3';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'cannot delete the file';
    }
}
