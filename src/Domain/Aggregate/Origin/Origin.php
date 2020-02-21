<?php

namespace App\Domain\Aggregate\Origin;

use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;

/**
 * Origin aggregate.
 */
interface Origin
{
    /**
     * Returns the ID.
     *
     * @return ID
     */
    public function identifier(): ID;

    /**
     * Returns the label.
     *
     * @return Label
     */
    public function label(): Label;
}
