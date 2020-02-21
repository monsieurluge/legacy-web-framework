<?php

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\ValueObject;

/**
 * Represents a number of seconds (like a timestamp).
 */
abstract class Seconds implements ValueObject
{
    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->seconds();
    }

    /**
     * Returns the seconds.
     *
     * @return int
     */
    abstract protected function seconds(): int;
}
