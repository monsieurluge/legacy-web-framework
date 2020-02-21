<?php

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\ValueObject;

final class Type implements ValueObject
{

    /** @var string **/
    private $value;

    public function __construct(string $type)
    {
        $this->value = $type;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->value;
    }

}
