<?php

namespace App\ServiceInterfaces\Validator;

use App\ServiceInterfaces\Validator\ValidationResultInterface;

/**
 * Validator Interface
 * @codeCoverageIgnore
 */
interface ValidatorInterface
{

    /**
     * Validates the target
     * @param  mixed                     $target
     * @param  ValidationResultInterface $result
     * @return ValidatorInterface
     */
    public function validate($target, ValidationResultInterface $result): ValidatorInterface;

}
