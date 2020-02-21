<?php

namespace App\Services\Result;

use Closure;

interface Option
{
    /**
     * Returns the value or the default one
     *
     * @param  [type] $default
     * @return [type]
     */
    public function getContentOrDefaultOnFailure($default);

    /**
     * [map description]
     *
     * @param  Closure $expression
     * @return [type]
     */
    public function getOrCall(Closure $expression);

    /**
     * [map description]
     *
     * @param  Closure $expression
     * @return Option
     */
    public function map(Closure $expression): Option;
}
