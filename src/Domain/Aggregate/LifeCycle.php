<?php

namespace App\Domain\Aggregate;

use DateTime;
use monsieurluge\optional\Optional\Maybe;
use App\Domain\ValueObject\Seconds;

/**
 * Object lifecycle
 */
final class LifeCycle
{
    /** @var Maybe */
    private $end;
    /** @var Seconds */
    private $pauseTime;
    /** @var Seconds */
    private $readTime;
    /** @var DateTime */
    private $start;

    /**
     * @param DateTime $start
     * @param Maybe    $end
     * @param Seconds  $readTime
     * @param Seconds  $pauseTime
     */
    public function __construct(DateTime $start, Maybe $end, Seconds $readTime, Seconds $pauseTime)
    {
        $this->end       = $end;
        $this->pauseTime = $pauseTime;
        $this->readTime  = $readTime;
        $this->start     = $start;
    }

    /**
     * Returns the "maybe" end date.
     *
     * @return Maybe an optional DateTime
     */
    public function ended(): Maybe
    {
        return $this->end;
    }

    /**
     * Returns the total "pause time", in seconds.
     *
     * @return Seconds
     */
    public function pauseTime(): Seconds
    {
        return $this->pauseTime;
    }

    /**
     * Returns the total "read time", in seconds.
     *
     * @return Seconds
     */
    public function readTime(): Seconds
    {
        return $this->readTime;
    }

    /**
     * Returns the start date.
     *
     * @return DateTime
     */
    public function started(): DateTime
    {
        return $this->start;
    }
}
