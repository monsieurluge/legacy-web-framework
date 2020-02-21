<?php

namespace App\Application\Command\Action;

use monsieurluge\result\Result\Result;

/**
 * Action interface
 */
interface Action
{
    /**
     * Returns the result of the action applied to the given target
     *
     * @param mixed $target
     *
     * @return Result
     */
    public function handle($target): Result;
}
