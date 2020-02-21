<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "user not found" Error.
 */
final class UserNotFound implements Error
{
    /** @var int */
    private $userId;

    /**
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-15';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return sprintf(
            'user #%s not found',
            $this->userId
        );
    }
}
