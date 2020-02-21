<?php

namespace App\Services\Validator;

use App\ServiceInterfaces\Requests\MethodInterface;
use App\ServiceInterfaces\Requests\PatternInterface;
use App\Services\Validator\Rules\URIPattern;
use App\Services\Validator\Rules\RequestMethod;

/**
 * Request Validator
 */
final class RequestValidator extends AbstractValidator
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
            new URIPattern($this->pattern->pattern()),
            new RequestMethod($this->method)
        ];
    }

}
