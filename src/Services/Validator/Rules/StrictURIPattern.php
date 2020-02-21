<?php

namespace App\Services\Validator\Rules;

use App\ServiceInterfaces\Validator\ValidationResultInterface;
use App\ServiceInterfaces\Validator\Rules\RuleInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Strict URI Pattern Rule
 */
final class StrictURIPattern implements RuleInterface
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
     * Checks strictly the request's URI
     * @param Request                   $target
     * @param ValidationResultInterface $result
     * @return RuleInterface
     */
    public function apply($target, ValidationResultInterface $result): RuleInterface
    {
        if ($this->pattern !== $target->getPathInfo()) {
            $result->addError(sprintf(
                'the request\'s URI "%s" does not match the expected pattern "%s"',
                $target->getPathInfo(),
                $this->pattern
            ));
        }

        return $this;
    }

}
