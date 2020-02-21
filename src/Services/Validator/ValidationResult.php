<?php

namespace App\Services\Validator;

use App\ServiceInterfaces\Validator\ValidationResultInterface;

/**
 * Validation Result
 */
final class ValidationResult implements ValidationResultInterface
{

    /** @var array **/
    private $errors;

    /**
     * @codeCoverageIgnore
     * @param array $errors
     */
    public function __construct($errors = [])
    {
        $this->errors = $errors;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function addError(string $message): ValidationResultInterface
    {
        $this->errors[] = $message;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }

    /**
    * @inheritDoc
    */
    public function errors(): array
    {
        return $this->errors;
    }

}
