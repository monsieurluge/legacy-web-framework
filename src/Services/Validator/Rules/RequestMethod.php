<?php

namespace App\Services\Validator\Rules;

use App\ServiceInterfaces\Requests\MethodInterface;
use App\ServiceInterfaces\Validator\ValidationResultInterface;
use App\ServiceInterfaces\Validator\Rules\RuleInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Request's Method Rule
 */
final class RequestMethod implements RuleInterface
{

    /** @var MethodInterface **/
    private $method;

    /**
     * @codeCoverageIgnore
     * @param MethodInterface $method
     */
    public function __construct(MethodInterface $method)
    {
        $this->method = $method;
    }

    /**
     * Checks the request's method
     * @param Request                   $target
     * @param ValidationResultInterface $result
     * @return RuleInterface
     */
    public function apply($target, ValidationResultInterface $result): RuleInterface
    {
        if (false === $this->method->matches($target)) {
            $result->addError(sprintf(
                'the request\'s method "%s" does not match the expected one',
                $target->getMethod()
            ));
        }

        return $this;
    }

}
