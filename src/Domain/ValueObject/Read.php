<?php

namespace App\Domain\ValueObject;

final class Read implements ValueObject
{

    /** @var bool **/
    private $value;

    public function __construct(bool $read)
    {
        $this->value = $read;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->value;
    }

}
