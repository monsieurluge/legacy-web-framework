<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "cannot update the issue" Error.
 */
final class CannotUpdateIssue implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-7';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'cannot update the issue';
    }
}
