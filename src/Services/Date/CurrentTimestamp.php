<?php

namespace App\Services\Date;

use DateTime;
use App\Services\Date\Timestamp;

final class CurrentTimestamp implements Timestamp
{

    /**
     * @inheritDoc
     */
    public function value(): int
    {
        return (new DateTime())->getTimestamp();
    }

}
