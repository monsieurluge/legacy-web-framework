<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "cannot delete the note" Error.
 */
final class CannotDeleteNote implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-4';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'cannot delete the note';
    }
}
