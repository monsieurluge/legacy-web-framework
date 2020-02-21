<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "cannot create a note" Error.
 */
final class CannotCreateNote implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-2';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'cannot create a note';
    }
}
