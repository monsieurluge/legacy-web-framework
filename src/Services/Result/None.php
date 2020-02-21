<?php

namespace App\Services\Result;

use Closure;
use App\Services\Result\Option;

final class None implements Option
{
    /**
    * @inheritDoc
    */
    public function getOrCall(Closure $expression)
    {
        return $expression();
    }

    /**
    * @inheritDoc
    */
    public function getContentOrDefaultOnFailure($default)
    {
        return $default;
    }

    /**
     * @inheritDoc
     */
    public function map(Closure $expression): Option
    {
        return $this;
    }
}
