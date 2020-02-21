<?php

namespace App\Domain\ValueObject;

use App\Domain\ValueObject\ValueObject;

final class Title implements ValueObject
{

    /** @var string **/
    private $value;

    public function __construct(string $title)
    {
        $this->value = $title;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->value;
    }

}
