<?php

namespace App\Services\Date;

use App\Services\Date\Timestamp;

final class FakeTimestamp implements Timestamp
{

    /** @var int **/
    private $value;

    /**
     * @param int $fixedValue
     */
    public function __construct(int $fixedValue)
    {
        $this->value = $fixedValue;
    }

    /**
     * @inheritDoc
     */
    public function value(): int
    {
        return $this->value;
    }

}
