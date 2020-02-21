<?php

namespace App\Services\Date;

interface Timestamp
{

    /**
     * Returns the timestamp value.
     *
     * @return int
     */
    public function value(): int;

}
