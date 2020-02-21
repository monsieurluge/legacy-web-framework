<?php

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\Seconds;

/**
 * Represents a number of seconds (like a timestamp).
 */
final class BaseSeconds extends Seconds
{
    /** @var int */
    private $seconds;

    /**
     * @param int $seconds
     */
    public function __construct(int $seconds = 0)
    {
        $this->seconds = $seconds;
    }

    /**
     * @inheritDoc
     */
    protected function seconds(): int
    {
        return $this->seconds;
    }
}
