<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "no issue found" Error.
 */
final class NoIssueFound implements Error
{
    /** @var int */
    private $issueId;

    /**
     * @param int $issueId
     */
    public function __construct(int $issueId)
    {
        $this->issueId = $issueId;
    }

    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-10';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return sprintf(
            'the issue #%s was not found',
            $this->issueId
        );
    }
}
