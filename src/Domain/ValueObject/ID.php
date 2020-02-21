<?php

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\ValueObject;

final class ID implements ValueObject
{

    /** @var int **/
    private $value;

    public function __construct(int $id)
    {
        $this->value = $id;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->value;
    }

}
