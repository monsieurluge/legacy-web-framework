<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "cannot create the issue" Error.
 */
final class CannotCreateIssue implements Error
{
    /** @var string */
    private $reason;

    /**
     * @param string $reason
     */
    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-1';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return sprintf(
            'cannot create the issue: %s',
            $this->reason
        );
    }
}
