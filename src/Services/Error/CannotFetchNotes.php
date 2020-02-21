<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "cannot fetch the notes" Error.
 */
final class CannotFetchNotes implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-6';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'cannot fetch the notes';
    }
}
