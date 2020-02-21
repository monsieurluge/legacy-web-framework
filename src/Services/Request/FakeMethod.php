<?php

namespace App\Services\Request;

use App\ServiceInterfaces\Requests\MethodInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Fake Method, for test purpose only
 * @codeCoverageIgnore
 */
final class FakeMethod implements MethodInterface
{

    /** @var string **/
    private $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function matches(Request $request): bool
    {
        return $this->name === $request->getMethod();
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return $this->name;
    }

}
