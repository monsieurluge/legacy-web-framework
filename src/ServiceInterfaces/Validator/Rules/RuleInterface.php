<?php

namespace App\ServiceInterfaces\Validator\Rules;

use App\ServiceInterfaces\Validator\ValidationResultInterface;

/**
 * Rule Interface
 */
interface RuleInterface
{

    /**
     * Apply the rule on the target and dispatch the result
     * @param mixed                     $target
     * @param ValidationResultInterface $result
     * @return RuleInterface
     */
    public function apply($target, ValidationResultInterface $result): RuleInterface;

}
