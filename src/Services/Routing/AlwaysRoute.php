<?php

namespace App\Services\Routing;

use Closure;
use monsieurluge\result\Result\Result;
use monsieurluge\result\Result\Success;
use App\Services\Routing\Route;
use App\Services\Request\QueryString;
use App\Services\Request\NoPathParameters;
use App\Services\Security\User\User;
use Symfony\Component\HttpFoundation\Request;

/**
 * A Route which always matches the request
 */
final class AlwaysRoute implements Route
{
    /** @var [type] **/
    private $controller;
    /** @var string **/
    private $customRequest;

    /**
     * @codeCoverageIgnore
     * @param [type] $controller
     */
    public function __construct($controller)
    {
        $this->controller    = $controller;
        $this->customRequest = null;
    }

    /**
     * @inheritDoc
     */
    public function canHandle(Request $request): bool
    {
        // this route always return true
        return true;
    }

    /**
     * @inheritDoc
     */
    public function expects(array $constraints): Route
    {
        // this route do not take constraints
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function for(Closure $controllerFactory): Route
    {
        $this->controller = ($controllerFactory)();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function handle(Request $request, User $user): Result
    {
        // this route always process the request
        return is_null($this->customRequest)
            ? $this->controller->process(new QueryString($request), new NoPathParameters(), $request->getContent(), $user)
            : $this->controller->process($this->customRequest($request));
    }

    /**
     * @inheritDoc
     */
    public function using(string $customRequest): Route
    {
        $this->customRequest = function ($request) use ($customRequest) { return new $customRequest($request); };

        return $this;
    }
}
