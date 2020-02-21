<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "cannot update the note" Error.
 */
final class CannotUpdateNote implements Error
{
    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-8';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return 'cannot update the note';
    }
}
