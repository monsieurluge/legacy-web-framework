<?php

namespace App\Services\Validator;

use App\ServiceInterfaces\Requests\MethodInterface;
use App\ServiceInterfaces\Requests\PatternInterface;
use App\Services\Validator\Rules\RequestMethod;
use App\Services\Validator\Rules\StrictURIPattern;

/**
 * Simple Request Validator
 */
final class SimpleRequestValidator extends AbstractValidator
{

    /** @var PatternInterface **/
    private $pattern;
    /** @var MethodInterface **/
    private $method;

    /**
     * @codeCoverageIgnore
     * @param PatternInterface $pattern
     * @param MethodInterface  $method
     */
    public function __construct(PatternInterface $pattern, MethodInterface $method)
    {
        $this->pattern = $pattern;
        $this->method  = $method;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    protected function rules(): array
    {
        return [
            new StrictURIPattern($this->pattern->pattern()),
            new RequestMethod($this->method)
        ];
    }

}
