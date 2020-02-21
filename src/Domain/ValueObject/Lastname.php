<?php

namespace App\Domain\ValueObject;

final class Lastname implements ValueObject
{

    /** @var string **/
    private $value;

    public function __construct(string $lastname)
    {
        $this->value = $lastname;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->value;
    }

}
