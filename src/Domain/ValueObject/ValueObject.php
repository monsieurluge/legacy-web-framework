<?php

namespace App\Domain\ValueObject;

/**
 * Value Object interface
 */
interface ValueObject
{

    /**
     * Returns the value.
     * @return mixed
     */
    public function value();

}
