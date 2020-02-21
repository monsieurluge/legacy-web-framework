<?php

namespace App\Services\Error;

use monsieurluge\result\Error\Error;

/**
 * "no value failure" Error.
 */
final class NoValueFailure implements Error
{
    /** @var string */
    private $message;

    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @inheritDoc
     */
    public function code(): string
    {
        return 'api-12';
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return $this->message;
    }
}
