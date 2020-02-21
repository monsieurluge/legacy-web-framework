<?php

namespace App\Services\Validator\Rules;

use App\ServiceInterfaces\Validator\ValidationResultInterface;
use App\ServiceInterfaces\Validator\Rules\RuleInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * URI Pattern Rule
 */
final class URIPattern implements RuleInterface
{

    /** @var string **/
    private $pattern;

    /**
     * @codeCoverageIgnore
     * @param string $pattern
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * Checks the request's URI, including the parameters
     * @param Request                   $target
     * @param ValidationResultInterface $result
     * @return RuleInterface
     */
    public function apply($target, ValidationResultInterface $result): RuleInterface
    {
        if (false === $this->matchesPattern($target)) {
            $result->addError(sprintf(
                'TODO ERROR MESSAGE'
            ));
        }

        return $this;
    }

    /**
     * Compares the request's URI with the pattern
     * @codeCoverageIgnore
     * @param Request $request
     * @return bool
     */
    private function matchesPattern(Request $request): bool
    {
        $patternElements = explode('/', $this->pattern);
        $pathElements    = explode('/', $request->getPathInfo());
        $elementsNb      = count($patternElements);

        if ($elementsNb !== count($pathElements)) {
            return false;
        }

        $results = array_map(
            function($patternElement, $pathElement) {
                return $this->isPatternParameter($patternElement)
                    ? (false === empty($pathElement)) // a parameter must not be empty
                    : $patternElement === $pathElement;
            },
            $patternElements,
            $pathElements
        );

        return !in_array(false, $results, true);
    }

    /**
     * Check if the pattern element is a parameter (ex: "{id}")
     *
     * @param  string $element
     * @return bool
     */
    private function isPatternParameter(string $element): bool
    {
        return 1 === preg_match('#{([a-zA-Z0-9-_]+)}#', $element);
    }

}
