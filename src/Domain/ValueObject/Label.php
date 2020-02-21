<?php

namespace App\Domain\ValueObject;

final class Label implements ValueObject
{

    /** @var string **/
    private $value;

    public function __construct(string $label)
    {
        $this->value = $label;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->value;
    }

}
