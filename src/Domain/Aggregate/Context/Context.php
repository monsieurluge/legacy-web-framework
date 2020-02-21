<?php

namespace App\Domain\Aggregate\Context;

use App\Domain\ValueObject\ID;
use App\Domain\ValueObject\Label;

/**
 * Context aggregate.
 */
interface Context
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
