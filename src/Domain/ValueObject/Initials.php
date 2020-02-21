<?php

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\ValueObject;

final class Initials implements ValueObject
{
    /** @var string */
    private $initials;

    /**
     * @param string $initials
     */
    public function __construct(string $initials)
    {
        $this->initials = $initials;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return strtoupper($this->initials);
    }
}
