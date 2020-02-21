<?php

namespace App\Services\Result;

use Closure;
use monsieurluge\result\Result\Result;
use App\Application\Command\Action\Action;

/**
 * Custom action.
 */
final class CustomAction implements Action
{
    /** @var Closure **/
    private $expression;

    /**
     * @param Closure $expression a closure like follows: T -> Result<T>
     */
    public function __construct(Closure $expression)
    {
        $this->expression = $expression;
    }

    /**
     * @inheritDoc
     */
    public function handle($target): Result
    {
        return ($this->expression)($target);
    }
}
