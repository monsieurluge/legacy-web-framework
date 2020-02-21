<?php

namespace App\Domain\ValueObject;

final class Office implements ValueObject
{

    /** @var string **/
    private $value;

    public function __construct(string $office)
    {
        $this->value = $office;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return str_pad($this->value, 4, '0', STR_PAD_LEFT);
    }
}
