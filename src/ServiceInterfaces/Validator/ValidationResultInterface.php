<?php

namespace App\ServiceInterfaces\Validator;

/**
 * Validation Result Interface
 * @codeCoverageIgnore
 */
interface ValidationResultInterface
{

    /**
     * Adds an error to the pool
     * @param string $message
     * @return ValidationResultInterface
     */
    public function addError(string $message): ValidationResultInterface;

    /**
     * Is the result valid ?
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Returns the validation errors
     * @return array
     */
    public function errors(): array;

}
