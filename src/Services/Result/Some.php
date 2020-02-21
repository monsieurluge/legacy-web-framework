<?php

namespace App\Services\Result;

use Closure;
use App\Services\Result\Option;

final class Some implements Option
{
    /** @var mixed */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function getOrCall(Closure $expression)
    {
        return $this->value;
    }

    /**
    * @inheritDoc
    */
    public function getContentOrDefaultOnFailure($default)
    {
        return $this->value;
    }

    /**
    * @inheritDoc
    */
    public function map(Closure $expression): Option
    {
        return new self($expression($this->value));
    }
}
