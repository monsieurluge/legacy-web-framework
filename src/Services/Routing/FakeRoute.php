<?php

namespace App\Services\Routing;

use Closure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Services\Routing\Route;
use App\Services\Security\User\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * Fake Route, for test purpose only
 * @codeCoverageIgnore
 */
final class FakeRoute implements Route
{
    /**
     * @inheritDoc
     */
    public function canHandle(Request $request): bool
    {
        return true;
    }

    /**
    * @inheritDoc
    */
    public function expects(array $constraints): Route
    {
        return $this;
    }

    /**
    * @inheritDoc
    */
    public function for(Closure $controllerFactory): Route
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, User $user): Result
    {
        return new Success($this);
    }

    /**
    * @inheritDoc
    */
    public function using(string $customRequest): Route
    {
        return $this;
    }
}
