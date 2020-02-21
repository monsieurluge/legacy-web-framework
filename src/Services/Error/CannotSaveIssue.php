<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "cannot save the issue data" Error.
 */
final class CannotSaveIssue implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-16';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'cannot save the issue data';
    }
}
