<?php

namespace App\Domain\ValueObject;

use Closure;
use App\Domain\ValueObject\Seconds;

/**
 * Represents a number of seconds (like a timestamp).
 */
final class SecondsFromCallback extends Seconds
{
    /** @var Closure */
    private $callback;
    /** @var int */
    private $seconds;

    /**
     * @param Closure $callback
     */
    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
        $this->seconds  = null;
    }

    /**
     * @inheritDoc
     */
    protected function seconds(): int
    {
        if (false === is_null($this->seconds)) {
            return $this->seconds;
        }

        $this->seconds = ($this->callback)();

        return $this->seconds();
    }
}
