<?php

namespace App\Domain\ValueObject;

final class Login implements ValueObject
{

    /** @var string **/
    private $value;

    public function __construct(string $login)
    {
        $this->value = $login;
    }

    /**
     * @inheritDoc
     */
    public function value()
    {
        return $this->value;
    }

}
