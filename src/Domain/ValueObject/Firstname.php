<?php

namespace App\Domain\ValueObject;

final class Firstname implements ValueObject
{

    /** @var string **/
    private $value;

    public function __construct(string $firstname)
    {
        $this->value = $firstname;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->value;
    }

}
