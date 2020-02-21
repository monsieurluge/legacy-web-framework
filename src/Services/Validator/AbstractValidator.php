<?php

namespace App\Services\Validator;

use App\ServiceInterfaces\Validator\ValidatorInterface;
use App\ServiceInterfaces\Validator\ValidationResultInterface;

/**
 * Abstract Validator
 * @codeCoverageIgnore
 */
abstract class AbstractValidator implements ValidatorInterface
{

    /**
     * Returns the validation rules
     * @return array
     */
    abstract protected function rules(): array;

    /**
     * @inheritDoc
     */
    public function validate($target, ValidationResultInterface $result): ValidatorInterface
    {
        $rules = $this->rules();

        foreach ($rules as $rule) {
            $rule->apply($target, $result);
        }

        return $this;
    }

}
