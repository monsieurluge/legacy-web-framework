<?php

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\ValueObject;

final class Status implements ValueObject
{

    /** @var string **/
    private $value;

    public function __construct(string $status)
    {
        $this->value = $status;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->value;
    }

}
